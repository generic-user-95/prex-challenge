<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controllers;

use App\Application\FavoriteGif\Commands\SaveFavoriteGifCommand;
use App\Application\FavoriteGif\UseCases\SaveFavoriteGifUseCase;
use App\Domain\Auth\Exceptions\UserNotFoundException;
use App\Infrastructure\Http\Requests\SaveFavoriteGifRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class FavoriteGifController extends Controller
{
    public function store(SaveFavoriteGifRequest $request, SaveFavoriteGifUseCase $saveFavoriteGifUseCase): JsonResponse
    {
        try {
            $command = new SaveFavoriteGifCommand(
                userId: (int) $request->input('user_id'),
                gifId: $request->input('gif_id'),
                alias: (string) $request->input('alias')
            );

            $dto = $saveFavoriteGifUseCase->execute($command);

            return response()->json([
                'message' => 'Favorite GIF saved successfully.',
                'data' => $dto->toArray(),
            ], Response::HTTP_CREATED);
        } catch (UserNotFoundException $e) {
            return response()->json([
                'error' => 'Not Found',
                'message' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
