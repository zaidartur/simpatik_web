<?php

namespace App\Http\Controllers;

use App\Models\ArsipSurat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;

ini_set('memory_limit', '1024M');
// ini_set('max_execution_time', '300');
set_time_limit(0); //unlimited time limit
class LaporanController extends Controller
{
    public function __construct() {
        $this->middleware('permission:statistik', ['only' => ['statistik', 'statistik_ssr']]);
        $this->middleware('permission:tindak lanjut', ['only' => ['tindak_lanjut', 'tindak_lanjut_ssr']]);
        $this->middleware('permission:agenda', ['only' => ['agenda', 'agenda_ssr', 'agenda_print']]);
    }

    public function statistik()
    {
        $data = [
            'tahun' => ArsipSurat::select('TAHUN as tahun')->distinct()->orderBy('TAHUN', 'DESC')->get(),
            'last'  => ArsipSurat::select('TAHUN as tahun')->distinct()->orderBy('TAHUN', 'DESC')->first(),
        ];
        return view('main.laporan.statistik', $data);
    }

    public function tindak_lanjut()
    {
        return view('main.laporan.tindak_lanjut');
    }

    public function agenda()
    {
        $data = [
            'years' => ArsipSurat::select('TAHUN')->distinct()->orderBy('TAHUN', 'desc')->get(),
        ];
        return view('main.laporan.agenda', $data);
    }

    public function statistik_ssr()
    {
        $request = Request();
        $start = $request->start;
        $length = $request->length;
        $month = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $year = request()->has('tahun') ? request()->get('tahun') : date('Y');
        $lists = ArsipSurat::selectRaw('BULAN, 
            SUM(CASE WHEN JENISSURAT = "Masuk" THEN 1 ELSE 0 END) AS surat_masuk,
            SUM(CASE WHEN JENISSURAT = "Keluar" THEN 1 ELSE 0 END) AS surat_keluar,
            COUNT(*) AS total')
            ->where('TAHUN', $year)
            ->groupBy('BULAN')
            ->orderBy('BULAN', 'ASC');
        $query = $lists->get();

        $data = [];
        $masuk = 0; $keluar = 0; $total = 0;
        if (count($query) > 0) {
            foreach ($query as $row) {
                $data[] = [
                    'bulan'         => $month[intval($row->BULAN)],
                    'surat_masuk'   => number_format(intval($row->surat_masuk), 0, ',', '.'),
                    'surat_keluar'  => number_format(intval($row->surat_keluar), 0, ',', '.'),
                    'total'         => number_format(intval($row->total), 0, ',', '.'),
                ];
                $masuk += intval($row->surat_masuk);
                $keluar += intval($row->surat_keluar);
                $total += intval($row->total);
            }
        }

        $data[] = [
                'bulan'         => '<strong class="text-success">Tahun '. $year .'</strong>',
                'surat_masuk'   => '<strong class="text-success">' . number_format($masuk, 0, ',', '.') . '</strong>',
                'surat_keluar'  => '<strong class="text-success">' . number_format($keluar, 0, ',', '.') . '</strong>',
                'total'         => '<strong class="text-success">' . number_format($total, 0, ',', '.') . '</strong>',
        ];



        return response()->json([
            'draw' => intval($request->draw) ?? 0,
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data),
            'data' => $data,
        ]);
    }

