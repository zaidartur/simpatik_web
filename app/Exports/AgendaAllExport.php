<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AgendaAllExport implements Export, WithMultipleSheets
{
    protected ?string $startDate;
    protected ?string $endDate;

    public function __construct(?string $startDate = null, ?string $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function sheets(): array
    {
        return [
            new AgendaMasukExport($this->startDate, $this->endDate),
            new AgendaKeluarExport($this->startDate, $this->endDate),
        ];
    }
}
