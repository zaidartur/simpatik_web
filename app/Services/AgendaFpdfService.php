<?php

namespace App\Services;

class AgendaFpdfService extends \FPDF
{
    public ?string $jenis = 'Semua';
    public string $range = '';
    public string $instansi = 'SEKRETARIAT DAERAH';
    public array $columns = [];
    public array $colWidths = [];
    public array $colAligns = [];
    protected $currentUser = null;

    public function __construct(string $orientation = 'L', string $unit = 'mm', array $size = [215.9, 355.6])
    {
        parent::__construct($orientation, $unit, $size);
        $this->AliasNbPages();
        $this->SetMargins(10, 10, 10);
        $this->SetAutoPageBreak(false);
    }

    /**
     * Header halaman: Halaman 1 mencetak Kop Surat resmi identik DomPDF + Header Tabel.
     * Halaman 2 dan seterusnya hanya mencetak Header Tabel (mengikuti thead DomPDF).
     */
    public function Header(): void
    {
        // Abaikan jika kolom belum dikonfigurasi (misal AddPage dipanggil terlalu awal)
        if (empty($this->columns)) {
            return;
        }

        if ($this->PageNo() == 1) {
            // === KOP SURAT (IDENTIK DENGAN LAYOUT DOMPDF) ===
            $logoPath = public_path('templates/images/Lambang_Kabupaten_Karanganyar.png');
            if (file_exists($logoPath)) {
                // Logo di sisi kiri (lebar ~20mm)
                $this->Image($logoPath, 15, 10, 20);
            }

            // Teks Instansi di samping logo
            $this->SetXY(40, 12);
            $this->SetFont('Helvetica', 'B', 15);
            $this->Cell(0, 6, $this->instansi, 0, 1, 'L');

            $this->SetX(40);
            $this->SetFont('Helvetica', 'B', 10.5);
            $this->Cell(0, 5, 'PEMERINTAH KABUPATEN KARANGANYAR', 0, 1, 'L');

            // Judul Agenda di tengah (seperti <h2> di DomPDF)
            $this->SetY(33);
            $this->SetFont('Helvetica', 'B', 13);
            $title = 'Daftar Agenda ' . (empty($this->jenis) || $this->jenis === 'Semua' ? 'Surat Masuk dan Keluar' : 'Surat ' . $this->jenis);
            $this->Cell(0, 6, $title, 0, 1, 'C');

            if (!empty($this->range)) {
                $this->SetFont('Helvetica', 'B', 11);
                $this->Cell(0, 6, $this->range, 0, 1, 'C');
            }
            $this->Ln(3);
        } else {
            // Halaman 2 ke atas dimulai dari margin atas 10mm
            $this->SetY(10);
        }

        // Cetak Header Tabel & Subheader Nomor Kolom
        $this->printTableHeader();
    }

    /**
     * Header tabel dan subheader nomor kolom (1, 2, 3...)
     */
    public function printTableHeader(): void
    {
        $startX = 10;
        $startY = $this->GetY();
        $headerHeight = 12;

        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.2);

        // 1. Header Baris Judul Kolom (Background putih, text-align left seperti DomPDF th)
        $this->SetFont('Helvetica', 'B', 8);
        $currX = $startX;
        foreach ($this->columns as $i => $col) {
            $w = $this->colWidths[$i];
            // Kotak border sel
            $this->Rect($currX, $startY, $w, $headerHeight);
            $this->SetXY($currX + 1.5, $startY + 1.5);
            $this->MultiCell($w - 3, 3.2, $col, 0, 'L');
            $currX += $w;
        }

        // 2. Subheader Penomoran Kolom (1, 2, 3... background #f2f2f2, text-align center)
        $subY = $startY + $headerHeight;
        $subHeight = 5;
        $this->SetFillColor(242, 242, 242);
        $this->SetFont('Helvetica', 'B', 8);

        $currX = $startX;
        foreach ($this->columns as $i => $col) {
            $w = $this->colWidths[$i];
            $this->Rect($currX, $subY, $w, $subHeight, 'DF');
            $this->SetXY($currX, $subY + 0.5);
            $this->Cell($w, 4, strval($i + 1), 0, 0, 'C');
            $currX += $w;
        }

