<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LoginApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('passport:client', [
            '--personal' => true,
            '--name' => 'Personal Access Client',
            '--no-interaction' => true,
        ]);
    }

    public function test_user_can_login_with_valid_credentials_and_receives_30_min_token(): void
    {
        $user = User::factory()->create([
            'email' => 'developer@challenge.com',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'developer@challenge.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in',
                'expires_at',
                'user' => ['id', 'email', 'name'],
            ]);

        $response->assertJson([
            'token_type' => 'Bearer',
            'expires_in' => 1800, // 30 minutes in seconds
            'user' => [
                'id' => $user->id,
                'email' => 'developer@challenge.com',
            ],
        ]);
    }

    public function test_login_via_short_url_alias(): void
    {
        User::factory()->create([
            'email' => 'dev2@challenge.com',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'dev2@challenge.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'token_type' => 'Bearer',
                'expires_in' => 1800,
            ]);
    }

    public function test_login_with_invalid_password_returns_401(): void
    {
        User::factory()->create([
            'email' => 'developer@challenge.com',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'developer@challenge.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Unauthorized',
                'message' => 'Invalid email or password.',
            ]);
    }

    public function test_login_with_non_existent_email_returns_401(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'nobody@challenge.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Unauthorized',
            ]);
    }

    public function test_login_validation_errors_return_422(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'not-an-email',
            // password missing
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors']);
    }
}
