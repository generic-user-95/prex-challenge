<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Auth;

use App\Domain\Auth\Entities\User;
use App\Domain\Auth\ValueObjects\Email;
use App\Domain\Auth\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;

class UserEntityTest extends TestCase
{
    public function test_user_entity_and_password_verification(): void
    {
        $hash = password_hash('secret123', PASSWORD_BCRYPT);
        $user = new User(
            id: new UserId(1),
            name: 'Jane Doe',
            email: new Email('jane@example.com'),
            passwordHash: $hash
        );

        $this->assertEquals(1, $user->id()->value());
        $this->assertEquals('Jane Doe', $user->name());
        $this->assertEquals('jane@example.com', $user->email()->value());
        $this->assertTrue($user->verifyPassword('secret123'));
        $this->assertFalse($user->verifyPassword('wrongpassword'));
    }
}
