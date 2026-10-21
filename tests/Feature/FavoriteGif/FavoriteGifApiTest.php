<?php

declare(strict_types=1);

namespace Tests\Feature\FavoriteGif;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class FavoriteGifApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_request_returns_401(): void
    {
        $response = $this->postJson('/api/gifs/favorites', [
            'gif_id' => '12345',
            'alias' => 'Cool GIF',
            'user_id' => 1,
        ]);

        $response->assertStatus(401);
    }

    public function test_missing_parameters_returns_422(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user, ['*'], 'api');

        $response = $this->postJson('/api/gifs/favorites', [
            // missing gif_id, alias, user_id
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors' => ['gif_id', 'alias', 'user_id']]);
    }

    public function test_user_not_found_returns_404(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user, ['*'], 'api');

        $response = $this->postJson('/api/gifs/favorites', [
            'gif_id' => 'abc999',
            'alias' => 'My Cat',
            'user_id' => 999999, // non-existent user
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'error' => 'Not Found',
            ]);
    }

    public function test_save_favorite_gif_successfully_persists_and_returns_201(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user, ['*'], 'api');

        $response = $this->postJson('/api/gifs/favorites', [
            'gif_id' => 'abc999',
            'alias' => 'Hilarious Cat',
            'user_id' => $user->id,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Favorite GIF saved successfully.',
                'data' => [
                    'user_id' => $user->id,
                    'gif_id' => 'abc999',
                    'alias' => 'Hilarious Cat',
                ],
            ]);

        $this->assertDatabaseHas('favorite_gifs', [
            'user_id' => $user->id,
            'gif_id' => 'abc999',
            'alias' => 'Hilarious Cat',
        ]);
    }

    public function test_save_favorite_via_short_url_alias(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user, ['*'], 'api');

        $response = $this->postJson('/api/favorites', [
            'gif_id' => 98765,
            'alias' => 'Numeric ID GIF',
            'user_id' => $user->id,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'gif_id' => '98765',
                    'alias' => 'Numeric ID GIF',
                ],
            ]);
    }
}
