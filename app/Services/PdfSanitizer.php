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


        $gs = $this->getGhostscriptBinary();

        $process = new Process([
            $gs,
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

        $process->setTimeout(120);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        if (! file_exists($outputPath) || filesize($outputPath) === 0) {
            throw new \RuntimeException('PDF sanitization failed.');
        }
    }

    /**
     * Resolves the Ghostscript binary path dynamically.
     */
    public function getGhostscriptBinary(): string
    {
        $custom = env('GHOSTSCRIPT_PATH');
        if (!empty($custom)) {
            return $custom;
        }

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $knownPaths = [
                'C:\\Program Files\\gs\\gs10.06.0\\bin\\gswin64c.exe',
                'C:\\Program Files\\gs\\gs10.05.0\\bin\\gswin64c.exe',
                'C:\\Program Files\\gs\\gs10.04.0\\bin\\gswin64c.exe',
            ];

            foreach ($knownPaths as $path) {
                if (file_exists($path)) {
                    return $path;
                }
            }

            // Auto-detect any installed Ghostscript version under Program Files
            $wildcards = glob('C:\\Program Files\\gs\\*\\bin\\gswin64c.exe');
            if (!empty($wildcards)) {
                return end($wildcards);
            }

            $wildcards32 = glob('C:\\Program Files (x86)\\gs\\*\\bin\\gswin32c.exe');
            if (!empty($wildcards32)) {
                return end($wildcards32);
            }

            return 'gswin64c.exe';
        }

        return 'gs';
    }
}
