<?php

declare(strict_types=1);

namespace App\Domain\Gif\Exceptions;

use DomainException;

class GifNotFoundException extends DomainException
{
    public function __construct(string $message = 'GIF not found.')
    {
        parent::__construct($message);
    }
}
