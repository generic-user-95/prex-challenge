<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Gif;

use App\Domain\Gif\Entities\Gif;
use App\Domain\Gif\ValueObjects\GifId;
use PHPUnit\Framework\TestCase;

class GifEntityTest extends TestCase
{
    public function test_gif_entity_instantiation_and_serialization(): void
    {
        $images = [
            'original' => [
                'url' => 'https://media.giphy.com/media/abc/giphy.gif',
                'width' => 480,
                'height' => 320,
            ],
        ];

        $gif = new Gif(
            id: new GifId('gif_123'),
            title: 'Cool Cat',
            url: 'https://giphy.com/gifs/gif_123',
            embedUrl: 'https://giphy.com/embed/gif_123',
            rating: 'g',
            images: $images
        );

        $this->assertEquals('gif_123', $gif->id()->value());
        $this->assertEquals('Cool Cat', $gif->title());
        $this->assertEquals('https://giphy.com/gifs/gif_123', $gif->url());
        $this->assertEquals('https://giphy.com/embed/gif_123', $gif->embedUrl());
        $this->assertEquals('g', $gif->rating());
        $this->assertEquals($images, $gif->images());

        $array = $gif->toArray();
        $this->assertEquals('gif_123', $array['id']);
        $this->assertEquals('Cool Cat', $array['title']);
    }
}
