<?php

declare(strict_types=1);

namespace App\Domain\Gif\ValueObjects;

use InvalidArgumentException;

final class Pagination
{
    private const DEFAULT_LIMIT = 25;
    private const MAX_LIMIT = 50;
    private const DEFAULT_OFFSET = 0;

    private int $limit;
    private int $offset;

    public function __construct(?int $limit = null, ?int $offset = null)
    {
        $resolvedLimit = $limit ?? self::DEFAULT_LIMIT;
        $resolvedOffset = $offset ?? self::DEFAULT_OFFSET;

        if ($resolvedLimit < 1) {
            throw new InvalidArgumentException("Limit must be greater than or equal to 1.");
        }
        if ($resolvedLimit > self::MAX_LIMIT) {
            $resolvedLimit = self::MAX_LIMIT;
        }

        if ($resolvedOffset < 0) {
            throw new InvalidArgumentException("Offset cannot be negative.");
        }

        $this->limit = $resolvedLimit;
        $this->offset = $resolvedOffset;
    }

    public function limit(): int
    {
        return $this->limit;
    }

    public function offset(): int
    {
        return $this->offset;
    }
}
