<?php

declare(strict_types=1);

namespace Tests\Unit\Application\InteractionLog;

use App\Application\InteractionLog\Commands\LogInteractionCommand;
use App\Application\InteractionLog\UseCases\LogInteractionUseCase;
use App\Domain\Auth\ValueObjects\UserId;
use App\Domain\InteractionLog\Entities\InteractionLog;
use App\Domain\InteractionLog\Repositories\InteractionLogRepositoryInterface;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class LogInteractionUseCaseTest extends TestCase
{
    public function test_log_interaction_saves_to_repository(): void
    {
        $savedEntity = new InteractionLog(
            id: 1,
            userId: new UserId(1),
            service: 'GET api/gifs/search',
            requestBody: '{"query":"cats"}',
            responseStatusCode: 200,
            responseBody: '{"data":[]}',
            ipAddress: '127.0.0.1',
            createdAt: new DateTimeImmutable()
        );

        $repository = $this->createMock(InteractionLogRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('save')
            ->willReturn($savedEntity);

        $useCase = new LogInteractionUseCase($repository);
        $command = new LogInteractionCommand(
            userId: 1,
            service: 'GET api/gifs/search',
            requestBody: '{"query":"cats"}',
            responseStatusCode: 200,
            responseBody: '{"data":[]}',
            ipAddress: '127.0.0.1'
        );

        $result = $useCase->execute($command);

        $this->assertEquals(1, $result->id());
        $this->assertEquals(1, $result->userId()?->value());
        $this->assertEquals('GET api/gifs/search', $result->service());
    }
}
