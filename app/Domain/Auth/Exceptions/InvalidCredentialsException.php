<?php

declare(strict_types=1);

namespace App\Domain\Auth\Exceptions;

use DomainException;

class InvalidCredentialsException extends DomainException
{
    public function __construct(string $message = 'Invalid email or password.')
    {
        parent::__construct($message);
    }
}
