<?php

namespace App\Console\Commands;

use App\Services\KlasifikasiImportService;
use Illuminate\Console\Command;

class ImportKlasifikasiPerbup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-klasifikasi
                            {--file= : Path berkas data JSON klasifikasi kustom}
                            {--force : Paksa timpa pengaturan JRA (r_aktif, r_inaktif, ket_jra, nilai_guna) jika kode sudah ada}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perbarui dan tambah kode klasifikasi arsip Perbup Karanganyar 045.1/2023 ke tabel klasifikasis';

    /**
     * Execute the console command.
     */
    public function handle(KlasifikasiImportService $importService): int
    {
        $this->info("=== Impor & Pembaruan Kode Klasifikasi Arsip (Perbup 045.1/2023) ===");

        $customFile = $this->option('file');
        $force = (bool) $this->option('force');

        try {
            $progressBar = null;

            $result = $importService->importPerbup(
                $customFile,
                $force,
                function ($current, $total) use (&$progressBar) {
                    if (!$progressBar) {
                        $progressBar = $this->output->createProgressBar($total);
                        $progressBar->start();
                    }
                    $progressBar->setProgress($current);
                }
            );

            if ($progressBar) {
                $progressBar->finish();
                $this->newLine(2);
            }

            $this->info("Proses impor/pembaruan kode klasifikasi selesai.");
            $this->table(
                ['Indikator', 'Jumlah'],
                [
                    ['Total Kode pada Sumber Data', number_format($result['total_source'])],
                    ['Kode Berhasil Diperbarui (Existing)', number_format($result['updated'])],
                    ['Kode Baru Ditambahkan (Insert)', number_format($result['inserted'])],
                    ['Total Kode Klasifikasi di Database Sekarang', number_format($result['total_in_db'])],
                ]
            );

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Terjadi kesalahan saat memproses data: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
