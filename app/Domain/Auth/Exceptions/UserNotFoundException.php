<?php

declare(strict_types=1);

namespace App\Domain\Auth\Exceptions;

use DomainException;

class UserNotFoundException extends DomainException
{
    public function __construct(string $message = 'User not found.')
    {
        parent::__construct($message);
    }
}
