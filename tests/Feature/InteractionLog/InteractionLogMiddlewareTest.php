<?php

declare(strict_types=1);

namespace Tests\Feature\InteractionLog;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Passport;
use Tests\TestCase;

class InteractionLogMiddlewareTest extends TestCase
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

    public function test_interaction_is_logged_on_successful_login_with_masked_password(): void
    {
        $user = User::factory()->create([
            'email' => 'auditor@challenge.com',
            'password' => bcrypt('supersecret'),
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'auditor@challenge.com',
            'password' => 'supersecret',
        ]);

        $this->assertDatabaseHas('interaction_logs', [
            'service' => 'POST api/auth/login',
            'response_status_code' => 200,
        ]);

        $log = DB::table('interaction_logs')->where('service', 'POST api/auth/login')->first();
        $this->assertNotNull($log);
        $this->assertStringNotContainsString('supersecret', $log->request_body);
        $this->assertStringContainsString('********', $log->request_body);
        $this->assertEquals(200, $log->response_status_code);
        $this->assertNotEmpty($log->ip_address);
        $this->assertStringContainsString('access_token', $log->response_body);
    }

    public function test_interaction_is_logged_on_authenticated_favorite_gif_creation(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user, ['*'], 'api');

        $this->postJson('/api/gifs/favorites', [
            'gif_id' => 'gif_xyz_123',
            'alias' => 'Super Dog',
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('interaction_logs', [
            'user_id' => $user->id,
            'service' => 'POST api/gifs/favorites',
            'response_status_code' => 201,
        ]);

        $log = DB::table('interaction_logs')
            ->where('service', 'POST api/gifs/favorites')
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals($user->id, $log->user_id);
        $this->assertStringContainsString('gif_xyz_123', $log->request_body);
        $this->assertStringContainsString('Super Dog', $log->request_body);
        $this->assertStringContainsString('Favorite GIF saved successfully', $log->response_body);
    }

    public function test_interaction_is_logged_on_unauthenticated_request(): void
    {
        $response = $this->getJson('/api/gifs/search?query=cat');

        $this->assertDatabaseHas('interaction_logs', [
            'user_id' => null,
            'service' => 'GET api/gifs/search',
            'response_status_code' => 401,
        ]);
    }
}
