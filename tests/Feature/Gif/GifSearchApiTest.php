<?php

declare(strict_types=1);

namespace Tests\Feature\Gif;

use App\Domain\Gif\Entities\Gif;
use App\Domain\Gif\Ports\GiphyGatewayInterface;
use App\Domain\Gif\ValueObjects\GifId;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class GifSearchApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_request_returns_401(): void
    {
        $response = $this->getJson('/api/gifs/search?query=cat');
        $response->assertStatus(401);
    }

    public function test_missing_query_parameter_returns_422(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user, ['*'], 'api');

        $response = $this->getJson('/api/gifs/search');
        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors' => ['query']]);
    }

    public function test_search_gifs_returns_collection_and_pagination(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user, ['*'], 'api');

        $sampleGif = new Gif(
            id: new GifId('cat_123'),
            title: 'Funny Cat Jumping',
            url: 'https://giphy.com/gifs/cat_123',
            embedUrl: 'https://giphy.com/embed/cat_123',
            rating: 'g',
            images: [
                'original' => [
                    'url' => 'https://media.giphy.com/media/cat_123/giphy.gif',
                    'width' => 480,
                    'height' => 360,
                ],
            ]
        );

        $mockGateway = $this->createMock(GiphyGatewayInterface::class);
        $mockGateway->expects($this->once())
            ->method('search')
            ->willReturn([
                'data' => [$sampleGif],
                'total_count' => 1,
                'count' => 1,
                'offset' => 0,
            ]);

        $this->app->instance(GiphyGatewayInterface::class, $mockGateway);

        $response = $this->getJson('/api/gifs/search?query=cat&limit=10&offset=0');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'url', 'embed_url', 'rating', 'images'],
                ],
                'pagination' => ['total_count', 'count', 'offset'],
            ])
            ->assertJson([
                'data' => [
                    [
                        'id' => 'cat_123',
                        'title' => 'Funny Cat Jumping',
                    ],
                ],
                'pagination' => [
                    'total_count' => 1,
                    'count' => 1,
                    'offset' => 0,
                ],
            ]);
    }
}
