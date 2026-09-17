<?php

namespace Tests\Feature;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class RateLimitingTest extends TestCase
{
    /**
     * Test sensitive auth rate limiter configuration and execution.
     */
    public function test_sensitive_auth_rate_limiting_logic(): void
    {
        $key = 'sensitive_test_user_ip';
        RateLimiter::clear($key);

        // Record 5 attempts
        for ($i = 1; $i <= 5; $i++) {
            $this->assertFalse(RateLimiter::tooManyAttempts($key, 5));
            RateLimiter::hit($key, 60);
        }

        // 6th attempt should be blocked
        $this->assertTrue(RateLimiter::tooManyAttempts($key, 5));
        $this->assertEquals(0, RateLimiter::retriesLeft($key, 5));

        // Clearing restores available attempts
        RateLimiter::clear($key);
        $this->assertFalse(RateLimiter::tooManyAttempts($key, 5));
    }

    /**
     * Test global and export rate limiters are registered properly.
     */
    public function test_rate_limiters_are_registered(): void
    {
        $request = Request::create('/test', 'GET');

        $limiter = RateLimiter::limiter('web-global');
        $this->assertNotNull($limiter);
        $limit = $limiter($request);
        $this->assertInstanceOf(Limit::class, $limit);
        $this->assertEquals(120, $limit->maxAttempts);

        $exportLimiter = RateLimiter::limiter('exports');
        $this->assertNotNull($exportLimiter);
        $exportLimit = $exportLimiter($request);
        $this->assertInstanceOf(Limit::class, $exportLimit);
        $this->assertEquals(10, $exportLimit->maxAttempts);

        $authLimiter = RateLimiter::limiter('sensitive-auth');
        $this->assertNotNull($authLimiter);
        $authLimit = $authLimiter($request);
        $this->assertInstanceOf(Limit::class, $authLimit);
        $this->assertEquals(5, $authLimit->maxAttempts);
    }
}
