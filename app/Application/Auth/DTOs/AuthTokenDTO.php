<?php

declare(strict_types=1);

namespace App\Application\Auth\DTOs;

final class AuthTokenDTO
{
    public function __construct(
        public readonly string $accessToken,
        public readonly string $tokenType,
        public readonly int $expiresIn,
        public readonly string $expiresAt,
        public readonly int $userId,
        public readonly string $userEmail,
        public readonly string $userName
    ) {
    }

    public function toArray(): array
    {
        return [
            'access_token' => $this->accessToken,
            'token_type' => $this->tokenType,
            'expires_in' => $this->expiresIn,
            'expires_at' => $this->expiresAt,
            'user' => [
                'id' => $this->userId,
                'email' => $this->userEmail,
                'name' => $this->userName,
            ],
        ];
    }
}
