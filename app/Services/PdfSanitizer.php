<?php

namespace App\Services;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class PdfSanitizer
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function sanitize(string $inputPath, string $outputPath): void
    {
        if (! file_exists($inputPath)) {
            throw new \RuntimeException('Input PDF does not exist.');
        }

        $process = new Process([
            'gs',
            '-q',                      // quiet
            '-dNOPAUSE',
            '-dBATCH',
            '-dSAFER',                 // sandbox
            '-sDEVICE=pdfwrite',
            '-dCompatibilityLevel=1.4',
            '-dPDFSETTINGS=/prepress', // high quality, safe rewrite
            '-dDetectDuplicateImages=true',
            '-dCompressFonts=true',
            '-dEmbedAllFonts=true',
            "-sOutputFile={$outputPath}",
            $inputPath,
        ]);

        $process->setTimeout(60);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        if (! file_exists($outputPath) || filesize($outputPath) === 0) {
            throw new \RuntimeException('PDF sanitization failed.');
        }
    }
}
