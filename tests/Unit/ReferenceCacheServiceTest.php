<?php

namespace Tests\Unit;

use App\Models\Klasifikasi;
use App\Models\SifatSurat;
use App\Services\ReferenceCacheService;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ReferenceCacheServiceTest extends TestCase
{
    protected ReferenceCacheService $cacheService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cacheService = new ReferenceCacheService();
    }

    /**
     * Test caching and cache retrieval for Klasifikasi.
     */
    public function test_get_klasifikasi_caches_results(): void
    {
        Cache::forget(ReferenceCacheService::KEY_KLASIFIKASI);

        $firstCall = $this->cacheService->getKlasifikasi();
        $this->assertNotEmpty($firstCall);
        $this->assertTrue(Cache::has(ReferenceCacheService::KEY_KLASIFIKASI));

        $secondCall = $this->cacheService->getKlasifikasi();
        $this->assertCount($firstCall->count(), $secondCall);
    }

    /**
     * Test cache invalidation by type.
     */
    public function test_forget_by_type_clears_specific_cache(): void
    {
        $this->cacheService->getSifatSurat();
        $this->assertTrue(Cache::has(ReferenceCacheService::KEY_SIFAT));

        $this->cacheService->forgetByType('sifat-surat');
        $this->assertFalse(Cache::has(ReferenceCacheService::KEY_SIFAT));
    }

    /**
     * Test flush all caches.
     */
    public function test_flush_all_clears_all_reference_keys(): void
    {
        $this->cacheService->getKlasifikasi();
        $this->cacheService->getSifatSurat();
        $this->cacheService->getTempatBerkas();

        $this->cacheService->flushAll();

        $this->assertFalse(Cache::has(ReferenceCacheService::KEY_KLASIFIKASI));
        $this->assertFalse(Cache::has(ReferenceCacheService::KEY_SIFAT));
        $this->assertFalse(Cache::has(ReferenceCacheService::KEY_TEMPAT));
    }
}
