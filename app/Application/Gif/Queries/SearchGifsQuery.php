<?php

declare(strict_types=1);

namespace App\Application\Gif\Queries;

final class SearchGifsQuery
{
    public function __construct(
        public readonly string $query,
        public readonly ?int $limit = null,
        public readonly ?int $offset = null
    ) {
    }
}
