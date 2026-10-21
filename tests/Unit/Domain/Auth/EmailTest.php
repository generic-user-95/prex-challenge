<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Auth;

use App\Domain\Auth\ValueObjects\Email;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function test_valid_email_is_created_and_lowercased(): void
    {
        $email = new Email('TEST@Example.Com ');

        $this->assertEquals('test@example.com', $email->value());
        $this->assertEquals('test@example.com', (string) $email);
    }

    public function test_invalid_email_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Email('invalid-email-address');
    }

    public function test_emails_can_be_compared_for_equality(): void
    {
        $email1 = new Email('user@example.com');
        $email2 = new Email('USER@example.com');
        $email3 = new Email('other@example.com');

        $this->assertTrue($email1->equals($email2));
        $this->assertFalse($email1->equals($email3));
    }
}
