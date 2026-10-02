<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class FileUploadValidationTest extends TestCase
{
    public function test_inbox_and_outbox_views_contain_sweetalert_file_validation_script(): void
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('Tidak ada user untuk autentikasi');
        }

        $viewsToCheck = [
            route('inbox.create'),
            route('outbox.create'),
        ];

        foreach ($viewsToCheck as $url) {
            $response = $this->actingAs($user)->get($url);
            $response->assertStatus(200);

            $content = $response->getContent();
            $this->assertStringContainsString('validateScanFile', $content, "Halaman {$url} harus memuat fungsi validateScanFile");
            $this->assertStringContainsString('10485760', $content, "Halaman {$url} harus memuat batas ukuran 10MB (10485760 bytes)");
            $this->assertStringContainsString('Swal.fire', $content, "Halaman {$url} harus memuat pemanggilan Swal.fire");
            $this->assertStringContainsString('Toast.fire', $content, "Halaman {$url} harus memuat pemanggilan Toast.fire");
        }
    }

    public function test_backend_validation_rejects_disallowed_file_types(): void
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('Tidak ada user untuk autentikasi');
        }

        // Test invalid file type (.txt)
        $invalidFile = UploadedFile::fake()->create('dokumen_rahasia.txt', 500, 'text/plain');

        $responseInbox = $this->actingAs($user)->post(route('inbox.store'), [
            'is_scan' => $invalidFile,
        ]);
        $responseInbox->assertSessionHasErrors(['is_scan']);

        $responseOutbox = $this->actingAs($user)->post(route('outbox.store'), [
            'is_scan' => $invalidFile,
        ]);
        $responseOutbox->assertSessionHasErrors(['is_scan']);
    }

    public function test_backend_validation_rejects_oversized_files(): void
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('Tidak ada user untuk autentikasi');
        }

        // 11 MB file exceeds 10240 KB limit
        $oversizedFile = UploadedFile::fake()->create('dokumen_besar.pdf', 11500, 'application/pdf');

        $responseInbox = $this->actingAs($user)->post(route('inbox.store'), [
            'is_scan' => $oversizedFile,
        ]);
        $responseInbox->assertSessionHasErrors(['is_scan']);

        $responseOutbox = $this->actingAs($user)->post(route('outbox.store'), [
            'is_scan' => $oversizedFile,
        ]);
        $responseOutbox->assertSessionHasErrors(['is_scan']);
    }
}
