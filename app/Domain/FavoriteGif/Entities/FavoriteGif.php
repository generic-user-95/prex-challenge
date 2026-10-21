<?php

declare(strict_types=1);

namespace App\Domain\FavoriteGif\Entities;

use App\Domain\Auth\ValueObjects\UserId;
use App\Domain\FavoriteGif\ValueObjects\Alias;
use App\Domain\Gif\ValueObjects\GifId;
use DateTimeImmutable;

final class FavoriteGif
{
    public function __construct(
        private ?int $id,
        private UserId $userId,
        private GifId $gifId,
        private Alias $alias,
        private ?DateTimeImmutable $createdAt = null
    ) {
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function gifId(): GifId
    {
        return $this->gifId;
    }

    public function alias(): Alias
    {
        return $this->alias;
    }

    public function createdAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId->value(),
            'gif_id' => $this->gifId->value(),
            'alias' => $this->alias->value(),
            'created_at' => $this->createdAt?->format(DateTimeImmutable::ATOM),
        ];
    }
}
