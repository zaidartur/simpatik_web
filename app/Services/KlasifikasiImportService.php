<?php

namespace App\Services;

use App\Models\Klasifikasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class KlasifikasiImportService
{
    protected ReferenceCacheService $cacheService;

    public function __construct(ReferenceCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Import or update classification data from Perbup 2023 JSON file into the database.
     *
     * @param string|null $customPath
     * @param bool $forceOverwriteRetention If true, overwrite r_aktif, r_inaktif, ket_jra, nilai_guna for existing records
     * @param callable|null $onProgress
     * @return array
     */
    public function importPerbup(?string $customPath = null, bool $forceOverwriteRetention = false, ?callable $onProgress = null): array
    {
        $filePath = $customPath ?: database_path('data/klasifikasi_perbup_2023.json');

        if (!File::exists($filePath)) {
            throw new \RuntimeException("Berkas data klasifikasi tidak ditemukan di: {$filePath}");
        }

        $records = json_decode(File::get($filePath), true);

        if (!is_array($records)) {
            throw new \RuntimeException("Format berkas JSON klasifikasi tidak valid.");
        }

        $totalSource = count($records);
        $updated = 0;
        $inserted = 0;
        $now = now();

        DB::beginTransaction();
        try {
            // Load all existing classifications indexed by klas3
            $existing = Klasifikasi::all()->keyBy('klas3');

            $toInsert = [];

            foreach ($records as $index => $r) {
                $code = $r['klas3'];

                if (isset($existing[$code])) {
                    // Update existing classification
                    $item = $existing[$code];
                    $updateData = [
                        'klas1'      => $r['klas1'],
                        'masalah1'   => $r['masalah1'],
                        'klas2'      => $r['klas2'],
                        'masalah2'   => $r['masalah2'],
                        'masalah3'   => $r['masalah3'],
                        'series'     => $r['series'],
                        'updated_at' => $now,
                    ];

                    if ($forceOverwriteRetention) {
                        $updateData['r_aktif']    = $r['r_aktif'];
                        $updateData['r_inaktif']  = $r['r_inaktif'];
                        $updateData['ket_jra']    = $r['ket_jra'];
                        $updateData['nilai_guna'] = $r['nilai_guna'];
                    }

                    $item->update($updateData);
                    $updated++;
                } else {
                    // Prepare for bulk insert
                    $toInsert[] = [
                        'klas1'      => $r['klas1'],
                        'masalah1'   => $r['masalah1'],
                        'klas2'      => $r['klas2'],
                        'masalah2'   => $r['masalah2'],
                        'klas3'      => $r['klas3'],
                        'masalah3'   => $r['masalah3'],
                        'series'     => $r['series'],
                        'r_aktif'    => $r['r_aktif'],
                        'r_inaktif'  => $r['r_inaktif'],
                        'ket_jra'    => $r['ket_jra'],
                        'nilai_guna' => $r['nilai_guna'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    $inserted++;
                }

                if ($onProgress && ($index + 1) % 500 === 0) {
                    $onProgress($index + 1, $totalSource);
                }
            }

            // Chunked insert for high performance
            if (!empty($toInsert)) {
                $chunks = array_chunk($toInsert, 500);
                foreach ($chunks as $chunk) {
                    Klasifikasi::insert($chunk);
                }
            }

            // Resynchronize PostgreSQL auto-increment sequence if on pgsql
            if (DB::connection()->getDriverName() === 'pgsql') {
                DB::statement("SELECT setval('klasifikasis_id_seq', coalesce(max(id), 1)) FROM klasifikasis");
            }

            DB::commit();

            // Clear application reference cache
            $this->cacheService->forgetKlasifikasi();

            return [
                'total_source' => $totalSource,
                'updated'      => $updated,
                'inserted'     => $inserted,
                'total_in_db'  => Klasifikasi::count(),
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
