<?php

namespace App\Services;

use App\Models\DataUnit;
use App\Models\Inbox;
use App\Models\Klasifikasi;
use App\Models\MediaSurat;
use App\Models\Outbox;
use App\Models\Perkembangan;
use App\Models\SifatSurat;
use App\Models\TempatBerkas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LegacyMigrationService
{
    protected array $klasifikasiMap = [];
    protected array $mediaMap = [];
    protected array $sifatMap = [];
    protected array $tempatMap = [];
    protected array $perkembanganMap = [];
    protected array $unitMap = [];
    protected ?User $defaultUser = null;

    /**
     * Inisialisasi lookup in-memory untuk kecepatan maksimal.
     */
    public function preloadLookups(): void
    {
        $this->klasifikasiMap = Klasifikasi::pluck('id', 'klas3')->toArray();
        $this->mediaMap = MediaSurat::pluck('id', 'nama')->toArray();
        $this->sifatMap = SifatSurat::pluck('id', 'nama_sifat')->toArray();
        $this->tempatMap = TempatBerkas::pluck('id', 'nama')->toArray();
        $this->perkembanganMap = Perkembangan::pluck('id', 'nama')->toArray();
        $this->unitMap = DataUnit::pluck('id', 'nama_unit')->toArray();

        // Default user untuk pencatat arsip: prioritaskan TU Umum (level 9), lalu Administrator (level 1)
        $this->defaultUser = User::where('level', 9)->first() 
            ?? User::where('level', 1)->first() 
            ?? User::first();
    }

    /**
     * Migrasi file .sql (MySQL dump) atau file .csv legacy.
     *
     * @param string $filePath Path ke berkas SQL atau CSV
     * @param string $type Filter tipe: 'all', 'masuk', atau 'keluar'
     * @param bool $dryRun Jika true, hanya hitung dan validasi tanpa simpan
     * @param callable|null $onProgress Callback progress: fn(int $current, string $status)
     * @return array Hasil migrasi [total, masuk_success, keluar_success, skipped, errors]
     */
    public function migrateFile(string $filePath, string $type = 'all', bool $dryRun = false, ?callable $onProgress = null): array
    {
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '512M');

        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("Berkas [{$filePath}] tidak ditemukan.");
        }

        $this->preloadLookups();

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if ($ext === 'csv') {
            return $this->processCsvFile($filePath, $type, $dryRun, $onProgress);
        }

        return $this->processSqlFile($filePath, $type, $dryRun, $onProgress);
    }

    /**
     * Memproses berkas SQL mentah (streaming parser).
     */
    protected function processSqlFile(string $filePath, string $type, bool $dryRun, ?callable $onProgress): array
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \RuntimeException("Gagal membuka berkas [{$filePath}].");
        }

        $columns = [
            'poenx', 'NO', 'SERIDPA', 'KD_WILAYAH', 'WILAYAH', 'KDINSTANSI', 'NAMAINSTANSI', 'drkpd', 'NAMAKOTA',
            'KLAS3', 'ISI', 'BULAN', 'TAHUN', 'masalahjra', 'MEDIA', 'JENISSURAT', 'AKTIF', 'INAKTIF', 'NILAIGUNA',
            'KETJRA', 'KODEOPR', 'NAMAOPR', 'TGLTERIMA', 'TTD', 'PIMPINAN', 'NOURUT', 'NOAGENDA', 'noagenda2',
            'NOSURAT', 'TGLSURAT', 'NAMABERKAS', 'MASALAH', 'TMPTBERKAS', 'TK_PERKEMBANGAN', 'PERIHAL', 'CATATAN',
            'TGLTERUS', 'KODEUP', 'NAMAUP', 'THAKTIF', 'THINAKTIF', 'pinjam', 'BALAS', 'TGLBALAS', 'TGLENTRY',
            'JAM', 'SIFAT_SURAT', 'NO_SISIP', 'nodef', 'tdt', 'gambar1', 'gambar2', 'gambar3', 'gambar4', 'gambar5',
            'pdf', 'status'
        ];

        $totalParsed = 0;
        $masukBatch = [];
        $keluarBatch = [];
        $masukSuccess = 0;
        $keluarSuccess = 0;
        $skipped = 0;
        $errors = [];

        $batchSize = 250;

        while (($line = fgets($handle)) !== false) {
            $trimmed = trim($line);
            if (!str_starts_with($trimmed, "INSERT INTO `aktif` VALUES (")) {
                continue;
            }

            $totalParsed++;
            $valContent = substr($trimmed, strlen("INSERT INTO `aktif` VALUES ("));
            if (str_ends_with($valContent, ");")) {
                $valContent = substr($valContent, 0, -2);
            }

            $tokens = $this->parseSqlValues($valContent);
            if (count($tokens) < count($columns)) {
                $skipped++;
                continue;
            }

            $row = array_combine(array_slice($columns, 0, count($tokens)), $tokens);
            $jenis = trim($row['JENISSURAT'] ?? '');
            $poenx = trim($row['poenx'] ?? '');

            $isMasuk = ($jenis === 'Masuk' || str_starts_with($poenx, 'M'));
            $isKeluar = ($jenis === 'Keluar' || str_starts_with($poenx, 'K'));

            if ($isMasuk && ($type === 'all' || $type === 'masuk')) {
                $masukBatch[] = $this->transformToInboxRow($row);
                if (count($masukBatch) >= $batchSize) {
                    if (!$dryRun) {
                        DB::table('inboxes')->insert($masukBatch);
                    }
                    $masukSuccess += count($masukBatch);
                    $masukBatch = [];
                }
            } elseif ($isKeluar && ($type === 'all' || $type === 'keluar')) {
                $keluarBatch[] = $this->transformToOutboxRow($row);
                if (count($keluarBatch) >= $batchSize) {
                    if (!$dryRun) {
                        DB::table('outboxes')->insert($keluarBatch);
                    }
                    $keluarSuccess += count($keluarBatch);
                    $keluarBatch = [];
                }
            } else {
                $skipped++;
            }

            if ($onProgress && $totalParsed % 250 === 0) {
                $onProgress($totalParsed, "Memproses data ke-{$totalParsed}...");
            }
        }

        fclose($handle);

        // Sisa batch
        if (!empty($masukBatch)) {
            if (!$dryRun) {
                DB::table('inboxes')->insert($masukBatch);
            }
            $masukSuccess += count($masukBatch);
        }

        if (!empty($keluarBatch)) {
            if (!$dryRun) {
                DB::table('outboxes')->insert($keluarBatch);
            }
            $keluarSuccess += count($keluarBatch);
        }

        return [
            'total_parsed'   => $totalParsed,
            'masuk_success'  => $masukSuccess,
            'keluar_success' => $keluarSuccess,
            'skipped'        => $skipped,
            'dry_run'        => $dryRun,
            'errors'         => $errors,
        ];
    }

    /**
     * Memproses berkas CSV terpisah.
     */
    protected function processCsvFile(string $filePath, string $type, bool $dryRun, ?callable $onProgress): array
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \RuntimeException("Gagal membuka file CSV [{$filePath}].");
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return ['total_parsed' => 0, 'masuk_success' => 0, 'keluar_success' => 0, 'skipped' => 0, 'dry_run' => $dryRun, 'errors' => []];
        }

        $isKeluarFile = in_array('kepada', $header, true);
        $totalParsed = 0;
        $batch = [];
        $masukSuccess = 0;
        $keluarSuccess = 0;
        $batchSize = 250;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($header)) {
                continue;
            }
            $totalParsed++;
            $data = array_combine($header, $row);

            if ($isKeluarFile && ($type === 'all' || $type === 'keluar')) {
                $batch[] = $this->transformCsvToOutboxRow($data);
                if (count($batch) >= $batchSize) {
                    if (!$dryRun) DB::table('outboxes')->insert($batch);
                    $keluarSuccess += count($batch);
                    $batch = [];
                }
            } elseif (!$isKeluarFile && ($type === 'all' || $type === 'masuk')) {
                $batch[] = $this->transformCsvToInboxRow($data);
                if (count($batch) >= $batchSize) {
                    if (!$dryRun) DB::table('inboxes')->insert($batch);
                    $masukSuccess += count($batch);
                    $batch = [];
                }
            }

            if ($onProgress && $totalParsed % 250 === 0) {
                $onProgress($totalParsed, "Memproses baris ke-{$totalParsed}...");
            }
        }

        fclose($handle);

        if (!empty($batch)) {
            if (!$dryRun) {
                if ($isKeluarFile) DB::table('outboxes')->insert($batch);
                else DB::table('inboxes')->insert($batch);
            }
            if ($isKeluarFile) $keluarSuccess += count($batch);
            else $masukSuccess += count($batch);
        }

        return [
            'total_parsed'   => $totalParsed,
            'masuk_success'  => $masukSuccess,
            'keluar_success' => $keluarSuccess,
            'skipped'        => 0,
            'dry_run'        => $dryRun,
            'errors'         => [],
        ];
    }

    /**
     * Parse nilai-nilai SQL INSERT (memperhatikan single-quotes, escape \', dan NULL).
     */
    protected function parseSqlValues(string $valStr): array
    {
        $values = [];
        $i = 0;
        $n = strlen($valStr);

        while ($i < $n) {
            while ($i < $n && ($valStr[$i] === ' ' || $valStr[$i] === "\t" || $valStr[$i] === "\r" || $valStr[$i] === "\n")) {
                $i++;
            }
            if ($i >= $n) break;

            if ($valStr[$i] === "'") {
                $i++;
                $s = '';
                while ($i < $n) {
                    if ($valStr[$i] === '\\' && $i + 1 < $n) {
                        $s .= $valStr[$i + 1];
                        $i += 2;
                    } elseif ($valStr[$i] === "'") {
                        if ($i + 1 < $n && $valStr[$i + 1] === "'") {
                            $s .= "'";
                            $i += 2;
                        } else {
                            $i++;
                            break;
                        }
                    } else {
                        $s .= $valStr[$i];
                        $i++;
                    }
                }
                $values[] = $s;
                while ($i < $n && ($valStr[$i] === ' ' || $valStr[$i] === "\t" || $valStr[$i] === "\r" || $valStr[$i] === "\n")) {
                    $i++;
                }
                if ($i < $n && $valStr[$i] === ',') {
                    $i++;
                }
            } else {
                $j = $i;
                while ($j < $n && $valStr[$j] !== ',') {
                    $j++;
                }
                $token = trim(substr($valStr, $i, $j - $i));
                if (strtoupper($token) === 'NULL') {
                    $values[] = null;
                } else {
                    $values[] = $token;
                }
                $i = $j + 1;
            }
        }

        return $values;
    }

    /**
     * Konversi baris data mentah aktif ke array kolom tabel inboxes.
     */
    protected function transformToInboxRow(array $r): array
    {
        $noAgenda = intval($r['NOAGENDA'] ?? $r['noagenda2'] ?? $r['NOURUT'] ?? 1);
        if ($noAgenda <= 0) $noAgenda = 1;

        $noSurat = trim($r['NOSURAT'] ?? '');
        if ($noSurat === '') $noSurat = "AGENDA-{$noAgenda}";

        $dari = trim($r['drkpd'] ?? $r['NAMAINSTANSI'] ?? 'Instansi Luar');
        if ($dari === '') $dari = 'Instansi Luar';

        $wilayah = trim($r['WILAYAH'] ?? $r['NAMAKOTA'] ?? 'Karanganyar');
        if ($wilayah === '') $wilayah = 'Karanganyar';

        $perihal = trim($r['PERIHAL'] ?? $r['ISI'] ?? 'Perihal Surat');
        if ($perihal === '') $perihal = 'Surat Masuk';

        $isi = trim($r['ISI'] ?? $r['PERIHAL'] ?? '-');
        if ($isi === '') $isi = '-';

        $tglSurat = $this->cleanDate($r['TGLSURAT'] ?? null);
        $tglTerima = $this->cleanDate($r['TGLTERIMA'] ?? null, $tglSurat);
        $year = intval($r['TAHUN'] ?? date('Y', strtotime($tglSurat)));
        if ($year < 1970 || $year > 2050) $year = intval(date('Y', strtotime($tglSurat)));

        // Lookups
        $idKlasifikasi = $this->resolveKlasifikasiId($r['KLAS3'] ?? null);
        $idMedia = 1; // 'Teks'
        $sifatId = $this->resolveSifatId($r['SIFAT_SURAT'] ?? null);
        $tempatId = $this->resolveTempatId($r['TMPTBERKAS'] ?? null);
        $perkembanganId = $this->resolvePerkembanganId($r['TK_PERKEMBANGAN'] ?? null);

        // Tindakan
        $balas = strtolower(trim($r['BALAS'] ?? ''));
        $tindakan = (str_contains($balas, 'balas') && !str_contains($balas, 'non')) ? 'balas' : 'non balas';

        $tglBalas = $this->cleanDateTime($r['TGLBALAS'] ?? null);
        $createdAt = $this->cleanDateTime(($r['TGLENTRY'] ?? '') . ' ' . ($r['JAM'] ?? '')) ?? $tglTerima . ' 00:00:00';

        $defaultUuid = $this->defaultUser ? $this->defaultUser->uuid : Str::uuid7()->toString();

        return [
            'uuid'              => Str::uuid7()->toString(),
            'no_agenda'         => $noAgenda,
            'nama_berkas'       => $r['NAMABERKAS'] ?: ($r['masalahjra'] ?: null),
            'no_surat'          => $noSurat,
            'dari'              => $dari,
            'wilayah'           => $wilayah,
            'perihal'           => $perihal,
            'isi_surat'         => $isi,
            'tgl_surat'         => $tglSurat,
            'tgl_diterima'      => $tglTerima,
            'year'              => $year,
            'id_media'          => $idMedia,
            'id_klasifikasi'    => $idKlasifikasi,
            'sifat_surat'       => $sifatId,
            'tempat_berkas'     => $tempatId,
            'id_perkembangan'   => $perkembanganId,
            'posisi_surat'      => $defaultUuid,
            'jml_lampiran'      => null,
            'tindakan'          => $tindakan,
            'tgl_balas'         => $tglBalas,
            'softcopy'          => $r['pdf'] ?: ($r['gambar1'] ?: null),
            'file_download'     => null,
            'keterangan'        => $r['CATATAN'] ?: ($r['masalahjra'] ?: null),
            'level_surat'       => 9, // TU Umum
            'status_surat'      => 'selesai',
            'is_primary_agenda' => true,
            'created_by'        => $defaultUuid,
            'created_at'        => $createdAt,
            'updated_at'        => $createdAt,
        ];
    }

    /**
     * Konversi baris data mentah aktif ke array kolom tabel outboxes.
     */
    protected function transformToOutboxRow(array $r): array
    {
        $noAgenda = intval($r['NOAGENDA'] ?? $r['noagenda2'] ?? 1);
        if ($noAgenda <= 0) $noAgenda = 1;

        $noSurat = trim($r['NOSURAT'] ?? '');
        if ($noSurat === '') $noSurat = "AGENDA-K-{$noAgenda}";

        $kepada = trim($r['drkpd'] ?? $r['NAMAINSTANSI'] ?? 'Penerima Surat');
        if ($kepada === '') $kepada = 'Penerima Surat';

        $wilayah = trim($r['WILAYAH'] ?? $r['NAMAKOTA'] ?? 'Karanganyar');
        if ($wilayah === '') $wilayah = 'Karanganyar';

        $perihal = trim($r['PERIHAL'] ?? $r['ISI'] ?? 'Surat Keluar');
        if ($perihal === '') $perihal = 'Surat Keluar';

        $isi = trim($r['ISI'] ?? $r['PERIHAL'] ?? '-');
        if ($isi === '') $isi = '-';

        $tglSurat = $this->cleanDate($r['TGLSURAT'] ?? null);
        $tglNaik = $this->cleanDate($r['TGLTERIMA'] ?? null, $tglSurat);
        $tglDiteruskan = $this->cleanDate($r['TGLTERUS'] ?? null);
        $year = intval($r['TAHUN'] ?? date('Y', strtotime($tglSurat)));
        if ($year < 1970 || $year > 2050) $year = intval(date('Y', strtotime($tglSurat)));

        // Lookups
        $idKlasifikasi = $this->resolveKlasifikasiId($r['KLAS3'] ?? null);
        $idMedia = 1;
        $sifatId = $this->resolveSifatId($r['SIFAT_SURAT'] ?? null);
        $tempatId = $this->resolveTempatId($r['TMPTBERKAS'] ?? null);
        $perkembanganId = $this->resolvePerkembanganId($r['TK_PERKEMBANGAN'] ?? null);
        $idUnit = $this->resolveUnitId($r['NAMAUP'] ?? null);

        $createdAt = $this->cleanDateTime(($r['TGLENTRY'] ?? '') . ' ' . ($r['JAM'] ?? '')) ?? $tglSurat . ' 00:00:00';
        $defaultUuid = $this->defaultUser ? $this->defaultUser->uuid : Str::uuid7()->toString();

        return [
            'uuid'              => Str::uuid7()->toString(),
            'no_agenda'         => $noAgenda,
            'nama_berkas'       => $r['NAMABERKAS'] ?: ($r['masalahjra'] ?: null),
            'no_surat'          => $noSurat,
            'kepada'            => $kepada,
            'wilayah'           => $wilayah,
            'perihal'           => $perihal,
            'isi_surat'         => $isi,
            'tgl_surat'         => $tglSurat,
            'tgl_naik'          => $tglNaik,
            'tgl_diteruskan'    => $tglDiteruskan,
            'year'              => $year,
            'id_media'          => $idMedia,
            'id_klasifikasi'    => $idKlasifikasi,
            'id_unit'           => $idUnit,
            'unit'              => $r['NAMAUP'] ?: null,
            'sifat_surat'       => $sifatId,
            'tempat_berkas'     => $tempatId,
            'id_perkembangan'   => $perkembanganId,
            'id_spd'            => null,
            'lampiran'          => null,
            'softcopy'          => $r['pdf'] ?: ($r['gambar1'] ?: null),
            'file_download'     => null,
            'keterangan'        => $r['CATATAN'] ?: null,
            'level_surat'       => 2, // Bagian Umum
            'is_primary_agenda' => true,
            'created_by'        => $defaultUuid,
            'on_delete'         => null,
            'created_at'        => $createdAt,
            'updated_at'        => $createdAt,
        ];
    }

    protected function transformCsvToInboxRow(array $d): array
    {
        $defaultUuid = $this->defaultUser ? $this->defaultUser->uuid : Str::uuid7()->toString();
        return [
            'uuid'              => Str::uuid7()->toString(),
            'no_agenda'         => intval($d['no_agenda'] ?? 1),
            'nama_berkas'       => $d['nama_berkas'] ?: null,
            'no_surat'          => $d['no_surat'] ?? 'AGENDA',
            'dari'              => $d['dari'] ?? 'Instansi Luar',
            'wilayah'           => $d['wilayah'] ?? 'Karanganyar',
            'perihal'           => $d['perihal'] ?? 'Surat Masuk',
            'isi_surat'         => $d['isi_surat'] ?? '-',
            'tgl_surat'         => $d['tgl_surat'] ?? date('Y-m-d'),
            'tgl_diterima'      => $d['tgl_diterima'] ?? date('Y-m-d'),
            'year'              => intval($d['year'] ?? date('Y')),
            'id_media'          => 1,
            'id_klasifikasi'    => $this->resolveKlasifikasiId($d['klas3'] ?? null),
            'sifat_surat'       => $this->resolveSifatId($d['sifat'] ?? null),
            'tempat_berkas'     => $this->resolveTempatId($d['tempat_berkas'] ?? null),
            'id_perkembangan'   => 1,
            'posisi_surat'      => $defaultUuid,
            'jml_lampiran'      => null,
            'tindakan'          => $d['tindakan'] ?? 'non balas',
            'tgl_balas'         => null,
            'softcopy'          => null,
            'file_download'     => null,
            'keterangan'        => $d['keterangan'] ?: null,
            'level_surat'       => 9,
            'status_surat'      => 'selesai',
            'is_primary_agenda' => true,
            'created_by'        => $defaultUuid,
            'created_at'        => $d['created_at'] ?? now()->toDateTimeString(),
            'updated_at'        => $d['created_at'] ?? now()->toDateTimeString(),
        ];
    }

    protected function transformCsvToOutboxRow(array $d): array
    {
        $defaultUuid = $this->defaultUser ? $this->defaultUser->uuid : Str::uuid7()->toString();
        return [
            'uuid'              => Str::uuid7()->toString(),
            'no_agenda'         => intval($d['no_agenda'] ?? 1),
            'nama_berkas'       => $d['nama_berkas'] ?: null,
            'no_surat'          => $d['no_surat'] ?? 'AGENDA-K',
            'kepada'            => $d['kepada'] ?? 'Penerima Surat',
            'wilayah'           => $d['wilayah'] ?? 'Karanganyar',
            'perihal'           => $d['perihal'] ?? 'Surat Keluar',
            'isi_surat'         => $d['isi_surat'] ?? '-',
            'tgl_surat'         => $d['tgl_surat'] ?? date('Y-m-d'),
            'tgl_naik'          => $d['tgl_naik'] ?: null,
            'tgl_diteruskan'    => $d['tgl_diteruskan'] ?: null,
            'year'              => intval($d['year'] ?? date('Y')),
            'id_media'          => 1,
            'id_klasifikasi'    => $this->resolveKlasifikasiId($d['klas3'] ?? null),
            'id_unit'           => $this->resolveUnitId($d['unit'] ?? null),
            'unit'              => $d['unit'] ?: null,
            'sifat_surat'       => $this->resolveSifatId($d['sifat'] ?? null),
            'tempat_berkas'     => $this->resolveTempatId($d['tempat_berkas'] ?? null),
            'id_perkembangan'   => 1,
            'id_spd'            => null,
            'lampiran'          => null,
            'softcopy'          => null,
            'file_download'     => null,
            'keterangan'        => $d['keterangan'] ?: null,
            'level_surat'       => 2,
            'is_primary_agenda' => true,
            'created_by'        => $defaultUuid,
            'on_delete'         => null,
            'created_at'        => $d['created_at'] ?? now()->toDateTimeString(),
            'updated_at'        => $d['created_at'] ?? now()->toDateTimeString(),
        ];
    }

    protected function resolveKlasifikasiId(?string $code): ?int
    {
        if (empty($code)) return null;
        $trimmed = trim($code);
        return $this->klasifikasiMap[$trimmed] ?? null;
    }

    protected function resolveSifatId(?string $name): int
    {
        if (empty($name)) return 1;
        $trimmed = trim($name);
        if ($trimmed === 'Sangat_Segera') $trimmed = 'Sangat Segera';
        return $this->sifatMap[$trimmed] ?? 1;
    }

    protected function resolveTempatId(?string $name): int
    {
        if (empty($name)) return 2; // Filling
        $trimmed = trim($name);
        return $this->tempatMap[$trimmed] ?? 2;
    }

    protected function resolvePerkembanganId(?string $name): int
    {
        if (empty($name)) return 1; // Asli
        $trimmed = trim($name);
        if ($trimmed === 'Faximile') return 5;
        return $this->perkembanganMap[$trimmed] ?? 1;
    }

    protected function resolveUnitId(?string $name): ?int
    {
        if (empty($name)) return null;
        $trimmed = trim($name);
        return $this->unitMap[$trimmed] ?? null;
    }

    protected function cleanDate(?string $raw, ?string $fallback = null): string
    {
        if (empty($raw) || str_starts_with($raw, '0000-00-00')) {
            return $fallback ?? date('Y-m-d');
        }
        $parts = explode(' ', trim($raw));
        $date = $parts[0];
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }
        return $fallback ?? date('Y-m-d');
    }

    protected function cleanDateTime(?string $raw): ?string
    {
        if (empty($raw) || str_starts_with($raw, '0000-00-00')) {
            return null;
        }
        try {
            return Carbon::parse($raw)->format('Y-m-d H:i:s');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
