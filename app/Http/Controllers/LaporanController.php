<?php

namespace App\Http\Controllers;

use App\Exports\AgendaKeluarExport;
use App\Exports\AgendaMasukExport;
use App\Exports\StatistikExport;
use App\Models\ArsipSurat;
use App\Models\Inbox;
use App\Models\Outbox;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Permission;

class LaporanController extends Controller
{
    public function __construct() {
        $this->middleware('permission:statistik', ['only' => ['statistik', 'statistik_ssr']]);
        $this->middleware('permission:tindak lanjut', ['only' => ['tindak_lanjut', 'tindak_lanjut_ssr']]);
        $this->middleware('permission:agenda', ['only' => ['agenda', 'agenda_ssr', 'agenda_print']]);
    }

    public function statistik()
    {
        $yearsInbox = Inbox::whereNull('on_delete')->select('year')->distinct();
        $yearsOutbox = Outbox::whereNull('on_delete')->select('year')->distinct();
        $years = $yearsInbox->union($yearsOutbox)->orderBy('year', 'DESC')->pluck('year');

        $yearsCollection = $years->map(function ($y) {
            return (object)['tahun' => $y];
        });

        $lastYear = $yearsCollection->first() ?? (object)['tahun' => date('Y')];

        $data = [
            'tahun' => $yearsCollection,
            'last'  => $lastYear,
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
            'years' => Inbox::select('year')->distinct()->orderBy('year', 'desc')->get(),
        ];
        return view('main.laporan.agenda', $data);
    }

