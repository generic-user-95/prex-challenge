<?php

declare(strict_types=1);

namespace App\Domain\InteractionLog\Entities;

use App\Domain\Auth\ValueObjects\UserId;
use DateTimeImmutable;

final class InteractionLog
{
    public function __construct(
        private ?int $id,
        private ?UserId $userId,
        private string $service,
        private ?string $requestBody,
        private int $responseStatusCode,
        private ?string $responseBody,
        private ?string $ipAddress,
        private ?DateTimeImmutable $createdAt = null
    ) {
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function userId(): ?UserId
    {
        return $this->userId;
    }

    public function service(): string
    {
        return $this->service;
    }

    public function requestBody(): ?string
    {
        return $this->requestBody;
    }

    public function responseStatusCode(): int
    {
        return $this->responseStatusCode;
    }

    public function responseBody(): ?string
    {
        return $this->responseBody;
    }

    public function ipAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function createdAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId?->value(),
            'service' => $this->service,
            'request_body' => $this->requestBody,
            'response_status_code' => $this->responseStatusCode,
            'response_body' => $this->responseBody,
            'ip_address' => $this->ipAddress,
            'created_at' => $this->createdAt?->format(DateTimeImmutable::ATOM),
        ];
    }
}
