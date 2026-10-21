<?php

declare(strict_types=1);

namespace App\Domain\Gif\Ports;

use App\Domain\Gif\Entities\Gif;
use App\Domain\Gif\ValueObjects\GifId;
use App\Domain\Gif\ValueObjects\Pagination;
use App\Domain\Gif\ValueObjects\SearchTerm;

interface GiphyGatewayInterface
{
    /**
     * @return array{data: list<Gif>, total_count: int, count: int, offset: int}
     */
    public function search(SearchTerm $searchTerm, Pagination $pagination): array;

    public function findById(GifId $id): ?Gif;
}
