<?php

namespace App\Http\Controllers;

use App\Models\Inbox;
use App\Models\Outbox;
use App\Services\LegacyMigrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LegacyMigrationController extends Controller
{
    protected LegacyMigrationService $migrationService;

    public function __construct(LegacyMigrationService $migrationService)
    {
        // Double-layer security: strictly guard against execution outside local / testing environments
        if (!app()->environment(['local', 'testing'])) {
            abort(404);
        }

        $this->migrationService = $migrationService;
    }

    /**
     * Resolusi path file bawaan pada folder storage private server.
     */
    protected function getDefaultFilePath(): string
    {
        $candidates = [
            storage_path('app/private/legacy/aktif.sql'),
            storage_path('app/private/sources/aktif.sql'),
            storage_path('app/private/aktif.sql'),
            base_path('.docs/sources/aktif.sql'),
        ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return storage_path('app/private/legacy/aktif.sql');
    }

    /**
     * Tampilkan halaman migrasi data legacy.
     */
    public function index()
    {
        $defaultFilePath = $this->getDefaultFilePath();
        $defaultFileExists = file_exists($defaultFilePath);
        $defaultFileSize = $defaultFileExists ? round(filesize($defaultFilePath) / 1024 / 1024, 2) : 0;
        
        $displayPath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $defaultFilePath);
        $displayPath = str_replace('\\', '/', $displayPath);

        $inboxCount = Inbox::count();
        $outboxCount = Outbox::count();

        return view('main.migration.index', compact(
            'defaultFileExists',
            'defaultFileSize',
            'defaultFilePath',
            'displayPath',
            'inboxCount',
            'outboxCount'
        ));
    }

    /**
     * Proses migrasi data legacy (via AJAX / Form submission).
     */
    public function process(Request $request)
    {
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '512M');

        $request->validate([
            'source_type'   => 'required|in:default,upload',
            'data_type'     => 'required|in:all,masuk,keluar',
            'dry_run'       => 'nullable|boolean',
            'uploaded_file' => 'required_if:source_type,upload|file|max:51200', // max 50MB
        ]);

        $dryRun = $request->boolean('dry_run', false);
        $dataType = $request->input('data_type', 'all');
        $tempPath = null;

        try {
            if ($request->input('source_type') === 'upload') {
                $file = $request->file('uploaded_file');
                $ext = strtolower($file->getClientOriginalExtension());
                if (!in_array($ext, ['sql', 'csv', 'txt'], true)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Format file tidak didukung. Harap unggah berkas .sql atau .csv.',
                    ], 422);
                }

                $fileName = 'upload_' . time() . '_' . uniqid() . '.' . $ext;
                $storedPath = $file->storeAs('temp_migration', $fileName);
                $filePath = Storage::path($storedPath);
                $tempPath = $filePath;
            } else {
                $filePath = $this->getDefaultFilePath();
                if (!file_exists($filePath)) {
                    $relativeName = str_replace('\\', '/', str_replace(base_path() . DIRECTORY_SEPARATOR, '', $filePath));
                    return response()->json([
                        'success' => false,
                        'message' => "Berkas sumber bawaan [{$relativeName}] tidak ditemukan di storage private server.",
                    ], 404);
                }
            }

            $startTime = microtime(true);

            $result = $this->migrationService->migrateFile(
                $filePath,
                $dataType,
                $dryRun
            );

            $duration = round(microtime(true) - $startTime, 2);
            $result['duration'] = $duration;

            $message = $dryRun 
                ? "Simulasi DRY RUN selesai! Validasi {$result['total_parsed']} data berhasil dalam {$duration} detik (tanpa menyimpan ke DB)."
                : "Migrasi data LIVE berhasil! Berhasil memasukkan {$result['masuk_success']} Surat Masuk dan {$result['keluar_success']} Surat Keluar dalam {$duration} detik.";

            return response()->json([
                'success' => true,
                'message' => $message,
                'data'    => $result,
            ]);

        } catch (\Throwable $e) {
            Log::error('Legacy Migration Failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses migrasi: ' . $e->getMessage(),
            ], 500);

        } finally {
            if ($tempPath && file_exists($tempPath)) {
                @unlink($tempPath);
            }
        }
    }
}
