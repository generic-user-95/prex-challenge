<?php

declare(strict_types=1);

namespace App\Domain\Gif\ValueObjects;

use InvalidArgumentException;

final class SearchTerm
{
    private string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            throw new InvalidArgumentException("Search query cannot be empty.");
        }
        $this->value = $trimmed;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
