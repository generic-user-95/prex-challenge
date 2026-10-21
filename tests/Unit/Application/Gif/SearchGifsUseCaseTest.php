<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Gif;

use App\Application\Gif\Queries\SearchGifsQuery;
use App\Application\Gif\UseCases\SearchGifsUseCase;
use App\Domain\Gif\Entities\Gif;
use App\Domain\Gif\Ports\GiphyGatewayInterface;
use App\Domain\Gif\ValueObjects\GifId;
use PHPUnit\Framework\TestCase;

class SearchGifsUseCaseTest extends TestCase
{
    public function test_search_gifs_delegates_to_gateway_and_returns_paginated_dto(): void
    {
        $gif = new Gif(
            id: new GifId('gif_1'),
            title: 'Sample Gif',
            url: 'https://giphy.com/gifs/gif_1',
            embedUrl: 'https://giphy.com/embed/gif_1',
            rating: 'g',
            images: []
        );

        $gateway = $this->createMock(GiphyGatewayInterface::class);
        $gateway->expects($this->once())
            ->method('search')
            ->willReturn([
                'data' => [$gif],
                'total_count' => 100,
                'count' => 1,
                'offset' => 0,
            ]);

        $useCase = new SearchGifsUseCase($gateway);
        $query = new SearchGifsQuery('dogs', 10, 0);

        $result = $useCase->execute($query);

        $this->assertEquals(100, $result->totalCount);
        $this->assertEquals(1, $result->count);
        $this->assertEquals(0, $result->offset);
        $this->assertCount(1, $result->data);
        $this->assertEquals('gif_1', $result->data[0]->id);
    }
}
