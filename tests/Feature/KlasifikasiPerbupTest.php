<?php

namespace Tests\Feature;

use App\Models\Klasifikasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class KlasifikasiPerbupTest extends TestCase
{
    public function test_klasifikasi_perbup_json_dataset_exists_and_is_complete(): void
    {
        $filePath = database_path('data/klasifikasi_perbup_2023.json');
        $this->assertTrue(File::exists($filePath), "Dataset klasifikasi_perbup_2023.json harus ada");

        $data = json_decode(File::get($filePath), true);
        $this->assertIsArray($data);
        $this->assertCount(2981, $data, "Dataset harus berisi tepat 2.981 kode");

        // Verify structure of first item
        $first = $data[0];
        $this->assertArrayHasKey('klas1', $first);
        $this->assertArrayHasKey('klas2', $first);
        $this->assertArrayHasKey('klas3', $first);
        $this->assertArrayHasKey('masalah1', $first);
        $this->assertArrayHasKey('masalah2', $first);
        $this->assertArrayHasKey('masalah3', $first);
        $this->assertArrayHasKey('series', $first);
        $this->assertArrayHasKey('r_aktif', $first);
        $this->assertArrayHasKey('r_inaktif', $first);
        $this->assertArrayHasKey('ket_jra', $first);
        $this->assertArrayHasKey('nilai_guna', $first);
    }

    public function test_import_command_executes_successfully(): void
    {
        $this->artisan('app:import-klasifikasi')
            ->assertExitCode(0);

        $this->assertGreaterThanOrEqual(2981, Klasifikasi::count());

        // Verify specific sample codes
        $code1 = Klasifikasi::where('klas3', '000.1.2.1')->first();
        $this->assertNotNull($code1);
        $this->assertEquals('Perjalanan Dinas Kepala Daerah', $code1->masalah3);
        $this->assertEquals('000', $code1->klas1);
        $this->assertEquals('000.1', $code1->klas2);

        $code2 = Klasifikasi::where('klas3', '900.1.9')->first();
        $this->assertNotNull($code2);
        $this->assertEquals('KEU', $code2->nilai_guna);
        $this->assertStringContainsString('Penyusunan Anggaran Pilkada', $code2->masalah3);
    }

    public function test_get_jra_endpoint_returns_data_for_new_classification_code(): void
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('Tidak ada user untuk autentikasi');
        }

        $response = $this->actingAs($user)->postJson('/detail-jra', [
            'kode' => '000.1.2.1',
            'name' => 'Perjalanan Dinas Kepala Daerah',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'jra' => [
                    'klas3' => '000.1.2.1',
                    'r_aktif' => 2,
                    'r_inaktif' => 3,
                ],
            ]);
    }
}
