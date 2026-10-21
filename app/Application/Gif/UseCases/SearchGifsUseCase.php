<?php

declare(strict_types=1);

namespace App\Application\Gif\UseCases;

use App\Application\Gif\DTOs\GifDTO;
use App\Application\Gif\DTOs\PaginatedGifsDTO;
use App\Application\Gif\Queries\SearchGifsQuery;
use App\Domain\Gif\Entities\Gif;
use App\Domain\Gif\Ports\GiphyGatewayInterface;
use App\Domain\Gif\ValueObjects\Pagination;
use App\Domain\Gif\ValueObjects\SearchTerm;

final class SearchGifsUseCase
{
    public function __construct(
        private GiphyGatewayInterface $giphyGateway
    ) {
    }

    public function execute(SearchGifsQuery $query): PaginatedGifsDTO
    {
        $searchTerm = new SearchTerm($query->query);
        $pagination = new Pagination($query->limit, $query->offset);

        $result = $this->giphyGateway->search($searchTerm, $pagination);

        $dtos = array_map(
            fn (Gif $gif) => GifDTO::fromEntity($gif),
            $result['data']
        );

        return new PaginatedGifsDTO(
            data: $dtos,
            totalCount: $result['total_count'],
            count: $result['count'],
            offset: $result['offset']
        );
    }
}
