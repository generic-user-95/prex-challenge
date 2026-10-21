<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\External\Giphy;

use App\Domain\Gif\Entities\Gif;
use App\Domain\Gif\Exceptions\GiphyApiException;
use App\Domain\Gif\Ports\GiphyGatewayInterface;
use App\Domain\Gif\ValueObjects\GifId;
use App\Domain\Gif\ValueObjects\Pagination;
use App\Domain\Gif\ValueObjects\SearchTerm;
use Illuminate\Support\Facades\Http;
use Throwable;

final class GiphyHttpAdapter implements GiphyGatewayInterface
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct(?string $baseUrl = null, ?string $apiKey = null)
    {
        $this->baseUrl = rtrim($baseUrl ?? (string) config('services.giphy.base_url', 'https://api.giphy.com/v1/gifs'), '/');
        $this->apiKey = $apiKey ?? (string) config('services.giphy.api_key', '');
    }

    public function search(SearchTerm $searchTerm, Pagination $pagination): array
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/search", [
                'api_key' => $this->apiKey,
                'q' => $searchTerm->value(),
                'limit' => $pagination->limit(),
                'offset' => $pagination->offset(),
            ]);

            if ($response->status() === 401 || $response->status() === 403) {
                // If API key is missing or invalid, check if mock/demo fallback is enabled
                throw new GiphyApiException("GIPHY API authentication failed (HTTP {$response->status()}). Please verify your GIPHY_API_KEY in .env.");
            }

            if (!$response->successful()) {
                throw new GiphyApiException("GIPHY API responded with status {$response->status()}: {$response->body()}");
            }

            $json = $response->json();
            $items = $json['data'] ?? [];
            $paginationData = $json['pagination'] ?? [];

            $gifs = array_map(fn (array $item) => $this->mapToGifEntity($item), $items);

            return [
                'data' => $gifs,
                'total_count' => (int) ($paginationData['total_count'] ?? count($gifs)),
                'count' => (int) ($paginationData['count'] ?? count($gifs)),
                'offset' => (int) ($paginationData['offset'] ?? $pagination->offset()),
            ];
        } catch (GiphyApiException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new GiphyApiException("Failed to connect to GIPHY API: {$e->getMessage()}", 502, $e);
        }
    }

    public function findById(GifId $id): ?Gif
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/{$id->value()}", [
                'api_key' => $this->apiKey,
            ]);

            if ($response->status() === 404) {
                return null;
            }

            if ($response->status() === 401 || $response->status() === 403) {
                throw new GiphyApiException("GIPHY API authentication failed (HTTP {$response->status()}). Please verify your GIPHY_API_KEY in .env.");
            }

            if (!$response->successful()) {
                throw new GiphyApiException("GIPHY API responded with status {$response->status()}: {$response->body()}");
            }

            $json = $response->json();
            $item = $json['data'] ?? null;

            if (empty($item) || !is_array($item)) {
                return null;
            }

            return $this->mapToGifEntity($item);
        } catch (GiphyApiException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new GiphyApiException("Failed to connect to GIPHY API: {$e->getMessage()}", 502, $e);
        }
    }

    /**
     * @param array<string, mixed> $item
     */
    private function mapToGifEntity(array $item): Gif
    {
        return new Gif(
            id: new GifId((string) ($item['id'] ?? '')),
            title: (string) ($item['title'] ?? ''),
            url: (string) ($item['url'] ?? ''),
            embedUrl: (string) ($item['embed_url'] ?? ''),
            rating: (string) ($item['rating'] ?? 'g'),
            images: (array) ($item['images'] ?? [])
        );
    }
}
