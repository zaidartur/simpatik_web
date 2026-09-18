<?php

namespace Tests\Feature;

use App\Models\DataUnit;
use App\Models\Sppd;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class QuickFixesTest extends TestCase
{
    /**
     * Test SPPD serverside search uses case-insensitive ilike.
     */
    public function test_sppd_serverside_ilike_search(): void
    {
        $user = User::role('administrator')->first();
        if (!$user) {
            $this->markTestSkipped('No user found.');
        }

        // Test with uppercase and lowercase search term
        $response = $this->actingAs($user)->getJson('/sppd/daftar-sppd?draw=1&start=0&length=10&search[value]=dinas');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data',
        ]);

        $responseUpper = $this->actingAs($user)->getJson('/sppd/daftar-sppd?draw=1&start=0&length=10&search[value]=DINAS');
        $responseUpper->assertStatus(200);
        // Both uppercase and lowercase searches should yield the exact same filtered count
        $this->assertEquals(
            $response->json('recordsFiltered'),
            $responseUpper->json('recordsFiltered')
        );
    }

    /**
     * Test dead routes are now removed and return 404.
     */
    public function test_dead_routes_are_removed(): void
    {
        $user = User::role('administrator')->first();
        if (!$user) {
            $this->markTestSkipped('No user found.');
        }

        // Dead route: user.create (/user/buat-user)
        $res1 = $this->actingAs($user)->get('/user/buat-user');
        $res1->assertStatus(404);

        // Dead route: user.save (/user/simpan-user-query)
        $res2 = $this->actingAs($user)->post('/user/simpan-user-query');
        $res2->assertStatus(404);

        // Dead route: sppd.edit (/sppd/edit-sppd/1)
        $res3 = $this->actingAs($user)->get('/sppd/edit-sppd/1');
        $res3->assertStatus(404);

        // Dead route: sppd.show (/sppd/lihat-sppd/1)
        $res4 = $this->actingAs($user)->get('/sppd/lihat-sppd/1');
        $res4->assertStatus(404);
    }

    /**
     * Test instansi CRUD with website url longer than 15 characters.
     */
    public function test_instansi_crud_with_long_website_url(): void
    {
        $admin = User::role('administrator')->first();
        if (!$admin) {
            $this->markTestSkipped('No administrator user found.');
        }

        $code = 'T' . rand(100, 999);
        $website = 'https://diskominfo.karanganyarkab.go.id/portal';

        // 1. Create with website URL > 15 chars
        $response = $this->actingAs($admin)->post('/instansi/simpan-instansi', [
            'nama'    => 'Dinas Pengujian ' . $code,
            'akronim' => 'DP' . $code,
            'kode'    => $code,
            'alamat'  => 'Jl. Lawu No. 123',
            'website' => $website,
            'email'   => 'uji' . $code . '@karanganyarkab.go.id',
        ]);
        $response->assertSessionHas('success');

        $instansi = DataUnit::where('kode', $code)->first();
        $this->assertNotNull($instansi);
        $this->assertEquals($website, $instansi->website);

        // 2. Update instansi website
        $newWebsite = 'https://diskominfo.karanganyarkab.go.id/portal-updated';
        $updateResponse = $this->actingAs($admin)->post('/instansi/update-instansi', [
            'uid'     => $instansi->id,
            'nama'    => 'Dinas Pengujian ' . $code . ' Rev',
            'akronim' => 'DP' . $code,
            'kode'    => $code,
            'alamat'  => 'Jl. Lawu No. 123 Rev',
            'website' => $newWebsite,
            'email'   => 'uji' . $code . '@karanganyarkab.go.id',
        ]);
        $updateResponse->assertSessionHas('success');

        $instansi->refresh();
        $this->assertEquals($newWebsite, $instansi->website);
        $this->assertEquals('Dinas Pengujian ' . $code . ' Rev', $instansi->nama_unit);

        // 3. Delete instansi
        $delResponse = $this->actingAs($admin)->postJson('/instansi/hapus-instansi', [
            'uid' => Crypt::encryptString($instansi->id),
        ]);
        $delResponse->assertStatus(200);
        $delResponse->assertJson(['status' => 'success']);

        $this->assertNull(DataUnit::find($instansi->id));
    }
}
