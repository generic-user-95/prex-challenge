<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Gif;

use App\Application\Gif\Queries\GetGifByIdQuery;
use App\Application\Gif\UseCases\GetGifByIdUseCase;
use App\Domain\Gif\Entities\Gif;
use App\Domain\Gif\Exceptions\GifNotFoundException;
use App\Domain\Gif\Ports\GiphyGatewayInterface;
use App\Domain\Gif\ValueObjects\GifId;
use PHPUnit\Framework\TestCase;

class GetGifByIdUseCaseTest extends TestCase
{
    public function test_get_gif_by_id_returns_gif_dto_when_found(): void
    {
        $gif = new Gif(
            id: new GifId('xyz999'),
            title: 'Found Gif',
            url: 'https://giphy.com/gifs/xyz999',
            embedUrl: 'https://giphy.com/embed/xyz999',
            rating: 'pg',
            images: ['original' => ['url' => 'https://example.com/gif.gif']]
        );

        $gateway = $this->createMock(GiphyGatewayInterface::class);
        $gateway->expects($this->once())
            ->method('findById')
            ->willReturn($gif);

        $useCase = new GetGifByIdUseCase($gateway);
        $result = $useCase->execute(new GetGifByIdQuery('xyz999'));

        $this->assertEquals('xyz999', $result->id);
        $this->assertEquals('Found Gif', $result->title);
    }

    public function test_get_gif_by_id_throws_exception_when_not_found(): void
    {
        $gateway = $this->createMock(GiphyGatewayInterface::class);
        $gateway->expects($this->once())
            ->method('findById')
            ->willReturn(null);

        $useCase = new GetGifByIdUseCase($gateway);

        $this->expectException(GifNotFoundException::class);
        $useCase->execute(new GetGifByIdQuery('not_exist'));
    }
}
