<?php

namespace Tests\Feature;

use App\Services\FileUploadService;
use App\Services\PdfSanitizer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Tests\TestCase;

class PdfSanitizationTest extends TestCase
{
    public function test_ghostscript_binary_is_detected(): void
    {
        $sanitizer = app(PdfSanitizer::class);
        $binary = $sanitizer->getGhostscriptBinary();

        $this->assertNotEmpty($binary, "Binary Ghostscript harus terdeteksi.");
    }

    public function test_pdf_sanitization_works_for_file_above_3mb(): void
    {
        $sanitizer = app(PdfSanitizer::class);

        // Generate genuine multi-page PDF >= 3.5 MB
        $fpdf = new \FPDF('P', 'mm', 'A4');
        $imgFiles = [];

        for ($page = 1; $page <= 7; $page++) {
            $imgFile = storage_path("app/tmp/test_gs_page_{$page}.jpg");
            $img = imagecreatetruecolor(1800, 2500);
            for ($y = 0; $y < 2500; $y += 25) {
                $col = imagecolorallocate($img, rand(0, 255), rand(0, 255), rand(0, 255));
                imagefilledrectangle($img, 0, $y, 1800, $y + 25, $col);
            }
            imagejpeg($img, $imgFile, 95);
            imagedestroy($img);
            $imgFiles[] = $imgFile;

            $fpdf->AddPage();
            $fpdf->Image($imgFile, 0, 0, 210, 297);
        }

        $inputPdf = storage_path('app/tmp/test_large_input_4mb.pdf');
        $fpdf->Output('F', $inputPdf);

        foreach ($imgFiles as $f) {
            @unlink($f);
        }

        $fileSizeMB = round(filesize($inputPdf) / (1024 * 1024), 2);
        $this->assertGreaterThanOrEqual(3.0, $fileSizeMB, "File pengujian harus berukuran >= 3.0 MB");

        $outputPdf = storage_path('app/tmp/test_large_output_sanitized.pdf');

        try {
            $start = microtime(true);
            $sanitizer->sanitize($inputPdf, $outputPdf);
            $duration = microtime(true) - $start;

            $this->assertFileExists($outputPdf, "File PDF hasil sanitasi harus terbentuk.");
            $this->assertGreaterThan(0, filesize($outputPdf), "Ukuran file sanitasi tidak boleh 0.");
            $this->assertLessThan(60, $duration, "Proses sanitasi berkas >= 3MB harus selesai di bawah 60 detik.");

            // Verify it is a valid PDF
            $header = file_get_contents($outputPdf, false, null, 0, 5);
            $this->assertEquals('%PDF-', $header, "Berkas hasil harus berheader PDF.");
        } finally {
            @unlink($inputPdf);
            @unlink($outputPdf);
        }
    }

    public function test_file_upload_service_sanitizes_and_saves_large_pdf(): void
    {
        $fileService = app(FileUploadService::class);

        // Generate genuine multi-page PDF >= 3.5 MB
        $fpdf = new \FPDF('P', 'mm', 'A4');
        $imgFiles = [];

        for ($page = 1; $page <= 7; $page++) {
            $imgFile = storage_path("app/tmp/test_upload_page_{$page}.jpg");
            $img = imagecreatetruecolor(1800, 2500);
            for ($y = 0; $y < 2500; $y += 25) {
                $col = imagecolorallocate($img, rand(0, 255), rand(0, 255), rand(0, 255));
                imagefilledrectangle($img, 0, $y, 1800, $y + 25, $col);
            }
            imagejpeg($img, $imgFile, 95);
            imagedestroy($img);
            $imgFiles[] = $imgFile;

            $fpdf->AddPage();
            $fpdf->Image($imgFile, 0, 0, 210, 297);
        }

        $inputPdf = storage_path('app/tmp/test_upload_large_3mb.pdf');
        $fpdf->Output('F', $inputPdf);

        foreach ($imgFiles as $f) {
            @unlink($f);
        }

        $uploadedFile = new UploadedFile(
            $inputPdf,
            'dokumen_lampiran_3mb.pdf',
            'application/pdf',
            null,
            true // test mode
        );

        $uuid = (string) Str::uuid();

        try {
            $savedName = $fileService->upload($uploadedFile, $uuid, 'suratmasuk');

            $this->assertNotNull($savedName, "Upload berkas PDF >= 3MB tidak boleh gagal/null");
            $this->assertStringContainsString('_sanitized_', $savedName);
            $this->assertStringEndsWith('.pdf', $savedName);

            $resolved = $fileService->resolveFilePath($savedName, 'suratmasuk');
            $this->assertNotNull($resolved);
            $this->assertFileExists($resolved);

            // Cleanup
            $fileService->deleteFile($savedName, 'suratmasuk');
        } finally {
            @unlink($inputPdf);
        }
    }
}
