<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    /**
     * Test guest cannot access notification endpoints.
     */
    public function test_guest_cannot_access_notifications(): void
    {
        $resCount = $this->getJson('/notifikasi/unread-count');
        $resCount->assertStatus(401);

        $resRecent = $this->getJson('/notifikasi/recent');
        $resRecent->assertStatus(401);
    }

    /**
     * Test authenticated user can access notification count and list.
     */
    public function test_authenticated_user_can_get_notifications(): void
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No user found.');
        }

        $resCount = $this->actingAs($user)->getJson('/notifikasi/unread-count');
        $resCount->assertStatus(200);
        $resCount->assertJsonStructure(['count']);

        $resRecent = $this->actingAs($user)->getJson('/notifikasi/recent');
        $resRecent->assertStatus(200);
        $resRecent->assertJsonStructure(['notifications', 'unread_count']);
    }

    /**
     * Test mark all notifications as read.
     */
    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No user found.');
        }

        $response = $this->actingAs($user)->postJson('/notifikasi/mark-all-read');
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'unread_count' => 0,
        ]);
    }
}
