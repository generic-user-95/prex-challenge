<?php

declare(strict_types=1);

namespace App\Application\FavoriteGif\Commands;

final class SaveFavoriteGifCommand
{
    public function __construct(
        public readonly int $userId,
        public readonly string|int $gifId,
        public readonly string $alias
    ) {
    }
}
