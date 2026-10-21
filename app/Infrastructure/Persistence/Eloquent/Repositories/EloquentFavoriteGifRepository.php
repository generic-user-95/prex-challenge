<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Auth\ValueObjects\UserId;
use App\Domain\FavoriteGif\Entities\FavoriteGif as DomainFavoriteGif;
use App\Domain\FavoriteGif\Repositories\FavoriteGifRepositoryInterface;
use App\Domain\FavoriteGif\ValueObjects\Alias;
use App\Domain\Gif\ValueObjects\GifId;
use App\Infrastructure\Persistence\Eloquent\Models\FavoriteGifModel;
use DateTimeImmutable;

final class EloquentFavoriteGifRepository implements FavoriteGifRepositoryInterface
{
    public function save(DomainFavoriteGif $favoriteGif): DomainFavoriteGif
    {
        $model = FavoriteGifModel::create([
            'user_id' => $favoriteGif->userId()->value(),
            'gif_id' => $favoriteGif->gifId()->value(),
            'alias' => $favoriteGif->alias()->value(),
        ]);

        return $this->toDomain($model);
    }

    public function findByUserIdAndGifId(UserId $userId, GifId $gifId): ?DomainFavoriteGif
    {
        $model = FavoriteGifModel::where('user_id', $userId->value())
            ->where('gif_id', $gifId->value())
            ->first();

        if ($model === null) {
            return null;
        }

        return $this->toDomain($model);
    }

    public function findByUserId(UserId $userId): array
    {
        $models = FavoriteGifModel::where('user_id', $userId->value())->get();

        return $models->map(fn (FavoriteGifModel $m) => $this->toDomain($m))->all();
    }

    public function delete(int $id): bool
    {
        return (bool) FavoriteGifModel::destroy($id);
    }

    private function toDomain(FavoriteGifModel $model): DomainFavoriteGif
    {
        return new DomainFavoriteGif(
            id: (int) $model->id,
            userId: new UserId((int) $model->user_id),
            gifId: new GifId((string) $model->gif_id),
            alias: new Alias((string) $model->alias),
            createdAt: $model->created_at ? DateTimeImmutable::createFromMutable($model->created_at) : null
        );
    }
}
