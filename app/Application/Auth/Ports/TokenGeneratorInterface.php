<?php

declare(strict_types=1);

namespace App\Application\Auth\Ports;

use App\Application\Auth\DTOs\AuthTokenDTO;
use App\Domain\Auth\Entities\User;

interface TokenGeneratorInterface
{
    public function generate(User $user, int $lifetimeMinutes = 30): AuthTokenDTO;
}
