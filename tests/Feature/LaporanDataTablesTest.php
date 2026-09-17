<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class LaporanDataTablesTest extends TestCase
{
    /**
     * Test guest user cannot access SSR datatables and is redirected.
     */
    public function test_guest_is_redirected_from_laporan_datatables(): void
    {
        $this->get('/laporan/tabel-tindak-lanjut')->assertRedirect('/login');
        $this->get('/laporan/tabel-agenda')->assertRedirect('/login');
    }

    /**
     * Test tindak lanjut datatable SSR returns valid JSON with proper structure.
     */
    public function test_tindak_lanjut_datatable_ssr_success(): void
    {
        $user = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['administrator', 'admin']);
        })->first() ?? User::first();

        if (!$user) {
            $this->markTestSkipped('No user found in database.');
        }

        $response = $this->actingAs($user)->getJson('/laporan/tabel-tindak-lanjut?draw=1&start=0&length=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'draw',
                'recordsTotal',
                'recordsFiltered',
                'data',
            ]);
    }

    /**
     * Test agenda datatable SSR with default / empty jenis returns valid JSON.
     */
    public function test_agenda_datatable_ssr_default_all(): void
    {
        $user = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['administrator', 'admin']);
        })->first() ?? User::first();

        if (!$user) {
            $this->markTestSkipped('No user found in database.');
        }

        $response = $this->actingAs($user)->getJson('/laporan/tabel-agenda?draw=1&start=0&length=10&jenis=');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'draw',
                'recordsTotal',
                'recordsFiltered',
                'data',
            ]);
    }

    /**
     * Test agenda datatable SSR with jenis=Masuk and jenis=Keluar.
     */
    public function test_agenda_datatable_ssr_with_jenis_masuk_and_keluar(): void
    {
        $user = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['administrator', 'admin']);
        })->first() ?? User::first();

        if (!$user) {
            $this->markTestSkipped('No user found in database.');
        }

        // Test jenis Masuk
        $resMasuk = $this->actingAs($user)->getJson('/laporan/tabel-agenda?draw=1&start=0&length=10&jenis=Masuk');
        $resMasuk->assertStatus(200)
            ->assertJsonStructure([
                'draw',
                'recordsTotal',
                'recordsFiltered',
                'data',
            ]);

        // Test jenis Keluar
        $resKeluar = $this->actingAs($user)->getJson('/laporan/tabel-agenda?draw=1&start=0&length=10&jenis=Keluar');
        $resKeluar->assertStatus(200)
            ->assertJsonStructure([
                'draw',
                'recordsTotal',
                'recordsFiltered',
                'data',
            ]);
    }

    /**
     * Test agenda datatable SSR with search and date range filters.
     */
    public function test_agenda_datatable_ssr_with_filters(): void
    {
        $user = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['administrator', 'admin']);
        })->first() ?? User::first();

        if (!$user) {
            $this->markTestSkipped('No user found in database.');
        }

        $response = $this->actingAs($user)->getJson('/laporan/tabel-agenda?draw=1&start=0&length=10&jenis=Masuk&start_date=2024-01-01&end_date=2026-12-31&search[value]=surat');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'draw',
                'recordsTotal',
                'recordsFiltered',
                'data',
            ]);
    }
}
