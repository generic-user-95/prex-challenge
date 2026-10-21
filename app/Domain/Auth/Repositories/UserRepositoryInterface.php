<?php

declare(strict_types=1);

namespace App\Domain\Auth\Repositories;

use App\Domain\Auth\Entities\User;
use App\Domain\Auth\ValueObjects\Email;
use App\Domain\Auth\ValueObjects\UserId;

interface UserRepositoryInterface
{
    public function findByEmail(Email $email): ?User;
    public function findById(UserId $id): ?User;
}