    public function tindak_lanjut_ssr()
    {
        $request = Request();
        $user = $request->user();
        if ($user->hasAnyRole(['administrator', 'umum'])) {
            $start = $request->start;
            $length = $request->length;

            // $query = ArsipSurat::whereNotNull('DisposisiSekda')->orWhereNotNull('DisposisiSekda2')->orWhereNotNull('DisposisiBupati')->orWhereNotNull('DisposisiWakil')
            $query = ArsipSurat::where(function($q) {
                        return $q->whereNotNull('DisposisiSekda')->whereRaw('TRIM(DisposisiSekda) != ""');
                    })
                    ->orWhere(function($q) {
                        return $q->whereNotNull('DisposisiSekda2')->whereRaw('TRIM(DisposisiSekda2) != ""');
                    })
                    ->orWhere(function($q) {
                        return $q->whereNotNull('DisposisiBupati')->whereRaw('TRIM(DisposisiBupati) != ""');
                    })
                    ->orWhere(function($q) {
                        return $q->whereNotNull('DisposisiWakil')->whereRaw('TRIM(DisposisiWakil) != ""');
                    })
                    ->where('JENISSURAT', 'Masuk')
                    ->orderBy('NO', 'DESC');
            if (Auth::user()->hasRole('setda')) {
                $query->whereIn('Posisi', ['Sekeretaris Daerah', 'Bupati']);
            }
            if (Auth::user()->hasRole('wabup')) {
                $query->whereIn('Posisi', ['Sekeretaris Daerah', 'Wakil Bupati']);
            }
            if (Auth::user()->hasRole('bupati')) {
                $query->whereIn('Posisi', ['Bupati']);
            }

            $totalData = $query->count();

            // search query
            if ($request->has('search') && $request->search['value'] != '') {
                $search = $request->search['value'];
                $query->where(function ($q) use ($search) {
                    $q->where('NOSURAT', 'like', "%$search%")
                        ->orWhere('drkpd', 'like', "%$search%")
                        ->orWhere('PERIHAL', 'like', "%$search%")
                        ->orWhere('ISI', 'like', "%$search%")
                        ->orWhere('KLAS3', 'like', "%$search%");
                });
            }
            $totalFiltered = $query->count();
            $list = $query->skip($start)->take($length)->get();

            $data = [];
            foreach ($list as $row) {
                $data[] = [
                    'nomor'     => $row->NOSURAT,
                    'no_agenda' => $row->NOAGENDA,
                    'klasifikasi' => $row->SIFAT_SURAT,
                    'berkas'    => $row->NAMABERKAS,
                    'wilayah'   => $row->WILAYAH,
                    'isi_surat' => $row->ISI,
                    'tanggal'   => Carbon::parse($row->TGLSURAT)->isoFormat('DD-MM-YYYY'),
                    'kepada'    => $row->drkpd,
                    'perihal'   => $row->PERIHAL,
                    'kode'      => $row->KLAS3,
                    'tgl_buat'  => Carbon::parse($row->TGLENTRY)->isoFormat('DD-MM-YYYY'),
                    'posisi'    => $row->Posisi,
                    'class'     => ($row->Posisi == 'Sekretaris Daerah' ? 'badge-info' : ($row->Posisi == 'Wakil Bupati' ? 'badge-secondary' : ($row->Posisi == 'Bupati' ? 'badge-primary' : 'badge-dark'))),
                    'uid'       => Crypt::encryptString($row->NO),
                    'sekda'     => $row->DisposisiSekda,
                    'sekda2'    => $row->DisposisiSekda2,
                    'bupati'    => $row->DisposisiBupati,
                    'wakil'     => $row->DisposisiWakil,
                    'tsekda'    => empty($row->tglsekda1) ? null : Carbon::parse($row->tglsekda1)->isoFormat('DD-MM-YYYY'),
                    'tsekda2'   => empty($row->tglsekda2) ? null : Carbon::parse($row->tglsekda2)->isoFormat('DD-MM-YYYY'),
                    'tbupati'   => empty($row->tglbupati1) ? null : Carbon::parse($row->tglbupati1)->isoFormat('DD-MM-YYYY'),
                    'twakil'    => empty($row->tglwakil) ? null : Carbon::parse($row->tglwakil)->isoFormat('DD-MM-YYYY'),
                    'option'    => '',
                    'status'    => $row->statussurat == 'selesai' ? '<span class="badge badge-success mb-2 me-4">Selesai</span>' : '<span class="badge badge-secondary mb-2 me-4">Menunggu</span>',
                ];
            }

            return response()->json([
                'draw' => intval($request->draw) ?? 0,
                'recordsTotal' => $totalData,
                'recordsFiltered' => $totalFiltered,
                'data' => $data,
            ]);
        } else {
            return response()->json([
                'draw' => intval($request->draw) ?? 0,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ]);
        }
    }

