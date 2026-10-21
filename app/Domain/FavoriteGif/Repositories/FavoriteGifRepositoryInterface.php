<?php

declare(strict_types=1);

namespace App\Domain\FavoriteGif\Repositories;

use App\Domain\Auth\ValueObjects\UserId;
use App\Domain\FavoriteGif\Entities\FavoriteGif;
use App\Domain\Gif\ValueObjects\GifId;

interface FavoriteGifRepositoryInterface
{
    public function save(FavoriteGif $favoriteGif): FavoriteGif;

    public function findByUserIdAndGifId(UserId $userId, GifId $gifId): ?FavoriteGif;

    /**
     * @return list<FavoriteGif>
     */
    public function findByUserId(UserId $userId): array;

    public function delete(int $id): bool;
}
