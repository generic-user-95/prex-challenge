<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Auth\Entities\User as DomainUser;
use App\Domain\Auth\Repositories\UserRepositoryInterface;
use App\Domain\Auth\ValueObjects\Email;
use App\Domain\Auth\ValueObjects\UserId;
use App\Models\User as EloquentUser;

final class EloquentUserRepository implements UserRepositoryInterface
{
    public function findByEmail(Email $email): ?DomainUser
    {
        $eloquentUser = EloquentUser::where('email', $email->value())->first();
        if ($eloquentUser === null) {
            return null;
        }

        return $this->toDomain($eloquentUser);
    }

    public function findById(UserId $id): ?DomainUser
    {
        $eloquentUser = EloquentUser::find($id->value());
        if ($eloquentUser === null) {
            return null;
        }

        return $this->toDomain($eloquentUser);
    }

    private function toDomain(EloquentUser $model): DomainUser
    {
        return new DomainUser(
            id: new UserId($model->id),
            name: $model->name,
            email: new Email($model->email),
            passwordHash: $model->password
        );
    }
}
