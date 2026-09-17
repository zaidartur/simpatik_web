<?php

namespace App\Exports;

use App\Models\Inbox;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AgendaMasukExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    protected ?string $startDate;
    protected ?string $endDate;
    protected ?int $sifatId;

    public function __construct(?string $startDate = null, ?string $endDate = null, ?int $sifatId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->sifatId = $sifatId;
    }

    public function collection()
    {
        $query = Inbox::with(['sifat', 'klasifikasi', 'posisi:uuid,nama_lengkap'])
            ->whereNull('on_delete');

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('tgl_diterima', [
                Carbon::parse($this->startDate)->format('Y-m-d'),
                Carbon::parse($this->endDate)->format('Y-m-d'),
            ]);
        }

        if ($this->sifatId) {
            $query->where('sifat_surat', $this->sifatId);
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
            'Tgl. Terima',
            'Pengirim (Dari)',
            'Wilayah',
            'Perihal',
            'Sifat Surat',
            'Klasifikasi',
            'Status',
            'Posisi Terakhir',
        ];
    }

    public function map($inbox): array
    {
        return [
            $inbox->no_agenda,
            $inbox->year,
            $inbox->no_surat ?: '-',
            $inbox->tgl_surat ? Carbon::parse($inbox->tgl_surat)->format('d/m/Y') : '-',
            $inbox->tgl_diterima ? Carbon::parse($inbox->tgl_diterima)->format('d/m/Y') : '-',
            $inbox->dari,
            $inbox->wilayah ?: '-',
            $inbox->perihal,
            $inbox->sifat->nama_sifat ?? '-',
            $inbox->klasifikasi->klas3 ?? '-',
            strtoupper($inbox->status_surat),
            $inbox->posisi->nama_lengkap ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:L1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1:L1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('3B3F5C');
        $sheet->getStyle('A1:L1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Agenda Surat Masuk';
    }
}
