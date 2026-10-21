<?php

declare(strict_types=1);

namespace App\Domain\InteractionLog\Repositories;

use App\Domain\InteractionLog\Entities\InteractionLog;

interface InteractionLogRepositoryInterface
{
    public function save(InteractionLog $log): InteractionLog;
}
