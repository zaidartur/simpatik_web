<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class LaporanTindakLanjutTest extends TestCase
{
    /**
     * Test guest cannot access tindak lanjut.
     */
    public function test_guest_is_redirected_from_tindak_lanjut(): void
    {
        $response = $this->get('/laporan/tindak-lanjut');
        $response->assertRedirect('/login');
    }

    /**
     * Test admin can view tindak lanjut page.
     */
    public function test_admin_can_view_tindak_lanjut(): void
    {
        $admin = User::role('administrator')->first();
        if (!$admin) {
            $this->markTestSkipped('No administrator found.');
        }

        $response = $this->actingAs($admin)->get('/laporan/tindak-lanjut');
        $response->assertStatus(200);
        $response->assertViewIs('main.laporan.tindak_lanjut');
    }

    /**
     * Test tindak lanjut datatable SSR with date range.
     */
    public function test_tindak_lanjut_ssr_with_date_filter(): void
    {
        $admin = User::role('administrator')->first();
        if (!$admin) {
            $this->markTestSkipped('No administrator found.');
        }

        $response = $this->actingAs($admin)->getJson('/laporan/tabel-tindak-lanjut?draw=1&start=0&length=10&start_date=2025-12-01&end_date=2025-12-31');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data',
        ]);
    }

    /**
     * Test admin can print tindak lanjut to PDF.
     */
    public function test_admin_can_print_tindak_lanjut_pdf(): void
    {
        $admin = User::role('administrator')->first();
        if (!$admin) {
            $this->markTestSkipped('No administrator found.');
        }

        $response = $this->actingAs($admin)->get('/laporan/cetak-tindak-lanjut?start_date=2025-12-01&end_date=2025-12-15');
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    /**
     * Test print tindak lanjut PDF rejects date range greater than 31 days.
     */
    public function test_print_tindak_lanjut_pdf_rejects_greater_than_31_days(): void
    {
        $admin = User::role('administrator')->first();
        if (!$admin) {
            $this->markTestSkipped('No administrator found.');
        }

        $response = $this->actingAs($admin)->get('/laporan/cetak-tindak-lanjut?start_date=2025-01-01&end_date=2025-03-31');
        $response->assertStatus(422);
        $response->assertJson(['status' => 'error']);
    }

    /**
     * Test admin can export tindak lanjut to Excel.
     */
    public function test_admin_can_export_tindak_lanjut_excel(): void
    {
        $admin = User::role('administrator')->first();
        if (!$admin) {
            $this->markTestSkipped('No administrator found.');
        }

        $response = $this->actingAs($admin)->get('/laporan/export-tindak-lanjut?start_date=2025-12-01&end_date=2025-12-31');
        $response->assertStatus(200);
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $response->headers->get('content-type'));
    }
}
