<?php

namespace Tests\Feature;

use App\Models\Inbox;
use App\Models\User;
use App\Notifications\SuratDiterimaNotification;
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

    /**
     * Test CSP header allows WebSocket connections (ws: wss:) for Reverb.
     */
    public function test_csp_header_permits_websocket_connections(): void
    {
        $response = $this->get('/');
        $csp = $response->headers->get('Content-Security-Policy');

        $this->assertNotNull($csp);
        $this->assertStringContainsString("connect-src 'self' ws: wss:", $csp);
    }

    /**
     * Test layout injects Reverb meta and script when broadcast connection is reverb.
     */
    public function test_layout_injects_reverb_meta_and_assets(): void
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No user found.');
        }

        config(['broadcasting.default' => 'reverb']);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('name="reverb-key"', false);
        $response->assertSee('name="reverb-host"', false);
        $response->assertSee('name="reverb-port"', false);
    }

    /**
     * Test SuratDiterimaNotification broadcast and database payload format.
     */
    public function test_surat_diterima_notification_payload(): void
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No user found.');
        }

        $inbox = new Inbox([
            'uuid'      => 'test-inbox-uuid',
            'no_surat'  => '001/TEST/2026',
            'no_agenda' => 123,
            'year'      => 2026,
            'perihal'   => 'Uji Coba Notifikasi',
            'dari'      => 'Dinas Pengujian',
        ]);

        $notification = new SuratDiterimaNotification($inbox, $user, 'disposisi', 'Harap ditindaklanjuti segera.');

        $this->assertEquals(['database', 'broadcast'], $notification->via($user));

        $dbData = $notification->toDatabase($user);
        $this->assertEquals('test-inbox-uuid', $dbData['surat_uuid']);
        $this->assertEquals('Disposisi Surat Masuk', $dbData['title']);

        $broadcastData = $notification->toBroadcast($user)->data;
        $this->assertEquals('test-inbox-uuid', $broadcastData['surat_uuid']);
        $this->assertEquals('Disposisi Surat Masuk', $broadcastData['title']);
    }
}
