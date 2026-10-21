<?php

declare(strict_types=1);

namespace Tests\Unit\Application\FavoriteGif;

use App\Application\FavoriteGif\Commands\SaveFavoriteGifCommand;
use App\Application\FavoriteGif\UseCases\SaveFavoriteGifUseCase;
use App\Domain\Auth\Entities\User;
use App\Domain\Auth\Exceptions\UserNotFoundException;
use App\Domain\Auth\Repositories\UserRepositoryInterface;
use App\Domain\Auth\ValueObjects\Email;
use App\Domain\Auth\ValueObjects\UserId;
use App\Domain\FavoriteGif\Entities\FavoriteGif;
use App\Domain\FavoriteGif\Repositories\FavoriteGifRepositoryInterface;
use App\Domain\FavoriteGif\ValueObjects\Alias;
use App\Domain\Gif\ValueObjects\GifId;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class SaveFavoriteGifUseCaseTest extends TestCase
{
    public function test_save_favorite_gif_successfully(): void
    {
        $user = new User(
            id: new UserId(2),
            name: 'John',
            email: new Email('john@example.com'),
            passwordHash: 'hash'
        );

        $savedEntity = new FavoriteGif(
            id: 1,
            userId: new UserId(2),
            gifId: new GifId('fav_gif_100'),
            alias: new Alias('Hilarious Cat'),
            createdAt: new DateTimeImmutable()
        );

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository->expects($this->once())
            ->method('findById')
            ->willReturn($user);

        $favoriteRepository = $this->createMock(FavoriteGifRepositoryInterface::class);
        $favoriteRepository->expects($this->once())
            ->method('save')
            ->willReturn($savedEntity);

        $useCase = new SaveFavoriteGifUseCase($favoriteRepository, $userRepository);
        $command = new SaveFavoriteGifCommand(2, 'fav_gif_100', 'Hilarious Cat');

        $result = $useCase->execute($command);

        $this->assertEquals(1, $result->id);
        $this->assertEquals(2, $result->userId);
        $this->assertEquals('fav_gif_100', $result->gifId);
        $this->assertEquals('Hilarious Cat', $result->alias);
    }

    public function test_save_favorite_gif_throws_exception_if_user_does_not_exist(): void
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository->expects($this->once())
            ->method('findById')
            ->willReturn(null);

        $favoriteRepository = $this->createMock(FavoriteGifRepositoryInterface::class);

        $useCase = new SaveFavoriteGifUseCase($favoriteRepository, $userRepository);
        $command = new SaveFavoriteGifCommand(999, 'fav_gif_100', 'Hilarious Cat');

        $this->expectException(UserNotFoundException::class);
        $useCase->execute($command);
    }
}
