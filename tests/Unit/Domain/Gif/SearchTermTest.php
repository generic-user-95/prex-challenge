<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Gif;

use App\Domain\Gif\ValueObjects\SearchTerm;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class SearchTermTest extends TestCase
{
    public function test_valid_search_term(): void
    {
        $term = new SearchTerm('  funny dogs  ');
        $this->assertEquals('funny dogs', $term->value());
        $this->assertEquals('funny dogs', (string) $term);
    }

    public function test_empty_search_term_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new SearchTerm('   ');
    }
}
