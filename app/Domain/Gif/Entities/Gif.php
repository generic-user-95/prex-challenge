<?php

declare(strict_types=1);

namespace App\Domain\Gif\Entities;

use App\Domain\Gif\ValueObjects\GifId;

final class Gif
{
    /**
     * @param array<string, mixed> $images
     */
    public function __construct(
        private GifId $id,
        private string $title,
        private string $url,
        private string $embedUrl,
        private string $rating,
        private array $images = []
    ) {
    }

    public function id(): GifId
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function embedUrl(): string
    {
        return $this->embedUrl;
    }

    public function rating(): string
    {
        return $this->rating;
    }

    /**
     * @return array<string, mixed>
     */
    public function images(): array
    {
        return $this->images;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'title' => $this->title,
            'url' => $this->url,
            'embed_url' => $this->embedUrl,
            'rating' => $this->rating,
            'images' => $this->images,
        ];
    }
}
