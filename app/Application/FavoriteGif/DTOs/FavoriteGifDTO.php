<?php

declare(strict_types=1);

namespace App\Application\FavoriteGif\DTOs;

use App\Domain\FavoriteGif\Entities\FavoriteGif;
use DateTimeImmutable;

final class FavoriteGifDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $userId,
        public readonly string $gifId,
        public readonly string $alias,
        public readonly ?string $createdAt
    ) {
    }

    public static function fromEntity(FavoriteGif $favoriteGif): self
    {
        return new self(
            id: $favoriteGif->id(),
            userId: $favoriteGif->userId()->value(),
            gifId: $favoriteGif->gifId()->value(),
            alias: $favoriteGif->alias()->value(),
            createdAt: $favoriteGif->createdAt()?->format(DateTimeImmutable::ATOM)
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'gif_id' => $this->gifId,
            'alias' => $this->alias,
            'created_at' => $this->createdAt,
        ];
    }
}
