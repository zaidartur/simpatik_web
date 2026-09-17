<?php

namespace Tests\Feature;

use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ExportExcelTest extends TestCase
{
    /**
     * Test guest cannot access export routes.
     */
    public function test_guest_is_redirected_from_exports(): void
    {
        $this->get('/laporan/export-agenda')->assertRedirect('/login');
        $this->get('/laporan/export-statistik')->assertRedirect('/login');
    }

    /**
     * Test authenticated user can download agenda export (Masuk & Keluar).
     */
    public function test_authenticated_user_can_export_agenda(): void
    {
        Excel::fake();
        Excel::matchByRegex();

        $user = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['administrator', 'admin']);
        })->first() ?? User::first();

        if (!$user) {
            $this->markTestSkipped('No user found in database.');
        }

        // Test export Agenda Masuk
        $this->actingAs($user)->get('/laporan/export-agenda?jenis=Masuk');
        Excel::assertDownloaded('/Agenda_Masuk_.*\.xlsx/', function (\App\Exports\AgendaMasukExport $export) {
            return $export->collection() instanceof \Illuminate\Support\Enumerable;
        });

        // Test export Agenda Keluar
        $this->actingAs($user)->get('/laporan/export-agenda?jenis=Keluar');
        Excel::assertDownloaded('/Agenda_Keluar_.*\.xlsx/', function (\App\Exports\AgendaKeluarExport $export) {
            return $export->collection() instanceof \Illuminate\Support\Enumerable;
        });
    }

    /**
     * Test authenticated user can download statistik export.
     */
    public function test_authenticated_user_can_export_statistik(): void
    {
        Excel::fake();
        Excel::matchByRegex();

        $user = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['administrator', 'admin']);
        })->first() ?? User::first();

        if (!$user) {
            $this->markTestSkipped('No user found in database.');
        }

        $this->actingAs($user)->get('/laporan/export-statistik?year=' . date('Y'));
        Excel::assertDownloaded('/Statistik_Persuratan_.*\.xlsx/', function (\App\Exports\StatistikExport $export) {
            return $export->collection() instanceof \Illuminate\Support\Enumerable;
        });
    }

    /**
     * Test authenticated user can download agenda export for Semua (multi-sheet).
     */
    public function test_authenticated_user_can_export_agenda_all(): void
    {
        Excel::fake();
        Excel::matchByRegex();

        $user = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['administrator', 'admin']);
        })->first() ?? User::first();

        if (!$user) {
            $this->markTestSkipped('No user found in database.');
        }

        $this->actingAs($user)->get('/laporan/export-agenda');
        Excel::assertDownloaded('/Agenda_Semua_.*\.xlsx/', function (\App\Exports\AgendaAllExport $export) {
            return count($export->sheets()) === 2;
        });
    }

    /**
     * Test authenticated user can stream agenda PDF.
     */
    public function test_authenticated_user_can_print_agenda_pdf(): void
    {
        $user = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['administrator', 'admin']);
        })->first() ?? User::first();

        if (!$user) {
            $this->markTestSkipped('No user found in database.');
        }

        $response = $this->actingAs($user)->get('/laporan/print-agenda?start_date=2026-02-01&end_date=2026-02-28&jenis=Masuk');
        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('Content-Type'), 'application/pdf'));
    }

    /**
     * Test authenticated user can stream agenda PDF using Native FPDF engine.
     */
    public function test_authenticated_user_can_print_agenda_fpdf(): void
    {
        $user = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['administrator', 'admin']);
        })->first() ?? User::first();

        if (!$user) {
            $this->markTestSkipped('No user found in database.');
        }

        $response = $this->actingAs($user)->get('/laporan/print-agenda-fpdf?start_date=2026-02-01&end_date=2026-02-28&jenis=Masuk');
        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('Content-Type'), 'application/pdf'));
    }

    /**
     * Test PDF print limits range to max 31 days.
     */
    public function test_agenda_pdf_rejects_more_than_31_days(): void
    {
        $user = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['administrator', 'admin']);
        })->first() ?? User::first();

        if (!$user) {
            $this->markTestSkipped('No user found in database.');
        }

        // Test with 45 days range
        $responseDom = $this->actingAs($user)->get('/laporan/print-agenda?start_date=2026-01-01&end_date=2026-02-15&jenis=Masuk');
        $this->assertTrue(str_contains($responseDom->getContent(), 'Rentang waktu cetak PDF maksimal 31 hari'));

        $responseFpdf = $this->actingAs($user)->get('/laporan/print-agenda-fpdf?start_date=2026-01-01&end_date=2026-02-15&jenis=Masuk');
        $this->assertTrue(str_contains($responseFpdf->getContent(), 'Rentang waktu cetak PDF maksimal 31 hari'));
    }
}
