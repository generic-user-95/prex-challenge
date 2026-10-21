<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\FavoriteGif;

use App\Domain\Auth\ValueObjects\UserId;
use App\Domain\FavoriteGif\Entities\FavoriteGif;
use App\Domain\FavoriteGif\ValueObjects\Alias;
use App\Domain\Gif\ValueObjects\GifId;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class FavoriteGifEntityTest extends TestCase
{
    public function test_favorite_gif_entity(): void
    {
        $now = new DateTimeImmutable('2026-10-21T12:00:00+00:00');
        $favorite = new FavoriteGif(
            id: 1,
            userId: new UserId(5),
            gifId: new GifId('xyz789'),
            alias: new Alias('Funny dog'),
            createdAt: $now
        );

        $this->assertEquals(1, $favorite->id());
        $this->assertEquals(5, $favorite->userId()->value());
        $this->assertEquals('xyz789', $favorite->gifId()->value());
        $this->assertEquals('Funny dog', $favorite->alias()->value());
        $this->assertEquals($now, $favorite->createdAt());

        $array = $favorite->toArray();
        $this->assertEquals(1, $array['id']);
        $this->assertEquals(5, $array['user_id']);
        $this->assertEquals('xyz789', $array['gif_id']);
        $this->assertEquals('Funny dog', $array['alias']);
    }
}
