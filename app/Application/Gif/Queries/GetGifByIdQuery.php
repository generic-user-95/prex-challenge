<?php

declare(strict_types=1);

namespace App\Application\Gif\Queries;

final class GetGifByIdQuery
{
    public function __construct(
        public readonly string|int $id
    ) {
    }
}
