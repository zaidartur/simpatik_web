<?php

namespace App\Exports;

use App\Models\ArsipSurat;
use Carbon\Carbon;
use Illuminate\Support\Enumerable;
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

class TindakLanjutExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    protected ?string $startDate;
    protected ?string $endDate;
    protected ?string $userRole;
    private int $rowNumber = 0;

    public function __construct(?string $startDate = null, ?string $endDate = null, ?string $userRole = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->userRole = $userRole;
    }

    public function collection(): Enumerable
    {
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
            ->where('JENISSURAT', 'Masuk');

        if ($this->userRole === 'setda') {
            $query->whereIn('Posisi', ['Sekeretaris Daerah', 'Sekretaris Daerah', 'Bupati']);
        } elseif ($this->userRole === 'wabup') {
            $query->whereIn('Posisi', ['Sekeretaris Daerah', 'Sekretaris Daerah', 'Wakil Bupati']);
        } elseif ($this->userRole === 'bupati') {
            $query->whereIn('Posisi', ['Bupati']);
        }

        if ($this->startDate && $this->endDate) {
            $startSlash = str_replace('-', '/', $this->startDate);
            $endSlash   = str_replace('-', '/', $this->endDate);
            $startDash  = str_replace('/', '-', $this->startDate);
            $endDash    = str_replace('/', '-', $this->endDate);

            $query->where(function($sub) use ($startSlash, $endSlash, $startDash, $endDash) {
                $sub->whereBetween('TGLSURAT', [$startSlash, $endSlash])
                    ->orWhereBetween('TGLSURAT', [$startDash, $endDash])
                    ->orWhereBetween('TGLENTRY', [$startSlash, $endSlash])
                    ->orWhereBetween('TGLENTRY', [$startDash, $endDash]);
            });
        }

        return $query->orderBy('NO', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'No. Agenda',
            'No. Surat',
            'Tgl. Surat',
            'Instansi / Pengirim',
            'Perihal',
            'Disposisi Sekda',
            'Disposisi Wabup',
            'Disposisi Bupati',
            'Posisi Saat Ini',
            'Status',
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;

        $formatDate = function($dateStr) {
            if (empty($dateStr)) return '-';
            try {
                $str = trim((string)$dateStr);
                if (str_contains($str, '/')) {
                    $parts = explode(' ', $str);
                    $dmy = explode('/', $parts[0]);
                    if (count($dmy) === 3) {
                        if (strlen($dmy[0]) === 4) {
                            return Carbon::createFromDate((int)$dmy[0], (int)$dmy[1], (int)$dmy[2])->format('d-m-Y');
                        }
                        return Carbon::createFromDate((int)$dmy[2], (int)$dmy[1], (int)$dmy[0])->format('d-m-Y');
                    }
                }
                return Carbon::parse($str)->format('d-m-Y');
            } catch (\Throwable $e) {
                return (string)$dateStr;
            }
        };

        $dispoSekda = trim(($row->DisposisiSekda ?? '') . ' ' . ($row->DisposisiSekda2 ?? ''));

        return [
            $this->rowNumber,
            $row->NOAGENDA ?? '-',
            $row->NOSURAT ?? '-',
            $formatDate($row->TGLSURAT),
            $row->drkpd ?? '-',
            $row->PERIHAL ?? '-',
            !empty($dispoSekda) ? $dispoSekda : '-',
            !empty($row->DisposisiWakil) ? $row->DisposisiWakil : '-',
            !empty($row->DisposisiBupati) ? $row->DisposisiBupati : '-',
            $row->Posisi ?? '-',
            $row->statussurat == 'selesai' ? 'Selesai' : 'Menunggu',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

        // Header style
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1B55E2'], // Primary blue
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(28);

        $highestRow = $sheet->getHighestRow();
        if ($highestRow > 1) {
            $sheet->getStyle('A2:K' . $highestRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFCCCCCC'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_TOP,
                ],
            ]);

            // Center align No, No. Agenda, Tgl, Status
            $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B2:B' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D2:D' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('K2:K' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return [];
    }

    public function title(): string
    {
        return 'Tindak Lanjut Disposisi';
    }
}
