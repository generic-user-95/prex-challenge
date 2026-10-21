<?php

declare(strict_types=1);

namespace App\Domain\Gif\ValueObjects;

use InvalidArgumentException;

final class GifId
{
    private string $value;

    public function __construct(string|int $value)
    {
        $str = trim((string) $value);
        if ($str === '') {
            throw new InvalidArgumentException("GIF ID cannot be empty.");
        }
        $this->value = $str;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
