<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class PrivateFileStorageTest extends TestCase
{
    /**
     * Test that public/datas directory does not exist.
     */
    public function test_public_datas_directory_does_not_exist(): void
    {
        $this->assertFalse(
            File::isDirectory(public_path('datas')),
            'public/datas directory must not exist in web root'
        );
    }

    /**
     * Test that private storage subdirectories exist.
     */
    public function test_private_storage_subdirectories_exist(): void
    {
        $this->assertTrue(File::isDirectory(storage_path('app/private/suratmasuk')));
        $this->assertTrue(File::isDirectory(storage_path('app/private/suratkeluar')));
        $this->assertTrue(File::isDirectory(storage_path('app/private/duplikat')));
    }

    /**
     * Test guest cannot access private scan files.
     */
    public function test_guest_cannot_access_private_files(): void
    {
        $encryptedDummy = Crypt::encryptString('dummy.pdf');

        $this->get('/surat-masuk/lihat-file/' . $encryptedDummy)
            ->assertRedirect('/login');

        $this->get('/surat-keluar/lihat-file/' . $encryptedDummy)
            ->assertRedirect('/login');

        $this->get('/surat-keluar/lihat-surat-duplikat/dummy.pdf')
            ->assertRedirect('/login');

        $this->get('/surat-keluar/unduh-surat-duplikat/dummy.pdf')
            ->assertRedirect('/login');
    }

    /**
     * Test user without permission gets 403 Forbidden.
     */
    public function test_user_without_permission_is_forbidden(): void
    {
        $levelId = \App\Models\LevelUser::value('id') ?? 1;

        $userWithoutPermission = User::create([
            'uuid'         => \Illuminate\Support\Str::uuid()->toString(),
            'nama_lengkap' => 'User No Permission',
            'username'     => 'noperm_' . rand(1000, 9999),
            'email'        => 'noperm_' . rand(1000, 9999) . '@example.com',
            'password'     => \Illuminate\Support\Facades\Hash::make('password123'),
            'level'        => $levelId,
            'blokir'       => 'N',
        ]);
        $userWithoutPermission->syncRoles([]);
        $userWithoutPermission->syncPermissions([]);

        $encryptedDummy = Crypt::encryptString('dummy.pdf');

        $this->actingAs($userWithoutPermission)
            ->get('/surat-masuk/lihat-file/' . $encryptedDummy)
            ->assertStatus(403);

        $this->actingAs($userWithoutPermission)
            ->get('/surat-keluar/lihat-file/' . $encryptedDummy)
            ->assertStatus(403);

        $this->actingAs($userWithoutPermission)
            ->get('/surat-keluar/lihat-surat-duplikat/dummy.pdf')
            ->assertStatus(403);

        $this->actingAs($userWithoutPermission)
            ->get('/surat-keluar/unduh-surat-duplikat/dummy.pdf')
            ->assertStatus(403);

        $userWithoutPermission->delete();
    }

    /**
     * Test authorized admin can view Surat Masuk scan file from private storage.
     */
    public function test_admin_can_view_inbox_file_from_private_storage(): void
    {
        $admin = User::role('administrator')->first();
        if (!$admin) {
            $this->markTestSkipped('No administrator found.');
        }

        // Use migrated file in private storage
        $filename = '019c78ad-60b2-7380-9410-161173bf446d_sanitized_20260220083234.jpg';
        $encrypted = Crypt::encryptString($filename);

        $response = $this->actingAs($admin)->get('/surat-masuk/lihat-file/' . $encrypted);
        $response->assertStatus(200);
        $this->assertStringContainsString('image/jpeg', $response->headers->get('Content-Type'));
    }

    /**
     * Test authorized admin can view Surat Keluar scan file from private storage.
     */
    public function test_admin_can_view_outbox_file_from_private_storage(): void
    {
        $admin = User::role('administrator')->first();
        if (!$admin) {
            $this->markTestSkipped('No administrator found.');
        }

        // Use migrated file in private storage
        $filename = '019c932f-782e-7353-b407-b4af75bda6aa_sanitized_20260225120450.pdf';
        $encrypted = Crypt::encryptString($filename);

        $response = $this->actingAs($admin)->get('/surat-keluar/lihat-file/' . $encrypted);
        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
    }

    /**
     * Test authorized admin can view and download duplikat from private storage.
     */
    public function test_admin_can_view_and_download_duplikat_from_private_storage(): void
    {
        $admin = User::role('administrator')->first();
        if (!$admin) {
            $this->markTestSkipped('No administrator found.');
        }

        // Find an existing file in duplikat
        $files = File::files(storage_path('app/private/duplikat'));
        if (empty($files)) {
            $this->markTestSkipped('No duplikat files found.');
        }

        $filename = $files[0]->getFilename();

        $viewResponse = $this->actingAs($admin)->get('/surat-keluar/lihat-surat-duplikat/' . $filename);
        $viewResponse->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $viewResponse->headers->get('Content-Type'));

        $downloadResponse = $this->actingAs($admin)->get('/surat-keluar/unduh-surat-duplikat/' . $filename);
        $downloadResponse->assertStatus(200);
        $this->assertStringContainsString('attachment', $downloadResponse->headers->get('Content-Disposition') ?? '');
    }

    /**
     * Test directory traversal and malformed inputs return 404.
     */
    public function test_directory_traversal_and_malformed_inputs_return_404(): void
    {
        $admin = User::role('administrator')->first();
        if (!$admin) {
            $this->markTestSkipped('No administrator found.');
        }

        // Invalid encrypted token
        $this->actingAs($admin)->get('/surat-masuk/lihat-file/invalid-token')
            ->assertStatus(404);

        // Path traversal payload in duplikat
        $this->actingAs($admin)->get('/surat-keluar/lihat-surat-duplikat/..%2F..%2F.env')
            ->assertStatus(404);

        $this->actingAs($admin)->get('/surat-keluar/unduh-surat-duplikat/..%2F..%2F.env')
            ->assertStatus(404);
    }

    /**
     * Test FileUploadService saves image directly into storage/app/private.
     */
    public function test_file_upload_service_saves_image_to_private_storage(): void
    {
        /** @var FileUploadService $fileService */
        $fileService = app(FileUploadService::class);

        $file = UploadedFile::fake()->image('test_scan.jpg', 600, 800);
        $testId = 'test-uuid-image';

        $savedName = $fileService->upload($file, $testId, 'suratmasuk');

        $this->assertNotNull($savedName);
        $expectedPath = storage_path("app/private/suratmasuk/{$savedName}");

        $this->assertFileExists($expectedPath);
        $this->assertFalse(File::exists(public_path("datas/uploads/suratmasuk/{$savedName}")));

        // Verify resolver finds it
        $resolved = $fileService->resolveFilePath($savedName, 'suratmasuk');
        $this->assertEquals($expectedPath, $resolved);

        // Clean up
        $fileService->deleteFile($savedName, 'suratmasuk');
        $this->assertFileDoesNotExist($expectedPath);
    }

    /**
     * Test FileUploadService saves Surat Keluar files directly into storage/app/private/suratkeluar.
     */
    public function test_file_upload_service_saves_outbox_file_to_private_storage(): void
    {
        /** @var FileUploadService $fileService */
        $fileService = app(FileUploadService::class);

        $file = UploadedFile::fake()->image('test_outbox_scan.png', 500, 500);
        $testId = 'test-uuid-outbox';

        $savedName = $fileService->upload($file, $testId, 'suratkeluar');

        $this->assertNotNull($savedName);
        $expectedPath = storage_path("app/private/suratkeluar/{$savedName}");

        $this->assertFileExists($expectedPath);
        $this->assertFalse(File::exists(public_path("datas/uploads/suratkeluar/{$savedName}")));

        // Verify resolver finds it
        $resolved = $fileService->resolveFilePath($savedName, 'suratkeluar');
        $this->assertEquals($expectedPath, $resolved);

        // Clean up
        $fileService->deleteFile($savedName, 'suratkeluar');
        $this->assertFileDoesNotExist($expectedPath);
    }
}
