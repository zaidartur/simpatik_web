<?php

namespace Tests\Feature;

use App\Models\SifatSurat;
use App\Models\User;
use Tests\TestCase;

class ReferensiTest extends TestCase
{
    /**
     * Test guest cannot access referensi page.
     */
    public function test_guest_is_redirected_from_referensi(): void
    {
        $response = $this->get('/referensi');
        $response->assertRedirect('/login');
    }

    /**
     * Test administrator can view referensi page.
     */
    public function test_admin_can_view_referensi(): void
    {
        $admin = User::role('administrator')->first();
        if (!$admin) {
            $this->markTestSkipped('No administrator user found.');
        }

        $response = $this->actingAs($admin)->get('/referensi');
        $response->assertStatus(200);
        $response->assertViewIs('main.referensi.index');
        $response->assertViewHas(['klasifikasis', 'sifats', 'tempats', 'perkembangans', 'medias']);
    }

    /**
     * Test administrator can create, update, and delete a reference record.
     */
    public function test_admin_can_manage_sifat_surat(): void
    {
        $admin = User::role('administrator')->first();
        if (!$admin) {
            $this->markTestSkipped('No administrator user found.');
        }

        $uniqueName = 'Test Sifat ' . uniqid();

        // 1. Create
        $storeRes = $this->actingAs($admin)->postJson('/referensi/sifat-surat/simpan', [
            'nama_sifat' => $uniqueName,
        ]);
        $storeRes->assertStatus(200);
        $this->assertDatabaseHas('sifat_surats', ['nama_sifat' => $uniqueName]);

        $item = SifatSurat::where('nama_sifat', $uniqueName)->first();
        $this->assertNotNull($item);

        // 2. Update
        $updatedName = $uniqueName . ' Updated';
        $updateRes = $this->actingAs($admin)->postJson('/referensi/sifat-surat/update', [
            'id' => $item->id,
            'nama_sifat' => $updatedName,
        ]);
        $updateRes->assertStatus(200);
        $this->assertDatabaseHas('sifat_surats', ['nama_sifat' => $updatedName]);

        // 3. Delete
        $destroyRes = $this->actingAs($admin)->postJson('/referensi/sifat-surat/hapus', [
            'id' => $item->id,
        ]);
        $destroyRes->assertStatus(200);
        $this->assertDatabaseMissing('sifat_surats', ['id' => $item->id]);
    }
}
