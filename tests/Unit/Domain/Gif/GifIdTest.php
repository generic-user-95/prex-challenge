<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Gif;

use App\Domain\Gif\ValueObjects\GifId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class GifIdTest extends TestCase
{
    public function test_valid_gif_id(): void
    {
        $id1 = new GifId('3o7TKxGqQz9h6yZ1qA');
        $id2 = new GifId(12345);

        $this->assertEquals('3o7TKxGqQz9h6yZ1qA', $id1->value());
        $this->assertEquals('12345', $id2->value());
    }

    public function test_empty_gif_id_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new GifId('   ');
    }

    public function test_equality(): void
    {
        $id1 = new GifId('abc');
        $id2 = new GifId('abc');
        $id3 = new GifId('xyz');

        $this->assertTrue($id1->equals($id2));
        $this->assertFalse($id1->equals($id3));
    }
}