    public function agenda_ssr()
    {
        $request = Request();
        $user = $request->user();
        if ($user->hasAnyRole(['administrator', 'umum', 'setda', 'wabup', 'bupati'])) {
            $start = $request->start;
            $length = $request->length;

            $query = ArsipSurat::where('TAHUN', (!empty($request->year) ? $request->year : date('Y')));

            if (isset($request->jenis) && in_array($request->jenis, ['Masuk', 'Keluar'])) {
                $query->where('JENISSURAT', $request->jenis);
            }
            // if (isset($request->bulan) && !empty($request->bulan) && (intval($request->bulan) > 0 && intval($request->bulan) < 13)) {
            //     $query->where('BULAN', intval($request->bulan));
            // }
            if (isset($request->start_date) && !empty($request->start_date) && isset($request->end_date) && !empty($request->end_date)) {
                $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
                $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
                $query->whereBetween('TGLENTRY', [$startDate, $endDate]);
            }

            $query->orderBy('noagenda2', 'ASC');

            $totalData = $query->count();

            // search query
            if ($request->has('search') && $request->search['value'] != '') {
                $search = $request->search['value'];
                $query->where(function ($q) use ($search) {
                    $q->where('NOSURAT', 'like', "%$search%")
                        ->orWhere('drkpd', 'like', "%$search%")
                        ->orWhere('PERIHAL', 'like', "%$search%")
                        ->orWhere('ISI', 'like', "%$search%")
                        ->orWhere('KLAS3', 'like', "%$search%");
                });
            }
            $totalFiltered = $query->count();
            $list = $query->skip($start)->take($length)->get();

            $data = [];
            foreach ($list as $l => $row) {
                $data[$l] = [
                    'no_agenda' => $row->NOAGENDA,
                    'kepada'    => $row->JENISSURAT == 'Masuk' ? $row->Posisi : $row->drkpd,
                    'tgl_buat'  => Carbon::parse($row->TGLENTRY)->isoFormat('DD-MM-YYYY'),
                    'tanggal'   => Carbon::parse($row->TGLSURAT)->isoFormat('DD-MM-YYYY'),
                    'nomor'     => $row->NOSURAT,
                    'row3'      => Carbon::parse($row->TGLENTRY)->isoFormat('DD-MM-YYYY'). '<br>' .Carbon::parse($row->TGLSURAT)->isoFormat('DD-MM-YYYY'). '<br>' .$row->NOSURAT,
                    'kode'      => $row->KLAS3,
                    'jra'       => $row->KETJRA,
                    'isi_surat' => $row->ISI,
                    'row4'      => $row->KLAS3. '<br><b>' .$row->KETJRA. '</b><br>' .$row->ISI,
                    // 'dari'      => $row->drkpd,
                    'berkas'    => $row->NAMABERKAS,
                    'wilayah'   => $row->WILAYAH,
                    'dari'      => $row->JENISSURAT == 'Keluar' ? $row->NAMAUP : $row->drkpd,
                    'perihal'   => $row->PERIHAL,
                    'class'     => ($row->Posisi == 'Sekretaris Daerah' ? 'badge-info' : ($row->Posisi == 'Wakil Bupati' ? 'badge-secondary' : ($row->Posisi == 'Bupati' ? 'badge-primary' : 'badge-dark'))),
                    'uid'       => Crypt::encryptString($row->NO),
                ];

                if ($user->hasAnyRole(['administrator', 'umum', 'setda'])) {
                    $data[$l] += [
                        'sekda'     => '<b><p style="width: 100%; text-align: right;">' .(empty($row->tglsekda1) ? null : Carbon::parse($row->tglsekda1)->isoFormat('DD-MM-YYYY HH:mm')). '</p></b><br>' .$row->DisposisiSekda,
                        'sekda2'    => '<b><p style="width: 100%; text-align: right;">' . (empty($row->tglsekda2) ? null : Carbon::parse($row->tglsekda2)->isoFormat('DD-MM-YYYY HH:mm')). '</p></b><br>' .$row->DisposisiSekda2,
                        'bupati'    => '<b><p style="width: 100%; text-align: right;">' .(empty($row->tglbupati1) ? null : Carbon::parse($row->tglbupati1)->isoFormat('DD-MM-YYYY HH:mm')). '</p></b><br>' .$row->DisposisiBupati,
                        'wakil'     => '<b><p style="width: 100%; text-align: right;">'. (empty($row->tglwakil) ? null : Carbon::parse($row->tglwakil)->isoFormat('DD-MM-YYYY HH:mm')). '</p></b><br>' .$row->DisposisiWakil,
                        'tsekda'    => empty($row->tglsekda1) ? null : Carbon::parse($row->tglsekda1)->isoFormat('DD-MM-YYYY HH:mm'),
                        'tsekda2'   => empty($row->tglsekda2) ? null : Carbon::parse($row->tglsekda2)->isoFormat('DD-MM-YYYY HH:mm'),
                        'tbupati'   => empty($row->tglbupati1) ? null : Carbon::parse($row->tglbupati1)->isoFormat('DD-MM-YYYY HH:mm'),
                        'twakil'    => empty($row->tglwakil) ? null : Carbon::parse($row->tglwakil)->isoFormat('DD-MM-YYYY HH:mm'),
                    ];
                }

                if ($user->hasAnyRole(['wabup'])) {
                    $data[$l] += [
                        'wakil'     => '<b><p style="width: 100%; text-align: right;">'. (empty($row->tglwakil) ? null : Carbon::parse($row->tglwakil)->isoFormat('DD-MM-YYYY HH:mm')). '</p></b><br>' .$row->DisposisiWakil,
                        'twakil'    => empty($row->tglwakil) ? null : Carbon::parse($row->tglwakil)->isoFormat('DD-MM-YYYY HH:mm'),
                    ];
                }

                if ($user->hasAnyRole(['bupati'])) {
                    $data[$l] += [
                        'bupati'    => '<b><p style="width: 100%; text-align: right;">' .(empty($row->tglbupati1) ? null : Carbon::parse($row->tglbupati1)->isoFormat('DD-MM-YYYY HH:mm')). '</p></b><br>' .$row->DisposisiBupati,
                        'tbupati'   => empty($row->tglbupati1) ? null : Carbon::parse($row->tglbupati1)->isoFormat('DD-MM-YYYY HH:mm'),
                    ];
                }
            }

            return response()->json([
                'draw' => intval($request->draw) ?? 0,
                'recordsTotal' => $totalData,
                'recordsFiltered' => $totalFiltered,
                'data' => $data,
            ]);
        } else {
            return response()->json([
                'draw' => intval($request->draw) ?? 0,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ]);
        }
    }

