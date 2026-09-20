<?php

namespace App\Console\Commands;

use App\Services\LegacyMigrationService;
use Illuminate\Console\Command;

class MigrateLegacyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-legacy 
                            {--file= : Path berkas SQL atau CSV (default: .docs/sources/aktif.sql)}
                            {--type=all : Tipe data yang diimpor (all, masuk, keluar)}
                            {--dry-run : Simulasi validasi data tanpa menyimpan perubahan ke database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrasi data persuratan arsip legacy (.sql atau .csv) ke tabel inboxes dan outboxes';

    /**
     * Execute the console command.
     */
    public function handle(LegacyMigrationService $migrationService)
    {
        $defaultCandidates = [
            storage_path('app/private/legacy/aktif.sql'),
            storage_path('app/private/sources/aktif.sql'),
            storage_path('app/private/aktif.sql'),
            base_path('.docs/sources/aktif.sql'),
        ];
        $defaultPath = storage_path('app/private/legacy/aktif.sql');
        foreach ($defaultCandidates as $cand) {
            if (file_exists($cand)) {
                $defaultPath = $cand;
                break;
            }
        }
        $filePath = $this->option('file') ?: $defaultPath;
        $type = strtolower($this->option('type') ?: 'all');
        $dryRun = (bool) $this->option('dry-run');

        if (!in_array($type, ['all', 'masuk', 'keluar'], true)) {
            $this->error("Pilihan --type tidak valid. Gunakan 'all', 'masuk', atau 'keluar'.");
            return Command::FAILURE;
        }

        if (!file_exists($filePath)) {
            $this->error("Berkas [{$filePath}] tidak ditemukan.");
            $this->line("Pastikan berkas berada di path yang benar atau gunakan opsi --file=/path/ke/berkas.sql");
            return Command::FAILURE;
        }

        $this->info("=================================================");
        $this->info("   SIPERMAS - Migrasi Data Persuratan Legacy    ");
        $this->info("=================================================");
        $this->line("Berkas Sumber : <comment>{$filePath}</comment> (" . number_format(filesize($filePath) / 1024 / 1024, 2) . " MB)");
        $this->line("Tipe Impor    : <comment>" . strtoupper($type) . "</comment>");
        $this->line("Mode Eksekusi : " . ($dryRun ? "<fg=yellow;options=bold>DRY RUN (Simulasi tanpa menyimpan)</>" : "<fg=green;options=bold>LIVE (Tulis ke Database)</>"));
        $this->newLine();

        if (!$dryRun && !$this->confirm("Apakah Anda yakin ingin memulai migrasi data ke database?", true)) {
            $this->warn("Operasi migrasi dibatalkan oleh pengguna.");
            return Command::SUCCESS;
        }

        $startTime = microtime(true);
        $this->line("Memulai parsing dan konversi data...");

        $bar = $this->output->createProgressBar();
        $bar->setFormat(" %current% data [%bar%] %elapsed% | %message%");
        $bar->setMessage("Membaca berkas...");
        $bar->start();

        try {
            $result = $migrationService->migrateFile(
                $filePath,
                $type,
                $dryRun,
                function (int $current, string $message) use ($bar) {
                    $bar->setProgress($current);
                    $bar->setMessage($message);
                }
            );

            $bar->finish();
            $this->newLine(2);

            $duration = round(microtime(true) - $startTime, 2);

            $this->info("=================================================");
            $this->info("          RINGKASAN HASIL MIGRASI               ");
            $this->info("=================================================");
            
            $this->table(
                ['Parameter', 'Keterangan'],
                [
                    ['Total Baris Diparsing', number_format($result['total_parsed'])],
                    ['Surat Masuk Berhasil', number_format($result['masuk_success'])],
                    ['Surat Keluar Berhasil', number_format($result['keluar_success'])],
                    ['Data Dilewati / Filtered', number_format($result['skipped'])],
                    ['Status Eksekusi', $dryRun ? 'DRY RUN (SUKSES)' : 'MIGRASI LIVE (SUKSES)'],
                    ['Durasi Proses', "{$duration} detik"],
                ]
            );

            if ($dryRun) {
                $this->warn("Catatan: Ini adalah DRY RUN. Tidak ada baris yang disimpan ke database.");
                $this->line("Jalankan tanpa opsi --dry-run untuk menyimpan data.");
            } else {
                $this->info("Data berhasil dimigrasikan ke database SIMPATIK/SIPERMAS.");
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $bar->finish();
            $this->newLine(2);
            $this->error("Terjadi kegagalan saat migrasi: " . $e->getMessage());
            $this->line($e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