    public function statistik_ssr()
    {
        $request = Request();
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

        $inboxCounts = Inbox::whereNull('on_delete')
                ->where('year', $year)
                ->select(DB::raw('count(id) as count'), DB::raw("EXTRACT(MONTH FROM created_at)::integer as month"))
                ->groupBy('month')
                ->pluck('count', 'month')
                ->toArray();

        $outboxCounts = Outbox::whereNull('on_delete')
                ->where('year', $year)
                ->select(DB::raw('count(id) as count'), DB::raw("EXTRACT(MONTH FROM created_at)::integer as month"))
                ->groupBy('month')
                ->pluck('count', 'month')
                ->toArray();

        $data = [];
        $masuk = 0;
        $keluar = 0;
        $jml = 0;

        for ($i = 1; $i <= 12; $i++) { 
            $surat_masuk  = intval($inboxCounts[$i] ?? 0);
            $surat_keluar = intval($outboxCounts[$i] ?? 0);
            $total        = $surat_masuk + $surat_keluar;

            $data[] = [
                'bulan'         => $month[$i] ?? $i,
                'surat_masuk'   => number_format($surat_masuk, 0, ',', '.'),
                'surat_keluar'  => number_format($surat_keluar, 0, ',', '.'),
                'total'         => number_format($total, 0, ',', '.'),
            ];

            $masuk += $surat_masuk;
            $keluar += $surat_keluar;
            $jml += $total;
        }

        $data[] = [
            'bulan'         => '<strong class="text-success">Tahun '. $year .'</strong>',
            'surat_masuk'   => '<strong class="text-success">' . number_format($masuk, 0, ',', '.') . '</strong>',
            'surat_keluar'  => '<strong class="text-success">' . number_format($keluar, 0, ',', '.') . '</strong>',
            'total'         => '<strong class="text-success">' . number_format($jml, 0, ',', '.') . '</strong>',
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
        if ($user->hasAnyRole(['administrator', 'umum', 'setda', 'wabup', 'bupati', 'admin'])) {
            $start = $request->start ?? 0;
            $length = $request->length ?? 10;

            $query = ArsipSurat::where(function($q) {
                        $q->where(function($sub) {
                            $sub->whereNotNull('DisposisiSekda')->whereRaw("LENGTH(TRIM(COALESCE(\"DisposisiSekda\", ''))) > 0");
                        })
                        ->orWhere(function($sub) {
                            $sub->whereNotNull('DisposisiSekda2')->whereRaw("LENGTH(TRIM(COALESCE(\"DisposisiSekda2\", ''))) > 0");
                        })
                        ->orWhere(function($sub) {
                            $sub->whereNotNull('DisposisiBupati')->whereRaw("LENGTH(TRIM(COALESCE(\"DisposisiBupati\", ''))) > 0");
                        })
                        ->orWhere(function($sub) {
                            $sub->whereNotNull('DisposisiWakil')->whereRaw("LENGTH(TRIM(COALESCE(\"DisposisiWakil\", ''))) > 0");
                        });
                    })
                    ->where('JENISSURAT', 'Masuk')
                    ->orderBy('NO', 'DESC');

            if ($user->hasRole('setda')) {
                $query->whereIn('Posisi', ['Sekeretaris Daerah', 'Sekretaris Daerah', 'Bupati']);
            }
            if ($user->hasRole('wabup')) {
                $query->whereIn('Posisi', ['Sekeretaris Daerah', 'Sekretaris Daerah', 'Wakil Bupati']);
            }
            if ($user->hasRole('bupati')) {
                $query->whereIn('Posisi', ['Bupati']);
            }

            $totalData = $query->count();

            // search query
            if ($request->has('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                $query->where(function ($q) use ($search) {
                    $q->where('NOSURAT', 'ilike', "%$search%")
                        ->orWhere('drkpd', 'ilike', "%$search%")
                        ->orWhere('PERIHAL', 'ilike', "%$search%")
                        ->orWhere('ISI', 'ilike', "%$search%")
                        ->orWhere('KLAS3', 'ilike', "%$search%");
                });
            }
            $totalFiltered = $query->count();
            $list = $query->skip($start)->take($length)->get();

            $parseLegacyDate = function($dateStr, $format = 'DD-MM-YYYY') {
                if (empty($dateStr)) return null;
                try {
                    $str = trim((string)$dateStr);
                    if (str_contains($str, '/')) {
                        $parts = explode(' ', $str);
                        $dmy = explode('/', $parts[0]);
                        if (count($dmy) === 3) {
                            $normDate = sprintf('%04d-%02d-%02d', $dmy[2], $dmy[1], $dmy[0]);
                            if (isset($parts[1])) {
                                $normDate .= ' ' . $parts[1];
                            }
                            return Carbon::parse($normDate)->isoFormat($format);
                        }
                    }
                    return Carbon::parse($str)->isoFormat($format);
                } catch (\Throwable $e) {
                    return (string) $dateStr;
                }
            };

            $data = [];
            foreach ($list as $row) {
                $data[] = [
                    'nomor'     => $row->NOSURAT,
                    'no_agenda' => $row->NOAGENDA,
                    'klasifikasi' => $row->SIFAT_SURAT,
                    'berkas'    => $row->NAMABERKAS,
                    'wilayah'   => $row->WILAYAH,
                    'isi_surat' => $row->ISI,
                    'tanggal'   => $parseLegacyDate($row->TGLSURAT) ?? '-',
                    'kepada'    => $row->drkpd,
                    'perihal'   => $row->PERIHAL,
                    'kode'      => $row->KLAS3,
                    'tgl_buat'  => $parseLegacyDate($row->TGLENTRY) ?? '-',
                    'posisi'    => $row->Posisi,
                    'class'     => ($row->Posisi == 'Sekretaris Daerah' ? 'badge-info' : ($row->Posisi == 'Wakil Bupati' ? 'badge-secondary' : ($row->Posisi == 'Bupati' ? 'badge-primary' : 'badge-dark'))),
                    'uid'       => Crypt::encryptString($row->NO),
                    'sekda'     => $row->DisposisiSekda,
                    'sekda2'    => $row->DisposisiSekda2,
                    'bupati'    => $row->DisposisiBupati,
                    'wakil'     => $row->DisposisiWakil,
                    'tsekda'    => $parseLegacyDate($row->tglsekda1),
                    'tsekda2'   => $parseLegacyDate($row->tglsekda2),
                    'tbupati'   => $parseLegacyDate($row->tglbupati1),
                    'twakil'    => $parseLegacyDate($row->tglwakil),
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
        }

        return response()->json([
            'draw' => intval($request->draw) ?? 0,
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
        ]);
    }

    public function agenda_ssr()
    {
        $request = Request();
        $user = $request->user();
        if ($user->hasAnyRole(['administrator', 'admin', 'setda', 'wabup', 'bupati', 'umum'])) {
            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $jenis = $request->input('jenis');

            if ($jenis === 'Keluar') {
                $query = Outbox::whereNull('on_delete')->with(['klasifikasi', 'creator.leveluser']);
            } else {
                // Default ke Surat Masuk jika 'Masuk', kosong, atau 'Semua'
                $query = Inbox::whereNull('on_delete')->with([
                    'klasifikasi',
                    'creator.leveluser',
                    'disposisi.pengirim.leveluser',
                    'disposisi.penerima.leveluser',
                    'disposisi.pimpinan',
                ]);
            }

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
                $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }

            $query->orderBy('created_at', 'ASC');

            $totalData = $query->count();

            // search query
            if ($request->has('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                if ($jenis === 'Keluar') {
                    $query->where(function ($q) use ($search) {
                        $q->where('no_surat', 'ilike', "%$search%")
                            ->orWhere('no_agenda', 'ilike', "%$search%")
                            ->orWhere('perihal', 'ilike', "%$search%")
                            ->orWhere('isi_surat', 'ilike', "%$search%")
                            ->orWhere('kepada', 'ilike', "%$search%")
                            ->orWhere('wilayah', 'ilike', "%$search%");
                    });
                } else {
                    $query->where(function ($q) use ($search) {
                        $q->where('no_surat', 'ilike', "%$search%")
                            ->orWhere('no_agenda', 'ilike', "%$search%")
                            ->orWhere('perihal', 'ilike', "%$search%")
                            ->orWhere('isi_surat', 'ilike', "%$search%")
                            ->orWhere('dari', 'ilike', "%$search%")
                            ->orWhere('wilayah', 'ilike', "%$search%");
                    });
                }
            }

            $totalFiltered = $query->count();
            $list = $query->skip($start)->take($length)->get();

            $getDisposisiInfo = function($disposisis, $target) {
                if (!$disposisis || $disposisis->isEmpty()) return '-';
                $found = $disposisis->first(function($d) use ($target) {
                    $senderLevel = strtolower($d->pengirim?->leveluser?->nama ?? '');
                    if ($target === 'bupati') {
                        return $d->id_pimpinan == 1 || str_contains($senderLevel, 'bupati') || ($d->pengirim && $d->pengirim->hasRole('bupati'));
                    }
                    if ($target === 'wakil') {
                        return $d->id_pimpinan == 3 || str_contains($senderLevel, 'wakil') || ($d->pengirim && $d->pengirim->hasRole('wabup'));
                    }
                    if ($target === 'sekda') {
                        return $d->id_pimpinan == 4 || str_contains($senderLevel, 'sekda') || str_contains($senderLevel, 'sekretaris daerah') || ($d->pengirim && $d->pengirim->hasRole('setda'));
                    }
                    return false;
                });

                if ($found && !empty($found->catatan_disposisi)) {
                    $tgl = $found->created_at ? Carbon::parse($found->created_at)->isoFormat('DD-MM-YYYY HH:mm') : '';
                    return '<b><p style="width: 100%; text-align: right;">' . $tgl . '</p></b><br>' . e($found->catatan_disposisi);
                }

                return '-';
            };

            $data = [];
            foreach ($list as $l => $row) {
                $tglKirim = $row->created_at ? Carbon::parse($row->created_at)->isoFormat('DD-MM-YYYY') : '-';
                $tglSurat = $row->tgl_surat ? Carbon::parse($row->tgl_surat)->isoFormat('DD-MM-YYYY') : '-';
                $noSurat = e($row->no_surat ?? '-');

                $klas3 = e($row->klasifikasi->klas3 ?? '-');
                $ketJra = e($row->klasifikasi->ket_jra ?? '');
                $isiSurat = e($row->isi_surat ?? $row->perihal ?? '-');

                $kepada = $jenis === 'Keluar' ? ($row->kepada ?? '-') : ($row->kepada ?? 'Sekretariat Daerah');
                $dari = $jenis === 'Keluar'
                    ? ($row->creator?->leveluser?->nama ?? ($row->creator?->nama_lengkap ?? ($row->unit ?? 'Sekretariat Daerah')))
                    : ($row->dari ?? ($row->creator?->leveluser?->nama ?? ($row->creator?->nama_lengkap ?? '-')));

                $disposisis = $jenis === 'Keluar' ? collect([]) : ($row->disposisi ?? collect([]));

                $data[$l] = [
                    'no_agenda' => $row->no_agenda ?? '-',
                    'kepada'    => $kepada,
                    'tgl_buat'  => $tglKirim,
                    'tanggal'   => $tglSurat,
                    'nomor'     => $noSurat,
                    'row3'      => $tglKirim . '<br>' . $tglSurat . '<br>' . $noSurat,
                    'kode'      => $klas3,
                    'jra'       => $ketJra,
                    'isi_surat' => $isiSurat,
                    'row4'      => $klas3 . '<br><b>' . $ketJra . '</b><br>' . $isiSurat,
                    'berkas'    => $row->nama_berkas ?? '-',
                    'wilayah'   => $row->wilayah ?? '-',
                    'dari'      => $dari,
                    'perihal'   => $row->perihal ?? '-',
                    'class'     => $row->posisi_level ?? '',
                    'uid'       => Crypt::encryptString($row->uuid),
                ];

                if ($user->hasAnyRole(['administrator', 'umum', 'setda'])) {
                    $data[$l] += [
                        'sekda'     => $getDisposisiInfo($disposisis, 'sekda'),
                        'bupati'    => $getDisposisiInfo($disposisis, 'bupati'),
                        'wakil'     => $getDisposisiInfo($disposisis, 'wakil'),
                    ];
                }

                if ($user->hasRole('wabup')) {
                    $data[$l] += [
                        'wakil'     => $getDisposisiInfo($disposisis, 'wakil'),
                    ];
                }

                if ($user->hasRole('bupati')) {
                    $data[$l] += [
                        'bupati'    => $getDisposisiInfo($disposisis, 'bupati'),
                    ];
                }
            }

            return response()->json([
                'draw' => intval($request->draw) ?? 0,
                'recordsTotal' => $totalData,
                'recordsFiltered' => $totalFiltered,
                'data' => $data,
            ]);
        }

        return response()->json([
            'draw' => intval($request->draw) ?? 0,
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
        ]);
    }

    public function agenda_print() 
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(300);

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

    /**
     * Export Agenda Surat Masuk / Keluar to Excel (.xlsx).
     */
    public function export_agenda(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $jenis = $request->jenis;

        $fileName = 'Agenda_' . ($jenis ?: 'Semua') . '_' . date('Ymd_His') . '.xlsx';

        if ($jenis === 'Keluar') {
            return Excel::download(new AgendaKeluarExport($startDate, $endDate), $fileName);
        }

        return Excel::download(new AgendaMasukExport($startDate, $endDate), $fileName);
    }

    /**
     * Export Statistik Persuratan to Excel (.xlsx).
     */
    public function export_statistik(Request $request)
    {
        $year = intval($request->input('year', date('Y')));
        $fileName = 'Statistik_Persuratan_' . $year . '_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new StatistikExport($year), $fileName);
    }
}
