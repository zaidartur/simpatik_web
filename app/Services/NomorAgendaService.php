<?php

namespace App\Services;

use App\Models\Inbox;
use App\Models\Outbox;
use Illuminate\Support\Facades\DB;

class NomorAgendaService
{
    /**
     * Generate transactional nomor agenda with lockForUpdate to prevent race conditions.
     *
     * @param string $type 'inbox' or 'outbox'
     * @param bool $isPrimary
     * @param int|null $level
     * @param int|null $year
     * @return int
     */
    public function generate(string $type, bool $isPrimary, ?int $level = null, ?int $year = null): int
    {
        $year = $year ?: intval(date('Y'));

        return DB::transaction(function () use ($type, $isPrimary, $level, $year) {
            $modelClass = ($type === 'outbox') ? Outbox::class : Inbox::class;

            $query = $modelClass::where('year', $year)->lockForUpdate();

            if ($isPrimary) {
                $query->where('is_primary_agenda', true);
            } else {
                $query->where('level_surat', $level);
            }

            $last = $query->orderBy('no_agenda', 'desc')->first();

            return $last ? (intval($last->no_agenda) + 1) : 1;
        });
    }

    /**
     * Preview nomor agenda without locking (for AJAX preview in forms).
     */
    public function preview(string $type, bool $isPrimary, ?int $level = null, ?int $year = null): int
    {
        $year = $year ?: intval(date('Y'));
        $modelClass = ($type === 'outbox') ? Outbox::class : Inbox::class;

        $query = $modelClass::where('year', $year);

        if ($isPrimary) {
            $query->where('is_primary_agenda', true);
        } else {
            $query->where('level_surat', $level);
        }

        $last = $query->orderBy('no_agenda', 'desc')->first();

        return $last ? (intval($last->no_agenda) + 1) : 1;
    }
}
