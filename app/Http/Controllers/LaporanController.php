<?php

namespace App\Http\Controllers;

use App\Models\ArsipSurat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class LaporanController extends Controller
{
    public function __construct() {
        $this->middleware('permission:statistik', ['only' => ['statistik', 'statistik_ssr']]);
        // $this->middleware('permission:edit surat masuk', ['only' => ['edit', 'update']]);
        // $this->middleware('permission:hapus surat masuk', ['only' => ['destroy']]);
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

            $query = ArsipSurat::whereNotNull('DisposisiSekda')->orWhereNotNull('DisposisiSekda2')->orWhereNotNull('DisposisiBupati')->orWhereNotNull('DisposisiWakil')
                    ->where('JENISSURAT', 'Masuk')
                    ->orderBy('NO', 'DESC');

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
}
