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

class GifShowApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_request_returns_401(): void
    {
        $response = $this->getJson('/api/gifs/cat123');
        $response->assertStatus(401);
    }

    public function test_show_gif_returns_gif_data_when_found(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user, ['*'], 'api');

        $sampleGif = new Gif(
            id: new GifId('gif_abc'),
            title: 'Sample Gif',
            url: 'https://giphy.com/gifs/gif_abc',
            embedUrl: 'https://giphy.com/embed/gif_abc',
            rating: 'g',
            images: ['original' => ['url' => 'https://media.giphy.com/media/gif_abc/giphy.gif']]
        );

        $mockGateway = $this->createMock(GiphyGatewayInterface::class);
        $mockGateway->expects($this->once())
            ->method('findById')
            ->willReturn($sampleGif);

        $this->app->instance(GiphyGatewayInterface::class, $mockGateway);

        $response = $this->getJson('/api/gifs/gif_abc');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => 'gif_abc',
                    'title' => 'Sample Gif',
                    'url' => 'https://giphy.com/gifs/gif_abc',
                ],
            ]);
    }

    public function test_show_gif_returns_404_when_not_found(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user, ['*'], 'api');

        $mockGateway = $this->createMock(GiphyGatewayInterface::class);
        $mockGateway->expects($this->once())
            ->method('findById')
            ->willReturn(null);

        $this->app->instance(GiphyGatewayInterface::class, $mockGateway);

        $response = $this->getJson('/api/gifs/nonexistent_id');

        $response->assertStatus(404)
            ->assertJson([
                'error' => 'Not Found',
            ]);
    }
}
