<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\InteractionLog;

use App\Domain\Auth\ValueObjects\UserId;
use App\Domain\InteractionLog\Entities\InteractionLog;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class InteractionLogEntityTest extends TestCase
{
    public function test_interaction_log_entity(): void
    {
        $now = new DateTimeImmutable('2026-10-21T12:00:00+00:00');
        $log = new InteractionLog(
            id: 10,
            userId: new UserId(3),
            service: 'GET api/gifs/search',
            requestBody: '{"query":"cats"}',
            responseStatusCode: 200,
            responseBody: '{"data":[]}',
            ipAddress: '192.168.1.1',
            createdAt: $now
        );

        $this->assertEquals(10, $log->id());
        $this->assertEquals(3, $log->userId()->value());
        $this->assertEquals('GET api/gifs/search', $log->service());
        $this->assertEquals('{"query":"cats"}', $log->requestBody());
        $this->assertEquals(200, $log->responseStatusCode());
        $this->assertEquals('{"data":[]}', $log->responseBody());
        $this->assertEquals('192.168.1.1', $log->ipAddress());

        $array = $log->toArray();
        $this->assertEquals(10, $array['id']);
        $this->assertEquals(3, $array['user_id']);
        $this->assertEquals(200, $array['response_status_code']);
    }
}
