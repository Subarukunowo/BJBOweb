<?php

namespace Tests\Unit\Services;

use App\Models\Admin;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService(new UserRepository);
    }

    public function test_register_user_creates_new_user(): void
    {
        $userData = [
            'name' => 'Ahmad Dani',
            'email' => 'ahmad@example.com',
            'password' => 'secret123',
        ];

        $user = $this->authService->registerUser($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Ahmad Dani', $user->name);
        $this->assertEquals('ahmad@example.com', $user->email);
        $this->assertTrue(Hash::check('secret123', $user->password));
    }

    public function test_attempt_login_admin_success(): void
    {
        Admin::factory()->create([
            'username' => 'admin_bjbo',
            'email' => 'admin@bjbo.com',
            'password' => Hash::make('password123'),
        ]);

        $result = $this->authService->attemptLogin([
            'email' => 'admin@bjbo.com',
            'password' => 'password123',
        ]);

        $this->assertTrue($result['success']);
        $this->assertEquals('admin', $result['role']);
    }

    public function test_attempt_login_user_success(): void
    {
        User::factory()->create([
            'email' => 'user@bjbo.com',
            'password' => Hash::make('password123'),
            'is_blocked' => false,
        ]);

        $result = $this->authService->attemptLogin([
            'email' => 'user@bjbo.com',
            'password' => 'password123',
        ]);

        $this->assertTrue($result['success']);
        $this->assertEquals('user', $result['role']);
    }

    public function test_attempt_login_blocked_user_fails(): void
    {
        User::factory()->create([
            'email' => 'blocked@bjbo.com',
            'password' => Hash::make('password123'),
            'is_blocked' => true,
        ]);

        $result = $this->authService->attemptLogin([
            'email' => 'blocked@bjbo.com',
            'password' => 'password123',
        ]);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('diblokir', $result['message']);
    }

    public function test_attempt_login_invalid_credentials(): void
    {
        $result = $this->authService->attemptLogin([
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword',
        ]);

        $this->assertFalse($result['success']);
    }

    public function test_logout_helpers(): void
    {
        $user = User::factory()->create();
        Auth::login($user);
        $this->assertTrue(Auth::check());

        $this->authService->logoutUser();
        $this->assertFalse(Auth::check());

        $admin = Admin::factory()->create();
        Auth::guard('admin')->login($admin);
        $this->assertTrue(Auth::guard('admin')->check());

        $this->authService->logoutAdmin();
        $this->assertFalse(Auth::guard('admin')->check());
    }
}
