<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controllers;

use App\Application\Gif\Queries\GetGifByIdQuery;
use App\Application\Gif\Queries\SearchGifsQuery;
use App\Application\Gif\UseCases\GetGifByIdUseCase;
use App\Application\Gif\UseCases\SearchGifsUseCase;
use App\Domain\Gif\Exceptions\GifNotFoundException;
use App\Domain\Gif\Exceptions\GiphyApiException;
use App\Infrastructure\Http\Requests\SearchGifsRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class GifController extends Controller
{
    public function search(SearchGifsRequest $request, SearchGifsUseCase $searchGifsUseCase): JsonResponse
    {
        try {
            $query = new SearchGifsQuery(
                query: (string) $request->input('query'),
                limit: $request->has('limit') ? (int) $request->input('limit') : null,
                offset: $request->has('offset') ? (int) $request->input('offset') : null
            );

            $result = $searchGifsUseCase->execute($query);

            return response()->json($result->toArray(), Response::HTTP_OK);
        } catch (GiphyApiException $e) {
            return response()->json([
                'error' => 'Bad Gateway',
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_GATEWAY);
        }
    }

    public function show(string|int $id, GetGifByIdUseCase $getGifByIdUseCase): JsonResponse
    {
        try {
            $query = new GetGifByIdQuery($id);
            $gif = $getGifByIdUseCase->execute($query);

            return response()->json([
                'data' => $gif->toArray(),
            ], Response::HTTP_OK);
        } catch (GifNotFoundException $e) {
            return response()->json([
                'error' => 'Not Found',
                'message' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        } catch (GiphyApiException $e) {
            return response()->json([
                'error' => 'Bad Gateway',
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_GATEWAY);
        }
    }
}
