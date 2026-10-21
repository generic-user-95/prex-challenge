<?php

declare(strict_types=1);

namespace App\Application\Gif\DTOs;

final class PaginatedGifsDTO
{
    /**
     * @param list<GifDTO> $data
     */
    public function __construct(
        public readonly array $data,
        public readonly int $totalCount,
        public readonly int $count,
        public readonly int $offset
    ) {
    }

    public function toArray(): array
    {
        return [
            'data' => array_map(fn (GifDTO $gif) => $gif->toArray(), $this->data),
            'pagination' => [
                'total_count' => $this->totalCount,
                'count' => $this->count,
                'offset' => $this->offset,
            ],
        ];
    }
}
