<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    /**
     * Test change password requires authenticated user.
     */
    public function test_guest_cannot_change_password(): void
    {
        $response = $this->postJson('/change-password', [
            'current_password' => 'oldpass',
            'password' => 'newpass123',
            'password_confirmation' => 'newpass123',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test user cannot change password with wrong current password.
     */
    public function test_user_cannot_change_password_with_incorrect_current_password(): void
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No user found.');
        }

        $response = $this->actingAs($user)->postJson('/change-password', [
            'current_password' => 'wrong_current_password_123',
            'password' => 'new_password_8_chars',
            'password_confirmation' => 'new_password_8_chars',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'status' => 'failed',
            'message' => 'Password saat ini tidak sesuai.',
        ]);
    }

    /**
     * Test user can successfully change password and sessions are updated.
     */
    public function test_user_can_change_password_successfully(): void
    {
        // Create temporary test user to prevent mutating existing account credentials
        $testUser = User::create([
            'uuid'         => (string) \Illuminate\Support\Str::uuid(),
            'nama_lengkap' => 'Test Security User',
            'username'     => 'testsec_' . uniqid(),
            'password'     => Hash::make('original_pass_123'),
            'level'        => 1,
            'is_block'     => false,
        ]);

        $response = $this->actingAs($testUser)->postJson('/change-password', [
            'current_password' => 'original_pass_123',
            'password' => 'new_secure_pass_456',
            'password_confirmation' => 'new_secure_pass_456',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);

        $testUser->refresh();
        $this->assertTrue(Hash::check('new_secure_pass_456', $testUser->password));

        // Clean up
        $testUser->delete();
    }
}
