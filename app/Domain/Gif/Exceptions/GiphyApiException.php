<?php

declare(strict_types=1);

namespace App\Domain\Gif\Exceptions;

use RuntimeException;

class GiphyApiException extends RuntimeException
{
    public function __construct(string $message = 'Error communicating with GIPHY API.', int $code = 502, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
