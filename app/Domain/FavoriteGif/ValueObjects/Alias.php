<?php

declare(strict_types=1);

namespace App\Domain\FavoriteGif\ValueObjects;

use InvalidArgumentException;

final class Alias
{
    private string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            throw new InvalidArgumentException("Alias cannot be empty.");
        }
        if (mb_strlen($trimmed) > 255) {
            throw new InvalidArgumentException("Alias cannot exceed 255 characters.");
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