        // Set cursor Y tepat di bawah subheader untuk baris data pertama
        $this->SetXY($startX, $subY + $subHeight);
    }

    /**
     * Footer penomoran halaman
     */
    public function Footer(): void
    {
        $this->SetY(-10);
        $this->SetFont('Helvetica', 'I', 7.5);
        $this->Cell(150, 4, 'Dicetak melalui SIPERMAS pada: ' . date('d-m-Y H:i:s'), 0, 0, 'L');
        $this->Cell(0, 4, 'Halaman ' . $this->PageNo() . ' dari {nb}', 0, 0, 'R');
    }

    /**
     * Cetak satu baris data dengan kalkulasi MultiCell dinamis
     */
    public function Row(array $rowValues): void
    {
        $lineHeight = 3.6;
        $nbLines = [];

        foreach ($rowValues as $i => $txt) {
            $w = $this->colWidths[$i];
            $nbLines[$i] = $this->NbLines($w, $txt);
        }

        $maxLines = max($nbLines);
        $rowHeight = max(6.5, $maxLines * $lineHeight + 2.5);

        // Auto Page Break jika melebihi batas bawah halaman cetak (200mm dari 215.9mm)
        if ($this->GetY() + $rowHeight > 200) {
            $this->AddPage('L', [215.9, 355.6]);
        }

        $startX = 10;
        $startY = $this->GetY();
        $currX = $startX;

        $this->SetFont('Helvetica', '', 8);

        foreach ($rowValues as $i => $txt) {
            $w = $this->colWidths[$i];
            $align = $this->colAligns[$i] ?? 'L';

            // Gambar border kotak sel
            $this->Rect($currX, $startY, $w, $rowHeight);

            // Cetak teks sel dengan padding 1.5mm kiri-kanan
            $this->SetXY($currX + 1.5, $startY + 1.2);
            $this->MultiCell($w - 3, $lineHeight, $txt, 0, $align);

            $currX += $w;
        }

        $this->SetXY($startX, $startY + $rowHeight);
    }

    /**
     * Hitung jumlah baris teks jika dibungkus pada lebar cell $w
     */
    public function NbLines(float $w, string $txt): int
    {
        $w -= 3; // Kurangi padding sel
        $cw = &$this->CurrentFont['cw'];
        if ($w <= 0) return 1;

        $txt = str_replace("\r", '', $txt);
        $lines = explode("\n", $txt);
        $count = 0;

        foreach ($lines as $line) {
            $l = 0;
            $len = strlen($line);
            for ($i = 0; $i < $len; $i++) {
                $c = $line[$i];
                $l += $cw[$c] ?? 500;
                if ($l * $this->FontSize / 1000 > $w) {
                    $count++;
                    $l = $cw[$c] ?? 500;
                }
            }
            $count++;
        }

        return max(1, $count);
    }

    /**
     * Inisialisasi kolom berdasarkan role pengguna dan render seluruh data
     */
    public function build(array $data, ?string $jenis, ?string $range, $user): string
    {
        $this->jenis = $jenis ?: 'Semua';
        $this->range = $range ?? '';
        $this->currentUser = $user;

        // Tentukan teks kop instansi sesuai role (mengikuti logika @role DomPDF)
        if ($user && $user->hasRole('umum')) {
            $this->instansi = 'BAGIAN UMUM DAN KEUANGAN SETDA';
        } else {
            $this->instansi = 'SEKRETARIAT DAERAH';
        }

        // Tentukan kolom dan lebar persis proporsi DomPDF (Lebar cetak: 335.6 mm)
        if ($user && $user->hasRole('administrator')) {
            $this->columns = [
                'NO. AGENDA',
                'KEPADA',
                "TGL. KIRIM /\nTGL. SURAT /\nNO. SURAT",
                "KLASIFIKASI /\nKET. JRA /\nISI INFORMASI",
                'DARI UNIT KERJA',
                'DISPOSISI SEKDA',
                'DISPOSISI WAKIL BUPATI',
                'DISPOSISI BUPATI',
            ];
            // Persentase DomPDF: 5%, 10%, 10%, 24%, 11%, 13%, 13%, 14% -> total 335.6 mm
            $this->colWidths = [18, 33.5, 33.5, 80.5, 37, 43.5, 43.5, 46];
            $this->colAligns = ['L', 'L', 'L', 'L', 'L', 'L', 'L', 'L'];
        } elseif ($user && ($user->hasRole('umum') || $user->hasRole('setda'))) {
            $this->columns = [
                'NO. AGENDA',
                'KEPADA',
                "TGL. KIRIM /\nTGL. SURAT /\nNO. SURAT",
                "KLASIFIKASI /\nKET. JRA /\nISI INFORMASI",
                'DARI UNIT KERJA',
                'DISPOSISI SEKDA',
                'DISPOSISI BUPATI',
            ];
            // Persentase DomPDF: 5%, 10%, 10%, 24%, 11%, 20%, 20% -> total 335.6 mm
            $this->colWidths = [17, 33.5, 33.5, 80.5, 37, 67, 67];
            $this->colAligns = ['L', 'L', 'L', 'L', 'L', 'L', 'L'];
        } elseif ($user && $user->hasRole('wabup')) {
            $this->columns = [
                'NO. AGENDA',
                'KEPADA',
                "TGL. KIRIM /\nTGL. SURAT /\nNO. SURAT",
                "KLASIFIKASI /\nKET. JRA /\nISI INFORMASI",
                'DARI UNIT KERJA',
                'DISPOSISI WAKIL BUPATI',
            ];
            // Persentase DomPDF: 5%, 10%, 10%, 24%, 11%, 40% -> total 335.6 mm
            $this->colWidths = [17, 33.5, 33.5, 80.5, 37, 134];
            $this->colAligns = ['L', 'L', 'L', 'L', 'L', 'L'];
        } else {
            // Role bupati atau lainnya
            $this->columns = [
                'NO. AGENDA',
                'KEPADA',
                "TGL. KIRIM /\nTGL. SURAT /\nNO. SURAT",
                "KLASIFIKASI /\nKET. JRA /\nISI INFORMASI",
                'DARI UNIT KERJA',
                'DISPOSISI BUPATI',
            ];
            // Persentase DomPDF: 5%, 10%, 10%, 24%, 11%, 40% -> total 335.6 mm
            $this->colWidths = [17, 33.5, 33.5, 80.5, 37, 134];
            $this->colAligns = ['L', 'L', 'L', 'L', 'L', 'L'];
        }

        // Tambahkan halaman pertama setelah kolom siap
        $this->AddPage();

        // Helper pembersih HTML menjadi teks terformat baris dengan normalisasi UTF-8 / Word
        $cleanHtml = function($str) {
            if (!$str || $str === '-') return '-';
            $s = str_ireplace(['<br>', '<br/>', '<br />'], "\n", $str);
            $s = str_ireplace(['</p>', '</div>'], "\n", $s);
            $s = strip_tags($s);
            $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');

            // Normalisasi simbol-simbol tipografi Word / UTF-8 ke ASCII standar (anti-garbled text)
            $replacements = [
                // Smart double quotes
                '“' => '"', '”' => '"', '„' => '"', '«' => '"', '»' => '"',
                // Smart single quotes & apostrophes
                '‘' => "'", '’' => "'", '‚' => "'", '`' => "'",
                // Dashes & hyphens
                '–' => '-', '—' => '-', '−' => '-', '‐' => '-',
                // Bullets, dots & ellipses
                '•' => '-', '·' => '-', '…' => '...',
                // Spaces
                "\xc2\xa0" => ' ', // Non-breaking space
            ];
            $s = strtr($s, $replacements);

            // Konversi encoding UTF-8 ke ISO-8859-1 / Windows-1252 yang didukung FPDF secara native
            if (function_exists('iconv')) {
                $converted = @iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $s);
                if ($converted !== false) {
                    $s = $converted;
                }
            }

            $s = preg_replace("/\n{3,}/", "\n\n", trim($s));
            return $s ?: '-';
        };

        foreach ($data as $row) {
            $rowValues = [
                $cleanHtml($row['no_agenda'] ?? '-'),
                $cleanHtml($row['kepada'] ?? '-'),
                $cleanHtml($row['row3'] ?? '-'),
                $cleanHtml($row['row4'] ?? '-'),
                $cleanHtml($row['dari'] ?? '-'),
            ];

            if ($user && $user->hasRole('administrator')) {
                $rowValues[] = $cleanHtml($row['sekda'] ?? '-');
                $rowValues[] = $cleanHtml($row['wakil'] ?? '-');
                $rowValues[] = $cleanHtml($row['bupati'] ?? '-');
            } elseif ($user && ($user->hasRole('umum') || $user->hasRole('setda'))) {
                $rowValues[] = $cleanHtml($row['sekda'] ?? '-');
                $rowValues[] = $cleanHtml($row['bupati'] ?? '-');
            } elseif ($user && $user->hasRole('wabup')) {
                $rowValues[] = $cleanHtml($row['wakil'] ?? '-');
            } else {
                $rowValues[] = $cleanHtml($row['bupati'] ?? '-');
            }

            $this->Row($rowValues);
        }

        return $this->Output('S');
    }
}
