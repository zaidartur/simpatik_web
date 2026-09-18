<?php

namespace Tests\Feature;

use App\Models\Sppd;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class SppdPrintTest extends TestCase
{
    /**
     * Test guest cannot print SPPD.
     */
    public function test_guest_is_redirected_from_sppd_print(): void
    {
        $response = $this->get('/sppd/print-pdf/' . Crypt::encryptString(1));
        $response->assertRedirect('/login');
    }

    /**
     * Test admin can print SPPD to PDF.
     */
    public function test_admin_can_print_sppd_pdf(): void
    {
        $admin = User::role('administrator')->first();
        if (!$admin) {
            $this->markTestSkipped('No administrator found.');
        }

        $sppd = Sppd::first();
        if (!$sppd) {
            $this->markTestSkipped('No SPPD found in database.');
        }

        $encryptedId = Crypt::encryptString($sppd->id);
        $response = $this->actingAs($admin)->get('/sppd/print-pdf/' . $encryptedId);

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    /**
     * Test invalid SPPD ID returns 404.
     */
    public function test_invalid_sppd_id_returns_404(): void
    {
        $admin = User::role('administrator')->first();
        if (!$admin) {
            $this->markTestSkipped('No administrator found.');
        }

        $encryptedId = Crypt::encryptString(999999999);
        $response = $this->actingAs($admin)->get('/sppd/print-pdf/' . $encryptedId);

        $response->assertStatus(404);
    }

    /**
     * Test SPPD serverside data includes print button option.
     */
    public function test_sppd_serverside_includes_print_button(): void
    {
        $admin = User::role('administrator')->first();
        if (!$admin) {
            $this->markTestSkipped('No administrator found.');
        }

        $response = $this->actingAs($admin)->getJson('/sppd/daftar-sppd?draw=1&start=0&length=5');
        $response->assertStatus(200);

        $data = $response->json('data');
        if (!empty($data)) {
            $firstRowOption = $data[0]['option'];
            $this->assertStringContainsString('print-pdf', $firstRowOption);
            $this->assertStringContainsString('Cetak Lembar SPPD', $firstRowOption);
        }
    }
}
