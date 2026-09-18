<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations to align role hierarchy matrix in accordance with Permendagri No. 1/2023.
     */
    public function up(): void
    {
        // 1. Administrator (ID 1)
        DB::table('level_users')->where('id', 1)->update([
            'daftar_terusan' => json_encode([2, 3, 4, 5, 6, 7, 8, 10, 11, 12]),
            'akses'          => json_encode([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]),
            'tindak_lanjut'  => true,
            'updated_at'     => now(),
        ]);

        // 2. Bagian Umum (ID 2)
        DB::table('level_users')->where('id', 2)->update([
            'daftar_terusan' => json_encode([3]),
            'akses'          => json_encode([2]),
            'tindak_lanjut'  => true,
            'updated_at'     => now(),
        ]);

        // 3. Sekretaris Daerah / Sekda (ID 3)
        // Dapat meneruskan ke Bupati/Wabup dan mendisposisikan ke para Asisten, Staf Ahli, dan Bagian Umum
        DB::table('level_users')->where('id', 3)->update([
            'daftar_terusan' => json_encode([2, 4, 5, 6, 7, 8, 10, 11, 12]),
            'akses'          => json_encode([2, 3, 4, 5, 6, 7, 8, 10, 11, 12]),
            'tindak_lanjut'  => true,
            'updated_at'     => now(),
        ]);

        // 4. Asisten 1 - Pemerintahan & Kesra (ID 4)
        DB::table('level_users')->where('id', 4)->update([
            'daftar_terusan' => json_encode([2, 3]),
            'akses'          => json_encode([2, 4]),
            'tindak_lanjut'  => true,
            'updated_at'     => now(),
        ]);

        // 5. Asisten 2 - Perekonomian & Pembangunan (ID 5)
        DB::table('level_users')->where('id', 5)->update([
            'daftar_terusan' => json_encode([2, 3]),
            'akses'          => json_encode([2, 5]),
            'tindak_lanjut'  => true,
            'updated_at'     => now(),
        ]);

        // 6. Asisten 3 - Administrasi Umum (ID 6)
        DB::table('level_users')->where('id', 6)->update([
            'daftar_terusan' => json_encode([2, 3]),
            'akses'          => json_encode([2, 6]),
            'tindak_lanjut'  => true,
            'updated_at'     => now(),
        ]);

        // 7. Wakil Bupati (ID 7)
        DB::table('level_users')->where('id', 7)->update([
            'daftar_terusan' => json_encode([3, 4, 5, 6, 8]),
            'akses'          => json_encode([2, 3, 4, 5, 6, 7, 8, 10, 11, 12]),
            'tindak_lanjut'  => true,
            'updated_at'     => now(),
        ]);

        // 8. Bupati (ID 8)
        DB::table('level_users')->where('id', 8)->update([
            'daftar_terusan' => json_encode([3, 4, 5, 6, 7]),
            'akses'          => json_encode([2, 3, 4, 5, 6, 7, 8, 10, 11, 12]),
            'tindak_lanjut'  => true,
            'updated_at'     => now(),
        ]);

        // 9. TU Umum / Admin (ID 9)
        DB::table('level_users')->where('id', 9)->update([
            'daftar_terusan' => json_encode([3, 7, 8]),
            'akses'          => json_encode([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]),
            'tindak_lanjut'  => true,
            'updated_at'     => now(),
        ]);

        // 10, 11, 12. Staf Ahli 1, 2, 3 (ID 10, 11, 12)
        foreach ([10, 11, 12] as $stId) {
            DB::table('level_users')->where('id', $stId)->update([
                'daftar_terusan' => json_encode([3]),
                'akses'          => json_encode([$stId]),
                'tindak_lanjut'  => true,
                'updated_at'     => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('level_users')->where('id', 8)->update([
            'daftar_terusan' => null,
            'akses'          => json_encode([8]),
            'tindak_lanjut'  => false,
        ]);

        DB::table('level_users')->where('id', 7)->update([
            'daftar_terusan' => json_encode([8]),
            'akses'          => json_encode([7]),
            'tindak_lanjut'  => true,
        ]);

        DB::table('level_users')->where('id', 3)->update([
            'daftar_terusan' => json_encode([7, 8]),
            'akses'          => json_encode([3, 8]),
            'tindak_lanjut'  => true,
        ]);
    }
};
