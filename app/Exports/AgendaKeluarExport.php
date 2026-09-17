<?php

namespace App\Exports;

use App\Models\Outbox;
use Carbon\Carbon;
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

class AgendaKeluarExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    protected ?string $startDate;
    protected ?string $endDate;

    public function __construct(?string $startDate = null, ?string $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection(): Enumerable
    {
        $query = Outbox::with(['sifat:id,nama_sifat', 'klasifikasi:id,klas3', 'pengolah:id,nama_unit'])
            ->whereNull('on_delete');

        if ($this->startDate && $this->endDate) {
            $startDate = Carbon::parse($this->startDate)->startOfDay();
            $endDate = Carbon::parse($this->endDate)->endOfDay();
            $sDateStr = Carbon::parse($this->startDate)->format('Y-m-d');
            $eDateStr = Carbon::parse($this->endDate)->format('Y-m-d');

            $query->where(function ($q) use ($startDate, $endDate, $sDateStr, $eDateStr) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->orWhereBetween('tgl_surat', [$sDateStr, $eDateStr]);
            });
        } else {
            $query->where('year', date('Y'));
        }

        return $query->orderBy('no_agenda', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'No. Agenda',
            'Tahun',
            'No. Surat',
            'Tgl. Surat',
            'Tgl. Naik',
            'Tgl. Diteruskan',
            'Kepada (Tujuan)',
            'Wilayah',
            'Perihal',
            'Unit Pengolah',
            'Sifat Surat',
            'Klasifikasi',
        ];
    }

    public function map($outbox): array
    {
        return [
            $outbox->no_agenda,
            $outbox->year,
            $outbox->no_surat ?: '-',
            $outbox->tgl_surat ? Carbon::parse($outbox->tgl_surat)->format('d/m/Y') : '-',
            $outbox->tgl_naik ? Carbon::parse($outbox->tgl_naik)->format('d/m/Y') : '-',
            $outbox->tgl_diteruskan ? Carbon::parse($outbox->tgl_diteruskan)->format('d/m/Y') : '-',
            $outbox->kepada,
            $outbox->wilayah ?: '-',
            $outbox->perihal,
            $outbox->pengolah->nama_unit ?? ($outbox->unit ?: '-'),
            $outbox->sifat->nama_sifat ?? '-',
            $outbox->klasifikasi->klas3 ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:L1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1:L1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('00AB55');
        $sheet->getStyle('A1:L1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Agenda Surat Keluar';
    }
}
