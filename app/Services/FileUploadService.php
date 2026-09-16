<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class FileUploadService
{
    protected PdfSanitizer $pdfSanitizer;

    public function __construct(PdfSanitizer $pdfSanitizer)
    {
        $this->pdfSanitizer = $pdfSanitizer;
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
     * Upload and sanitize file, saving into private storage.
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
        $destinationFolder = storage_path("app/private/{$subfolder}");

        File::ensureDirectoryExists($destinationFolder);

        $destinationPath = $destinationFolder . DIRECTORY_SEPARATOR . $fileName;
        file_put_contents($destinationPath, $image->toString());

        return $fileName;
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

            $destinationFolder = storage_path("app/private/{$subfolder}");
            File::ensureDirectoryExists($destinationFolder);

            $fileName = $id . '_sanitized_' . date('YmdHis') . '.pdf';
            $destinationPath = $destinationFolder . DIRECTORY_SEPARATOR . $fileName;

            $moved = File::move($tempOutput, $destinationPath);

            @unlink($tempInput);
            @unlink($tempOutput);

            return $moved ? $fileName : null;
        } catch (\Throwable $e) {
            Log::error('Gagal sanitasi PDF: ' . $e->getMessage());
            @unlink($tempInput);
            @unlink($tempOutput);
            return null;
        }
    }

    /**
     * Resolve file path with fallback to legacy public uploads.
     */
    public function resolveFilePath(string $fileName, string $subfolder): ?string
    {
        $safeName = basename($fileName);
        if ($safeName !== $fileName) {
            return null;
        }

        $privatePath = storage_path("app/private/{$subfolder}/{$safeName}");
        if (file_exists($privatePath)) {
            return $privatePath;
        }

        $legacyPath = public_path("datas/uploads/{$subfolder}/{$safeName}");
        if (file_exists($legacyPath)) {
            return $legacyPath;
        }

        return null;
    }

    /**
     * Delete file from private or legacy storage.
     */
    public function deleteFile(?string $fileName, string $subfolder): void
    {
        if (empty($fileName)) {
            return;
        }

        $path = $this->resolveFilePath($fileName, $subfolder);
        if ($path && file_exists($path)) {
            @unlink($path);
        }
    }
}
