<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserBlockingTest extends TestCase
{
    /**
     * Test admin can toggle user status between Aktif and Nonaktif.
     */
    public function test_admin_can_toggle_user_status(): void
    {
        $admin = User::role('administrator')->first();
        if (!$admin) {
            $this->markTestSkipped('No administrator found.');
        }

        // Create a dummy user
        $testUser = User::create([
            'uuid'         => Str::uuid()->toString(),
            'nama_lengkap' => 'Test User Block',
            'username'     => 'testuser_' . rand(1000, 9999),
            'email'        => 'testblock_' . rand(1000, 9999) . '@example.com',
            'password'     => Hash::make('password123'),
            'level'        => 1,
            'blokir'       => 'N',
        ]);

        // 1. Toggle N -> Y (Block)
        $response = $this->actingAs($admin)->postJson('/user/toggle-status', [
            'uid' => Crypt::encryptString($testUser->id),
        ]);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success', 'new_status' => 'Y']);

        $testUser->refresh();
        $this->assertEquals('Y', $testUser->blokir);

        // 2. Toggle Y -> N (Unblock)
        $response2 = $this->actingAs($admin)->postJson('/user/toggle-status', [
            'uid' => Crypt::encryptString($testUser->id),
        ]);
        $response2->assertStatus(200);
        $response2->assertJson(['status' => 'success', 'new_status' => 'N']);

        $testUser->refresh();
        $this->assertEquals('N', $testUser->blokir);

        // Clean up
        $testUser->delete();
    }

    /**
     * Test admin cannot block themselves.
     */
    public function test_admin_cannot_self_block(): void
    {
        $admin = User::role('administrator')->first();
        if (!$admin) {
            $this->markTestSkipped('No administrator found.');
        }

        $response = $this->actingAs($admin)->postJson('/user/toggle-status', [
            'uid' => Crypt::encryptString($admin->id),
        ]);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'failed']);

        $admin->refresh();
        $this->assertEquals('N', $admin->blokir);
    }

    /**
     * Test blocked user cannot login and gets specific disabled message.
     */
    public function test_blocked_user_cannot_login(): void
    {
        $username = 'blocked_' . rand(1000, 9999);
        $password = 'SecretPass123!';

        $user = User::create([
            'uuid'         => Str::uuid()->toString(),
            'nama_lengkap' => 'Blocked Account User',
            'username'     => $username,
            'email'        => $username . '@example.com',
            'password'     => Hash::make($password),
            'level'        => 1,
            'blokir'       => 'Y',
        ]);

        $response = $this->post('/login', [
            'username' => $username,
            'password' => $password,
        ]);

        $response->assertSessionHasErrors('username');
        $errorMsg = session('errors')->first('username');
        $this->assertStringContainsString('dinonaktifkan', $errorMsg);

        $this->assertGuest();

        $user->delete();
    }

    /**
     * Test active session of blocked user is kicked out by CheckUserBlocked middleware.
     */
    public function test_active_session_of_blocked_user_is_logged_out(): void
    {
        $user = User::create([
            'uuid'         => Str::uuid()->toString(),
            'nama_lengkap' => 'Active Session Blocked',
            'username'     => 'active_block_' . rand(1000, 9999),
            'email'        => 'active_block_' . rand(1000, 9999) . '@example.com',
            'password'     => Hash::make('password123'),
            'level'        => 1,
            'blokir'       => 'Y', // Set blocked
        ]);

        // Attempt to request an auth route
        $response = $this->actingAs($user)->get('/dashboard');

        // Middleware should log user out and redirect to login with error
        $this->assertGuest();
        $response->assertRedirect('/login');

        $user->delete();
    }
}
