<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\FavoriteGif;

use App\Domain\FavoriteGif\ValueObjects\Alias;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class AliasTest extends TestCase
{
    public function test_valid_alias(): void
    {
        $alias = new Alias('My Favorite GIF');
        $this->assertEquals('My Favorite GIF', $alias->value());
        $this->assertEquals('My Favorite GIF', (string) $alias);
    }

    public function test_empty_alias_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Alias('   ');
    }

    public function test_too_long_alias_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Alias(str_repeat('a', 256));
    }
}
