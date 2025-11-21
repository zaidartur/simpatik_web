<?php

namespace App\Http\Controllers;

use App\Models\ArsipSurat;
use Illuminate\Http\Request;

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
}
