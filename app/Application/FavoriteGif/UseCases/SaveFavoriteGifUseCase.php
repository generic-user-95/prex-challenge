<?php

declare(strict_types=1);

namespace App\Application\FavoriteGif\UseCases;

use App\Application\FavoriteGif\Commands\SaveFavoriteGifCommand;
use App\Application\FavoriteGif\DTOs\FavoriteGifDTO;
use App\Domain\Auth\Exceptions\UserNotFoundException;
use App\Domain\Auth\Repositories\UserRepositoryInterface;
use App\Domain\Auth\ValueObjects\UserId;
use App\Domain\FavoriteGif\Entities\FavoriteGif;
use App\Domain\FavoriteGif\Repositories\FavoriteGifRepositoryInterface;
use App\Domain\FavoriteGif\ValueObjects\Alias;
use App\Domain\Gif\ValueObjects\GifId;
use DateTimeImmutable;

final class SaveFavoriteGifUseCase
{
    public function __construct(
        private FavoriteGifRepositoryInterface $favoriteGifRepository,
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function execute(SaveFavoriteGifCommand $command): FavoriteGifDTO
    {
        $userId = new UserId($command->userId);
        $user = $this->userRepository->findById($userId);
        if ($user === null) {
            throw new UserNotFoundException("User with ID {$command->userId} does not exist.");
        }

        $gifId = new GifId($command->gifId);
        $alias = new Alias($command->alias);

        $favorite = new FavoriteGif(
            id: null,
            userId: $userId,
            gifId: $gifId,
            alias: $alias,
            createdAt: new DateTimeImmutable()
        );

        $savedFavorite = $this->favoriteGifRepository->save($favorite);

        return FavoriteGifDTO::fromEntity($savedFavorite);
    }
}
