<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class GlobalSearchTest extends TestCase
{
    /**
     * Test guest user cannot access global search and is redirected.
     */
    public function test_guest_is_redirected_from_search(): void
    {
        $response = $this->get('/search?q=test');
        $response->assertRedirect('/login');
    }

    /**
     * Test authenticated user can access global search.
     */
    public function test_authenticated_user_can_perform_search(): void
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No user found in database.');
        }

        $response = $this->actingAs($user)->get('/search?q=surat');

        $response->assertStatus(200);
        $response->assertViewIs('main.search.index');
        $response->assertViewHas(['results', 'term', 'filterType', 'totalCount']);
    }
}
