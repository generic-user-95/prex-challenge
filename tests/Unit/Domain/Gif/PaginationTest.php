<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Gif;

use App\Domain\Gif\ValueObjects\Pagination;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PaginationTest extends TestCase
{
    public function test_default_pagination(): void
    {
        $pagination = new Pagination();
        $this->assertEquals(25, $pagination->limit());
        $this->assertEquals(0, $pagination->offset());
    }

    public function test_custom_valid_pagination(): void
    {
        $pagination = new Pagination(10, 20);
        $this->assertEquals(10, $pagination->limit());
        $this->assertEquals(20, $pagination->offset());
    }

    public function test_limit_is_clamped_to_max_50(): void
    {
        $pagination = new Pagination(100, 0);
        $this->assertEquals(50, $pagination->limit());
    }

    public function test_negative_limit_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Pagination(0);
    }

    public function test_negative_offset_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Pagination(10, -1);
    }
}
