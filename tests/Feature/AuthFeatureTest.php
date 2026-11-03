<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_are_accessible(): void
    {
        $response = $this->get(route('splash'));
        $response->assertStatus(200);

        $aboutResponse = $this->get(route('about'));
        $aboutResponse->assertStatus(200);
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
    }

    public function test_register_page_renders_successfully(): void
    {
        $response = $this->get(route('register'));
        $response->assertStatus(200);
    }

    public function test_user_can_register_via_post(): void
    {
        $response = $this->post(route('post.register'), [
            'name' => 'User Baru',
            'email' => 'userbaru@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('otp.verify.show'));
        $this->assertDatabaseHas('email_otps', ['email' => 'userbaru@gmail.com']);
    }

    public function test_user_can_login_and_logout(): void
    {
        $user = User::factory()->create([
            'email' => 'loginuser@example.com',
            'password' => bcrypt('password123'),
            'is_blocked' => false,
        ]);

        $response = $this->post(route('post.login'), [
            'email' => 'loginuser@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('user.dashboard'));
        $this->assertAuthenticatedAs($user);

        // Logout
        $logoutResponse = $this->get(route('user.logout'));
        $logoutResponse->assertRedirect(route('splash'));
        $this->assertGuest();
    }
}
