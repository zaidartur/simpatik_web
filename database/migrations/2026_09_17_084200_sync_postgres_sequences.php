<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $tables = [
            'sifat_surats',
            'klasifikasis',
            'tempat_berkas',
            'perkembangans',
            'media_surats',
            'inboxes',
            'outboxes',
            'disposisis',
            'users',
            'level_users',
            'activity_logs',
        ];

        foreach ($tables as $table) {
            try {
                $maxId = DB::table($table)->max('id');
                if ($maxId !== null) {
                    DB::statement("SELECT setval(pg_get_serial_sequence('{$table}', 'id'), {$maxId})");
                }
            } catch (\Throwable $e) {
                // Ignore if table has no id sequence
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
