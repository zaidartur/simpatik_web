<?php

namespace App\Exports;

use App\Models\Inbox;
use App\Models\Outbox;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StatistikExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    protected int $year;

    public function __construct(?int $year = null)
    {
        $this->year = $year ?: intval(date('Y'));
    }

    public function collection(): Enumerable
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $inboxCounts = Inbox::selectRaw('EXTRACT(MONTH FROM tgl_diterima) as month, count(*) as count')
            ->whereYear('tgl_diterima', $this->year)
            ->whereNull('on_delete')
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $outboxCounts = Outbox::selectRaw('EXTRACT(MONTH FROM tgl_surat) as month, count(*) as count')
            ->whereYear('tgl_surat', $this->year)
            ->whereNull('on_delete')
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $rows = collect();
        $totalMasuk = 0;
        $totalKeluar = 0;

        foreach ($months as $num => $name) {
            $masuk = intval($inboxCounts[$num] ?? 0);
            $keluar = intval($outboxCounts[$num] ?? 0);
            $totalMasuk += $masuk;
            $totalKeluar += $keluar;

            $rows->push((object)[
                'no'     => $num,
                'bulan'  => $name,
                'tahun'  => $this->year,
                'masuk'  => $masuk,
                'keluar' => $keluar,
                'total'  => $masuk + $keluar,
            ]);
        }

        // Total row
        $rows->push((object)[
            'no'     => '',
            'bulan'  => 'TOTAL TAHUN ' . $this->year,
            'tahun'  => $this->year,
            'masuk'  => $totalMasuk,
            'keluar' => $totalKeluar,
            'total'  => $totalMasuk + $totalKeluar,
        ]);

        return $rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'Bulan',
            'Tahun',
            'Jumlah Surat Masuk',
            'Jumlah Surat Keluar',
            'Total Keseluruhan',
        ];
    }

    public function map($row): array
    {
        return [
            $row->no,
            $row->bulan,
            $row->tahun,
            $row->masuk,
            $row->keluar,
            $row->total,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:F1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1:F1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1B55E2');
        $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Bold total row
        $sheet->getStyle('A14:F14')->getFont()->setBold(true);
        $sheet->getStyle('A14:F14')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E0E6ED');

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Statistik ' . $this->year;
    }
}
