<?php

namespace App\Services;

use App\Models\Klasifikasi;
use App\Models\MediaSurat;
use App\Models\Perkembangan;
use App\Models\Pimpinan;
use App\Models\SifatSurat;
use App\Models\TempatBerkas;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class ReferenceCacheService
{
    public const TTL = 3600; // 1 hour

    public const KEY_KLASIFIKASI = 'ref_klasifikasi_all';
    public const KEY_SIFAT       = 'ref_sifat_all';
    public const KEY_TEMPAT      = 'ref_tempat_all';
    public const KEY_PERKEMBANGAN = 'ref_perkembangan_all';
    public const KEY_MEDIA       = 'ref_media_all';
    public const KEY_PEJABAT     = 'ref_pejabat_all';

    /**
     * Get all Klasifikasi JRA from cache.
     */
    public function getKlasifikasi(): Collection
    {
        return Cache::remember(self::KEY_KLASIFIKASI, self::TTL, function () {
            return Klasifikasi::orderBy('klas3')->get();
        });
    }

    /**
     * Get all Sifat Surat from cache.
     */
    public function getSifatSurat(): Collection
    {
        return Cache::remember(self::KEY_SIFAT, self::TTL, function () {
            return SifatSurat::orderBy('nama_sifat')->get();
        });
    }

    /**
     * Get all Tempat Berkas from cache.
     */
    public function getTempatBerkas(): Collection
    {
        return Cache::remember(self::KEY_TEMPAT, self::TTL, function () {
            return TempatBerkas::orderBy('nama')->get();
        });
    }

    /**
     * Get all Tingkat Perkembangan from cache.
     */
    public function getPerkembangan(): Collection
    {
        return Cache::remember(self::KEY_PERKEMBANGAN, self::TTL, function () {
            return Perkembangan::orderBy('nama')->get();
        });
    }

    /**
     * Get all Media Surat from cache.
     */
    public function getMediaSurat(): Collection
    {
        return Cache::remember(self::KEY_MEDIA, self::TTL, function () {
            return MediaSurat::orderBy('nama')->get();
        });
    }

    /**
     * Get default pimpinan for specific level from cache.
     */
    public function getDefaultPimpinan(?int $level = null): ?Pimpinan
    {
        $key = 'ref_pimpinan_default_' . ($level ?? 'all');

        return Cache::remember($key, self::TTL, function () use ($level) {
            $query = Pimpinan::where('is_default', true);
            if ($level !== null) {
                $query->where('level', $level);
            }
            return $query->first();
        });
    }

    /**
     * Get all pimpinan / pejabat list.
     */
    public function getAllPejabat(): Collection
    {
        return Cache::remember(self::KEY_PEJABAT, self::TTL, function () {
            return Pimpinan::orderBy('nama')->get();
        });
    }

    // --- Cache Invalidation Methods ---

    public function forgetKlasifikasi(): void
    {
        Cache::forget(self::KEY_KLASIFIKASI);
    }

    public function forgetSifat(): void
    {
        Cache::forget(self::KEY_SIFAT);
    }

    public function forgetTempat(): void
    {
        Cache::forget(self::KEY_TEMPAT);
    }

    public function forgetPerkembangan(): void
    {
        Cache::forget(self::KEY_PERKEMBANGAN);
    }

    public function forgetMedia(): void
    {
        Cache::forget(self::KEY_MEDIA);
    }

    public function forgetPimpinan(): void
    {
        Cache::forget(self::KEY_PEJABAT);
        // Forget common levels
        for ($i = 1; $i <= 10; $i++) {
            Cache::forget('ref_pimpinan_default_' . $i);
        }
        Cache::forget('ref_pimpinan_default_all');
    }

    /**
     * Invalidate cache based on reference type string.
     */
    public function forgetByType(string $type): void
    {
        match ($type) {
            'klasifikasi'  => $this->forgetKlasifikasi(),
            'sifat-surat'  => $this->forgetSifat(),
            'tempat-berkas'=> $this->forgetTempat(),
            'perkembangan' => $this->forgetPerkembangan(),
            'media-surat'  => $this->forgetMedia(),
            'pimpinan'     => $this->forgetPimpinan(),
            default        => null,
        };
    }

    /**
     * Flush all cached reference data.
     */
    public function flushAll(): void
    {
        $this->forgetKlasifikasi();
        $this->forgetSifat();
        $this->forgetTempat();
        $this->forgetPerkembangan();
        $this->forgetMedia();
        $this->forgetPimpinan();
    }
}
