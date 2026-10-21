<?php

declare(strict_types=1);

namespace App\Application\Gif\DTOs;

use App\Domain\Gif\Entities\Gif;

final class GifDTO
{
    /**
     * @param array<string, mixed> $images
     */
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly string $url,
        public readonly string $embedUrl,
        public readonly string $rating,
        public readonly array $images
    ) {
    }

    public static function fromEntity(Gif $gif): self
    {
        return new self(
            id: $gif->id()->value(),
            title: $gif->title(),
            url: $gif->url(),
            embedUrl: $gif->embedUrl(),
            rating: $gif->rating(),
            images: $gif->images()
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'url' => $this->url,
            'embed_url' => $this->embedUrl,
            'rating' => $this->rating,
            'images' => $this->images,
        ];
    }
}
