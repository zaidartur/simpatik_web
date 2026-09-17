<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Global web rate limiter: 120 requests per minute
        RateLimiter::for('web-global', function (Request $request) {
            return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
        });

        // Sensitive auth / password operations: 5 requests per minute
        RateLimiter::for('sensitive-auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip())->response(function () {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Terlalu banyak percobaan. Harap tunggu beberapa saat lagi.',
                ], 429);
            });
        });

        // Heavy report exports: 10 requests per minute
        RateLimiter::for('exports', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip())->response(function () {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Batas unduhan laporan tercapai. Harap tunggu sebentar sebelum mengekspor kembali.',
                ], 429);
            });
        });
    }
}
