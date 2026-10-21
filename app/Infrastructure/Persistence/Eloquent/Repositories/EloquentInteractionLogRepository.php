<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Auth\ValueObjects\UserId;
use App\Domain\InteractionLog\Entities\InteractionLog;
use App\Domain\InteractionLog\Repositories\InteractionLogRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\InteractionLogModel;
use DateTimeImmutable;

final class EloquentInteractionLogRepository implements InteractionLogRepositoryInterface
{
    public function save(InteractionLog $log): InteractionLog
    {
        $model = InteractionLogModel::create([
            'user_id' => $log->userId()?->value(),
            'service' => $log->service(),
            'request_body' => $log->requestBody(),
            'response_status_code' => $log->responseStatusCode(),
            'response_body' => $log->responseBody(),
            'ip_address' => $log->ipAddress(),
        ]);

        return new InteractionLog(
            id: (int) $model->id,
            userId: $model->user_id ? new UserId((int) $model->user_id) : null,
            service: $model->service,
            requestBody: $model->request_body,
            responseStatusCode: (int) $model->response_status_code,
            responseBody: $model->response_body,
            ipAddress: $model->ip_address,
            createdAt: $model->created_at ? DateTimeImmutable::createFromMutable($model->created_at) : null
        );
    }
}
