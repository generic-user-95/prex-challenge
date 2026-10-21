<?php

declare(strict_types=1);

namespace App\Infrastructure\Providers;

use App\Application\Auth\Ports\TokenGeneratorInterface;
use App\Domain\Auth\Repositories\UserRepositoryInterface;
use App\Domain\FavoriteGif\Repositories\FavoriteGifRepositoryInterface;
use App\Domain\Gif\Ports\GiphyGatewayInterface;
use App\Domain\InteractionLog\Repositories\InteractionLogRepositoryInterface;
use App\Infrastructure\Adapters\External\Giphy\GiphyHttpAdapter;
use App\Infrastructure\Auth\PassportTokenGenerator;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentFavoriteGifRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentInteractionLogRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

final class HexagonalServiceProvider extends ServiceProvider
{
    /**
     * Register hexagonal domain and infrastructure bindings.
     */
    public function register(): void
    {
        // Domain Repositories
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(FavoriteGifRepositoryInterface::class, EloquentFavoriteGifRepository::class);
        $this->app->bind(InteractionLogRepositoryInterface::class, EloquentInteractionLogRepository::class);

        // Application Ports
        $this->app->bind(TokenGeneratorInterface::class, PassportTokenGenerator::class);
        $this->app->bind(GiphyGatewayInterface::class, GiphyHttpAdapter::class);
    }

    public function boot(): void
    {
        //
    }
}
