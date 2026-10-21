<?php

declare(strict_types=1);

namespace App\Application\InteractionLog\UseCases;

use App\Application\InteractionLog\Commands\LogInteractionCommand;
use App\Domain\Auth\ValueObjects\UserId;
use App\Domain\InteractionLog\Entities\InteractionLog;
use App\Domain\InteractionLog\Repositories\InteractionLogRepositoryInterface;
use DateTimeImmutable;

final class LogInteractionUseCase
{
    public function __construct(
        private InteractionLogRepositoryInterface $interactionLogRepository
    ) {
    }

    public function execute(LogInteractionCommand $command): InteractionLog
    {
        $userId = $command->userId !== null ? new UserId($command->userId) : null;

        $log = new InteractionLog(
            id: null,
            userId: $userId,
            service: $command->service,
            requestBody: $command->requestBody,
            responseStatusCode: $command->responseStatusCode,
            responseBody: $command->responseBody,
            ipAddress: $command->ipAddress,
            createdAt: new DateTimeImmutable()
        );

        return $this->interactionLogRepository->save($log);
    }
}
