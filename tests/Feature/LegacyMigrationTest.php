<?php

namespace Tests\Feature;

use App\Http\Controllers\LegacyMigrationController;
use App\Models\User;
use App\Services\LegacyMigrationService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class LegacyMigrationTest extends TestCase
{
    /**
     * Test command artisan app:migrate-legacy --dry-run
     */
    public function test_artisan_command_dry_run(): void
    {
        $this->artisan('app:migrate-legacy', [
            '--dry-run' => true,
            '--type' => 'all',
        ])
        ->expectsOutputToContain('SIPERMAS - Migrasi Data Persuratan Legacy')
        ->expectsOutputToContain('RINGKASAN HASIL MIGRASI')
        ->expectsOutputToContain('8,080')
        ->assertSuccessful();
    }

    /**
     * Test admin dapat mengakses halaman migrasi saat environment local / testing.
     */
    public function test_admin_can_view_migration_index(): void
    {
        $admin = User::role('administrator')->first();
        if (!$admin) {
            $this->markTestSkipped('No administrator user found.');
        }

        $response = $this->actingAs($admin)->get(route('migration.index'));
        $response->assertStatus(200);
        $response->assertViewIs('main.migration.index');
        $response->assertViewHas(['defaultFileExists', 'inboxCount', 'outboxCount']);
    }

    /**
     * Test proses simulasi dry run via endpoint AJAX.
     */
    public function test_admin_can_run_dry_run_via_ajax(): void
    {
        $admin = User::role('administrator')->first();
        if (!$admin) {
            $this->markTestSkipped('No administrator user found.');
        }

        $response = $this->actingAs($admin)->postJson(route('migration.process'), [
            'source_type' => 'default',
            'data_type'   => 'all',
            'dry_run'     => 1,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'total_parsed'   => 8080,
                'masuk_success'  => 7688,
                'keluar_success' => 392,
                'dry_run'        => true,
            ]
        ]);
    }

    /**
     * Test non-admin tidak dapat mengakses halaman migrasi.
     */
    public function test_non_admin_forbidden_from_migration(): void
    {
        $nonAdmin = User::whereDoesntHave('roles', function ($q) {
            $q->where('name', 'administrator');
        })->first();

        if (!$nonAdmin) {
            $this->markTestSkipped('No non-admin user found.');
        }

        $response = $this->actingAs($nonAdmin)->get('/legacy-migration');
        $response->assertStatus(403);
    }

    /**
     * Test controller melempar HTTP 404 jika dipanggil di luar environment local/testing.
     */
    public function test_controller_guard_aborts_in_production(): void
    {
        // Simulasikan APP_ENV production
        config(['app.env' => 'production']);
        $this->app['env'] = 'production';

        $this->expectException(NotFoundHttpException::class);

        $service = app(LegacyMigrationService::class);
        new LegacyMigrationController($service);
    }
}
