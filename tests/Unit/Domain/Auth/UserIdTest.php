<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Auth;

use App\Domain\Auth\ValueObjects\UserId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class UserIdTest extends TestCase
{
    public function test_valid_user_id(): void
    {
        $id = new UserId(42);
        $this->assertEquals(42, $id->value());
        $this->assertEquals('42', (string) $id);
    }

    public function test_zero_or_negative_user_id_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new UserId(0);
    }

    public function test_negative_user_id_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new UserId(-5);
    }
}
