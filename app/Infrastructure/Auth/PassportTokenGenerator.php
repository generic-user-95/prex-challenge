<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth;

use App\Application\Auth\DTOs\AuthTokenDTO;
use App\Application\Auth\Ports\TokenGeneratorInterface;
use App\Domain\Auth\Entities\User as DomainUser;
use App\Models\User as EloquentUser;
use Carbon\Carbon;
use RuntimeException;

final class PassportTokenGenerator implements TokenGeneratorInterface
{
    public function generate(DomainUser $user, int $lifetimeMinutes = 30): AuthTokenDTO
    {
        $eloquentUser = EloquentUser::find($user->id()->value());
        if ($eloquentUser === null) {
            throw new RuntimeException("Eloquent user not found for ID: {$user->id()->value()}");
        }

        $tokenResult = $eloquentUser->createToken('Personal Access Token');
        $token = $tokenResult->token;

        $expiresAt = Carbon::now()->addMinutes($lifetimeMinutes);
        $token->expires_at = $expiresAt;
        $token->save();

        return new AuthTokenDTO(
            accessToken: $tokenResult->accessToken,
            tokenType: 'Bearer',
            expiresIn: $lifetimeMinutes * 60,
            expiresAt: $expiresAt->toIso8601String(),
            userId: $user->id()->value(),
            userEmail: $user->email()->value(),
            userName: $user->name()
        );
    }
}
