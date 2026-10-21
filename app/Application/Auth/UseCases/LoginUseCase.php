<?php

declare(strict_types=1);

namespace App\Application\Auth\UseCases;

use App\Application\Auth\Commands\LoginCommand;
use App\Application\Auth\DTOs\AuthTokenDTO;
use App\Application\Auth\Ports\TokenGeneratorInterface;
use App\Domain\Auth\Exceptions\InvalidCredentialsException;
use App\Domain\Auth\Repositories\UserRepositoryInterface;
use App\Domain\Auth\ValueObjects\Email;

final class LoginUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private TokenGeneratorInterface $tokenGenerator
    ) {
    }

    public function execute(LoginCommand $command): AuthTokenDTO
    {
        try {
            $email = new Email($command->email);
        } catch (\InvalidArgumentException $e) {
            throw new InvalidCredentialsException('Invalid email or password.');
        }

        $user = $this->userRepository->findByEmail($email);
        if ($user === null) {
            throw new InvalidCredentialsException('Invalid email or password.');
        }

        if (!$user->verifyPassword($command->password)) {
            throw new InvalidCredentialsException('Invalid email or password.');
        }

        // Generate token with 30 minutes lifetime as per challenge specification
        return $this->tokenGenerator->generate($user, 30);
    }
}
