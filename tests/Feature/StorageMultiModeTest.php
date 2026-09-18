<?php

namespace Tests\Feature;

use App\Services\FileUploadService;
use App\Services\PdfSanitizer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StorageMultiModeTest extends TestCase
{
    protected FileUploadService $fileService;
    protected string $testNasPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileService = app(FileUploadService::class);
        $this->testNasPath = storage_path('app/private/test_nas_folder');
        File::ensureDirectoryExists($this->testNasPath);
    }

    protected function tearDown(): void
    {
        if (File::exists($this->testNasPath)) {
            File::deleteDirectory($this->testNasPath);
        }
        parent::tearDown();
    }

    public function test_default_disk_is_local_and_can_switch_manually(): void
    {
        $this->assertEquals('local', $this->fileService->getActiveDisk());

        $this->fileService->setActiveDisk('nas');
        $this->assertEquals('nas', $this->fileService->getActiveDisk());

        $this->fileService->setActiveDisk('minio');
        $this->assertEquals('minio', $this->fileService->getActiveDisk());

        $this->fileService->setActiveDisk('local');
        $this->assertEquals('local', $this->fileService->getActiveDisk());
    }

    public function test_invalid_disk_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->fileService->setActiveDisk('unsupported_cloud');
    }

    public function test_nas_mode_saves_and_resolves_file(): void
    {
        // Konfigurasi NAS path
        config(['filesystems.disks.nas.root' => $this->testNasPath]);
        $this->fileService->setActiveDisk('nas');

        $file = UploadedFile::fake()->image('nas_document.jpg', 600, 800);
        $savedName = $this->fileService->upload($file, 'surat_nas_01', 'suratmasuk');

        $this->assertNotNull($savedName);
        $this->assertStringContainsString('.jpg', $savedName);

        // Pastikan tersimpan di direktori NAS
        $expectedNasFile = $this->testNasPath . DIRECTORY_SEPARATOR . 'suratmasuk' . DIRECTORY_SEPARATOR . $savedName;
        $this->assertFileExists($expectedNasFile);

        // Resolve path harus mengembalikan lokasi NAS
        $resolved = $this->fileService->resolveFilePath($savedName, 'suratmasuk');
        $this->assertEquals($expectedNasFile, $resolved);

        // Cleanup
        $this->fileService->deleteFile($savedName, 'suratmasuk');
        $this->assertFileDoesNotExist($expectedNasFile);
    }

    public function test_auto_switch_fallback_resolves_file_when_disk_mode_changes(): void
    {
        // 1. Simpan file saat mode = 'local'
        $this->fileService->setActiveDisk('local');
        $file = UploadedFile::fake()->image('legacy_doc.jpg', 400, 400);
        $savedName = $this->fileService->upload($file, 'doc_local_01', 'suratmasuk');

        $this->assertNotNull($savedName);
        $localPath = storage_path("app/private/suratmasuk/{$savedName}");
        $this->assertFileExists($localPath);

        // 2. Sekarang admin mengganti mode ke 'nas' (di direktori berbeda)
        config(['filesystems.disks.nas.root' => $this->testNasPath]);
        $this->fileService->setActiveDisk('nas');
        $this->assertEquals('nas', $this->fileService->getActiveDisk());

        // 3. Sistem harus AUTO-FALLBACK: file lama yang diunggah di local tetap berhasil ditemukan!
        $resolved = $this->fileService->resolveFilePath($savedName, 'suratmasuk');
        $this->assertNotNull($resolved);
        $this->assertEquals($localPath, $resolved);

        // Cleanup
        $this->fileService->deleteFile($savedName, 'suratmasuk');
        $this->assertFileDoesNotExist($localPath);
    }

    public function test_minio_mode_with_fake_storage(): void
    {
        config([
            'filesystems.disks.minio.key' => 'fake-key',
            'filesystems.disks.minio.bucket' => 'fake-bucket',
        ]);
        Storage::fake('minio');
        $this->fileService->setActiveDisk('minio');

        $file = UploadedFile::fake()->image('minio_doc.jpg', 500, 500);
        $savedName = $this->fileService->upload($file, 'doc_minio_01', 'suratmasuk');

        $this->assertNotNull($savedName);

        // Pastikan file tersimpan di fake bucket MinIO
        Storage::disk('minio')->assertExists("suratmasuk/{$savedName}");

        // Resolving file harus men-download ke local cache dan mengembalikan path valid
        $resolved = $this->fileService->resolveFilePath($savedName, 'suratmasuk');
        $this->assertNotNull($resolved);
        $this->assertFileExists($resolved);

        // Cleanup
        $this->fileService->deleteFile($savedName, 'suratmasuk');
        Storage::disk('minio')->assertMissing("suratmasuk/{$savedName}");
    }

    public function test_storage_status_diagnostic(): void
    {
        $status = $this->fileService->getStorageStatus();

        $this->assertIsArray($status);
        $this->assertArrayHasKey('active_disk', $status);
        $this->assertArrayHasKey('supported_disks', $status);
        $this->assertContains('local', $status['supported_disks']);
        $this->assertContains('minio', $status['supported_disks']);
        $this->assertContains('nas', $status['supported_disks']);
    }
}
