<?php

declare(strict_types=1);

namespace App\Application\InteractionLog\Commands;

final class LogInteractionCommand
{
    public function __construct(
        public readonly ?int $userId,
        public readonly string $service,
        public readonly ?string $requestBody,
        public readonly int $responseStatusCode,
        public readonly ?string $responseBody,
        public readonly ?string $ipAddress
    ) {
    }
}
