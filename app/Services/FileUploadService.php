<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class FileUploadService
{
    protected PdfSanitizer $pdfSanitizer;
    protected string $activeDisk;

    public function __construct(PdfSanitizer $pdfSanitizer, ?string $disk = null)
    {
        $this->pdfSanitizer = $pdfSanitizer;
        $this->activeDisk = $disk ?? config('filesystems.document_disk', env('DOCUMENT_STORAGE_DISK', 'local'));
    }

    /**
     * Dapatkan disk penyimpanan aktif saat ini ('local', 'minio', atau 'nas').
     */
    public function getActiveDisk(): string
    {
        return $this->activeDisk;
    }

    /**
     * Ganti mode penyimpanan secara dinamis pada runtime (misal: untuk testing/migrasi).
     */
    public function setActiveDisk(string $disk): self
    {
        $validDisks = ['local', 'minio', 'nas'];
        $normalized = strtolower(trim($disk));
        if (!in_array($normalized, $validDisks, true)) {
            throw new \InvalidArgumentException("Storage disk [{$disk}] tidak didukung. Pilihan valid: " . implode(', ', $validDisks));
        }

        $this->activeDisk = $normalized;
        return $this;
    }

    /**
     * Dapatkan path direktori NAS (dapat dikonfigurasi via env NAS_STORAGE_PATH).
     */
    public function getNasFolderPath(string $subfolder = ''): string
    {
        $base = config('filesystems.disks.nas.root', env('NAS_STORAGE_PATH', storage_path('app/private')));
        return $subfolder !== '' ? rtrim($base, '/\\') . DIRECTORY_SEPARATOR . $subfolder : rtrim($base, '/\\');
    }

    /**
     * Cek apakah kredensial MinIO sudah dikonfigurasi di .env atau runtime.
     */
    public function isMinioConfigured(): bool
    {
        $key = config('filesystems.disks.minio.key');
        $bucket = config('filesystems.disks.minio.bucket');
        return !empty($key) && !empty($bucket);
    }

    /**
     * Validate magic bytes of the uploaded file.
     */
    public function validateMagicBytes(UploadedFile $file, array $allowed = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg']): bool
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $realMime = $finfo->file($file->getRealPath());

        return in_array($realMime, $allowed);
    }

    /**
     * Upload and sanitize file, saving into active storage (local, minio, or nas).
     */
    public function upload(UploadedFile $file, string $id, string $subfolder = 'suratmasuk'): ?string
    {
        if (!$this->validateMagicBytes($file)) {
            Log::warning("File upload ditolak: MIME tidak valid untuk file id {$id}");
            return null;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $realMime = $finfo->file($file->getRealPath());

        if (in_array($realMime, ['image/jpeg', 'image/png', 'image/jpg'])) {
            return $this->sanitizeImage($file, $id, $subfolder);
        } elseif ($realMime === 'application/pdf') {
            return $this->sanitizePdf($file, $id, $subfolder);
        }

        return null;
    }

    /**
     * Sanitize and compress image, re-orienting and converting to JPEG.
     */
    public function sanitizeImage(UploadedFile $file, string $id, string $subfolder): ?string
    {
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file)
            ->orient()
            ->toJpeg(quality: 90);

        $fileName = $id . '_sanitized_' . date('YmdHis') . '.jpg';
        $saved = $this->saveFileContent($fileName, $image->toString(), $subfolder);

        return $saved ? $fileName : null;
    }

    /**
     * Sanitize PDF using Ghostscript sandbox.
     */
    public function sanitizePdf(UploadedFile $file, string $id, string $subfolder): ?string
    {
        $tempInput = storage_path('app/tmp/original_' . date('YmdHis') . '_' . uniqid() . '.pdf');
        $tempOutput = storage_path('app/tmp/sanitized_' . date('YmdHis') . '_' . uniqid() . '.pdf');

        File::ensureDirectoryExists(dirname($tempInput));
        $file->move(dirname($tempInput), basename($tempInput));

        try {
            $this->pdfSanitizer->sanitize($tempInput, $tempOutput);

            $fileName = $id . '_sanitized_' . date('YmdHis') . '.pdf';
            $saved = $this->saveFileFromPath($fileName, $tempOutput, $subfolder);

            @unlink($tempInput);
            @unlink($tempOutput);

            return $saved ? $fileName : null;
        } catch (\Throwable $e) {
            Log::error('Gagal sanitasi PDF: ' . $e->getMessage());
            @unlink($tempInput);
            @unlink($tempOutput);
            return null;
        }
    }

    /**
     * Simpan konten binary ke target storage aktif (local, minio, atau nas).
     */
    protected function saveFileContent(string $fileName, string $content, string $subfolder): bool
    {
        if ($this->activeDisk === 'minio') {
            try {
                return Storage::disk('minio')->put("{$subfolder}/{$fileName}", $content);
            } catch (\Throwable $e) {
                Log::error("Gagal simpan konten ke MinIO [{$subfolder}/{$fileName}], fallback ke local: " . $e->getMessage());
                return $this->saveToLocalPrivate($fileName, $content, $subfolder);
            }
        }

        if ($this->activeDisk === 'nas') {
            $dir = $this->getNasFolderPath($subfolder);
            File::ensureDirectoryExists($dir);
            $res = file_put_contents($dir . DIRECTORY_SEPARATOR . $fileName, $content);
            return $res !== false;
        }

        return $this->saveToLocalPrivate($fileName, $content, $subfolder);
    }

    /**
     * Simpan file dari path fisik lokal ke target storage aktif.
     */
    protected function saveFileFromPath(string $fileName, string $sourcePath, string $subfolder): bool
    {
        if ($this->activeDisk === 'minio') {
            try {
                $stream = fopen($sourcePath, 'r');
                if ($stream !== false) {
                    $saved = Storage::disk('minio')->put("{$subfolder}/{$fileName}", $stream);
                    if (is_resource($stream)) {
                        fclose($stream);
                    }
                    if ($saved) {
                        return true;
                    }
                }
            } catch (\Throwable $e) {
                Log::error("Gagal upload stream ke MinIO [{$subfolder}/{$fileName}], fallback ke local: " . $e->getMessage());
            }
            return $this->copyToLocalPrivate($fileName, $sourcePath, $subfolder);
        }

        if ($this->activeDisk === 'nas') {
            $dir = $this->getNasFolderPath($subfolder);
            File::ensureDirectoryExists($dir);
            return File::copy($sourcePath, $dir . DIRECTORY_SEPARATOR . $fileName);
        }

        return $this->copyToLocalPrivate($fileName, $sourcePath, $subfolder);
    }

    protected function saveToLocalPrivate(string $fileName, string $content, string $subfolder): bool
    {
        $dir = storage_path("app/private/{$subfolder}");
        File::ensureDirectoryExists($dir);
        return file_put_contents($dir . DIRECTORY_SEPARATOR . $fileName, $content) !== false;
    }

    protected function copyToLocalPrivate(string $fileName, string $sourcePath, string $subfolder): bool
    {
        $dir = storage_path("app/private/{$subfolder}");
        File::ensureDirectoryExists($dir);
        return File::copy($sourcePath, $dir . DIRECTORY_SEPARATOR . $fileName);
    }

    /**
     * Resolve file path dengan auto-switch & fallback berantai:
     * 1. Cek disk aktif (local, nas, atau minio dengan auto-cache)
     * 2. Fallback: jika tidak ada di disk aktif, cari di disk lain secara otomatis
     * 3. Fallback: cari di legacy public uploads jika ada arsip lama
     */
    public function resolveFilePath(string $fileName, string $subfolder): ?string
    {
        $safeName = basename($fileName);
        if ($safeName !== $fileName) {
            return null;
        }

        // 1. Cek pada disk aktif terlebih dahulu
        if ($this->activeDisk === 'local') {
            $localPath = storage_path("app/private/{$subfolder}/{$safeName}");
            if (file_exists($localPath)) {
                return $localPath;
            }
        } elseif ($this->activeDisk === 'nas') {
            $nasPath = $this->getNasFolderPath($subfolder) . DIRECTORY_SEPARATOR . $safeName;
            if (file_exists($nasPath)) {
                return $nasPath;
            }
        } elseif ($this->activeDisk === 'minio') {
            $cached = $this->fetchFromMinioToLocalCache($safeName, $subfolder);
            if ($cached) {
                return $cached;
            }
        }

        // 2. Auto-switch fallback: jika file diunggah sebelum admin mengganti mode disk
        // Cek Local Private
        $privatePath = storage_path("app/private/{$subfolder}/{$safeName}");
        if (file_exists($privatePath)) {
            return $privatePath;
        }

        // Cek NAS (jika lokasi NAS terpisah dari local private)
        $nasFallback = $this->getNasFolderPath($subfolder) . DIRECTORY_SEPARATOR . $safeName;
        if (file_exists($nasFallback)) {
            return $nasFallback;
        }

        // Cek MinIO hanya jika mode aktif adalah minio
        if ($this->activeDisk === 'minio') {
            $cached = $this->fetchFromMinioToLocalCache($safeName, $subfolder);
            if ($cached) {
                return $cached;
            }
        }

        // Cek Legacy Public Folder
        $legacyPath = public_path("datas/uploads/{$subfolder}/{$safeName}");
        if (file_exists($legacyPath)) {
            return $legacyPath;
        }

        return null;
    }

    /**
     * Download file dari MinIO ke cache lokal sementara untuk kompatibilitas stream/FPDI.
     */
    protected function fetchFromMinioToLocalCache(string $safeName, string $subfolder): ?string
    {
        if ($this->activeDisk !== 'minio') {
            return null;
        }

        $cacheDir = storage_path("app/tmp/minio_cache/{$subfolder}");
        $cachePath = $cacheDir . DIRECTORY_SEPARATOR . $safeName;

        if (file_exists($cachePath) && filesize($cachePath) > 0) {
            return $cachePath;
        }

        try {
            if (Storage::disk('minio')->exists("{$subfolder}/{$safeName}")) {
                File::ensureDirectoryExists($cacheDir);
                $content = Storage::disk('minio')->get("{$subfolder}/{$safeName}");
                if ($content !== null && file_put_contents($cachePath, $content) !== false) {
                    return $cachePath;
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Gagal fetch file dari MinIO [{$subfolder}/{$safeName}]: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Kirim response file langsung ke browser (stream response).
     */
    public function responseFile(string $fileName, string $subfolder, array $headers = [])
    {
        $safeName = basename($fileName);
        if ($safeName !== $fileName) {
            return abort(404);
        }

        $path = $this->resolveFilePath($safeName, $subfolder);
        if (!$path || !file_exists($path)) {
            return abort(404);
        }

        return response()->file($path, $headers);
    }

    /**
     * Delete file from active disk and all fallback locations.
     */
    public function deleteFile(?string $fileName, string $subfolder): void
    {
        if (empty($fileName)) {
            return;
        }

        $safeName = basename($fileName);
        if ($safeName !== $fileName) {
            return;
        }

        // Hapus dari local private
        $localPath = storage_path("app/private/{$subfolder}/{$safeName}");
        if (file_exists($localPath)) {
            @unlink($localPath);
        }

        // Hapus dari NAS
        $nasPath = $this->getNasFolderPath($subfolder) . DIRECTORY_SEPARATOR . $safeName;
        if (file_exists($nasPath)) {
            @unlink($nasPath);
        }

        // Hapus dari MinIO (hanya jika mode aktif adalah minio)
        if ($this->activeDisk === 'minio') {
            try {
                if (Storage::disk('minio')->exists("{$subfolder}/{$safeName}")) {
                    Storage::disk('minio')->delete("{$subfolder}/{$safeName}");
                }
            } catch (\Throwable $e) {
                Log::warning("Gagal hapus file dari MinIO [{$subfolder}/{$safeName}]: " . $e->getMessage());
            }
        }

        // Hapus dari local minio cache jika ada
        $cachePath = storage_path("app/tmp/minio_cache/{$subfolder}/{$safeName}");
        if (file_exists($cachePath)) {
            @unlink($cachePath);
        }

        // Hapus dari legacy public uploads
        $legacyPath = public_path("datas/uploads/{$subfolder}/{$safeName}");
        if (file_exists($legacyPath)) {
            @unlink($legacyPath);
        }
    }

    /**
     * Dapatkan informasi dan status konfigurasi storage aktif.
     */
    public function getStorageStatus(): array
    {
        return [
            'active_disk'     => $this->activeDisk,
            'supported_disks' => ['local', 'minio', 'nas'],
            'local_path'      => storage_path('app/private'),
            'nas_path'        => $this->getNasFolderPath(),
            'minio_endpoint'  => env('MINIO_ENDPOINT', 'belum dikonfigurasi'),
            'minio_bucket'    => env('MINIO_BUCKET', 'belum dikonfigurasi'),
        ];
    }
}
