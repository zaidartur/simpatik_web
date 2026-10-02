<?php

namespace Database\Seeders;

use App\Services\KlasifikasiImportService;
use Illuminate\Database\Seeder;

class KlasifikasiPerbupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(KlasifikasiImportService $importService): void
    {
        $this->command->info("Menjalankan seeder kode klasifikasi Perbup Karanganyar 045.1/2023...");
        $res = $importService->importPerbup();
        $this->command->info("Selesai! Diperbarui: {$res['updated']}, Ditambahkan: {$res['inserted']}, Total DB: {$res['total_in_db']}.");
    }
}