    public function agenda_print() 
    {
        $request = Request();
        $year    = !empty($request->tahun) ? $request->tahun : date('Y');
        $month   = $request->bulan;
        $type    = $request->jenis;

        $query   = ArsipSurat::where('TAHUN', $year);
        if (!empty($type) && in_array($type, ['Masuk', 'Keluar'])) {
            $query->where('JENISSURAT', $type);
        }
        // if (!empty($month) && (intval($month) > 0 && intval($month) < 13)) {
        //     $query->where('BULAN', (intval($month) < 10 ? '0'.$month : $month));
        // }
        if (isset($request->start_date) && !empty($request->start_date) && isset($request->end_date) && !empty($request->end_date)) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
            $query->whereBetween('TGLENTRY', [$startDate, $endDate]);
        } else {
            return '<script>alert("Rentang waktu harus diisi untuk mencetak laporan agenda."); window.close();</script>';
        }
        
        $res = $query->orderBy('noagenda2', 'ASC')->get();

        $pdf = $this->build_pdf($res, $type, $year, $month, $request->start_date, $request->end_date);
        return $pdf->stream('agenda_' .$year.$month. '.pdf');
    }

    public function build_pdf($agenda, $type, $tahun, $bulan, $start_date = null, $end_date = null)
    {
        $pdf = Pdf::setPaper('legal', 'landscape');
        $pdf->setOption([
            'dpi' => 96,
            'interpolate' => false,
            'isPhpEnabled' => false,
            'isRemoteEnabled' => false,
            'isHtml5ParserEnabled' => true,
        ]);
        // $pdf = Pdf::setPaper([0, 0, 792, 612], 'portrait');
        $data = [
            'data'  => $agenda,
            'jenis' => $type,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'range' => (!empty($start_date) && !empty($end_date) ? Carbon::createFromFormat('Y-m-d', $start_date)->isoFormat('DD MMMM YYYY') . ' s/d ' . Carbon::createFromFormat('Y-m-d', $end_date)->isoFormat('DD MMMM YYYY') : ''),
        ];
        $pdf->loadView('main.laporan.template_agenda', $data);
        return $pdf;
    }
}
