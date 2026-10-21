<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Auth;

use App\Application\Auth\Commands\LoginCommand;
use App\Application\Auth\DTOs\AuthTokenDTO;
use App\Application\Auth\Ports\TokenGeneratorInterface;
use App\Application\Auth\UseCases\LoginUseCase;
use App\Domain\Auth\Entities\User;
use App\Domain\Auth\Exceptions\InvalidCredentialsException;
use App\Domain\Auth\Repositories\UserRepositoryInterface;
use App\Domain\Auth\ValueObjects\Email;
use App\Domain\Auth\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;

class LoginUseCaseTest extends TestCase
{
    public function test_successful_login_returns_token_dto(): void
    {
        $email = new Email('test@example.com');
        $user = new User(
            id: new UserId(1),
            name: 'Test User',
            email: $email,
            passwordHash: password_hash('password123', PASSWORD_BCRYPT)
        );

        $expectedDTO = new AuthTokenDTO(
            accessToken: 'sample-jwt-token',
            tokenType: 'Bearer',
            expiresIn: 1800,
            expiresAt: '2026-10-21T12:30:00+00:00',
            userId: 1,
            userEmail: 'test@example.com',
            userName: 'Test User'
        );

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository->expects($this->once())
            ->method('findByEmail')
            ->willReturn($user);

        $tokenGenerator = $this->createMock(TokenGeneratorInterface::class);
        $tokenGenerator->expects($this->once())
            ->method('generate')
            ->with($user, 30)
            ->willReturn($expectedDTO);

        $useCase = new LoginUseCase($userRepository, $tokenGenerator);
        $command = new LoginCommand('test@example.com', 'password123');

        $result = $useCase->execute($command);

        $this->assertEquals('sample-jwt-token', $result->accessToken);
        $this->assertEquals(1800, $result->expiresIn);
        $this->assertEquals(1, $result->userId);
    }

    public function test_invalid_password_throws_invalid_credentials_exception(): void
    {
        $user = new User(
            id: new UserId(1),
            name: 'Test User',
            email: new Email('test@example.com'),
            passwordHash: password_hash('correct-password', PASSWORD_BCRYPT)
        );

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository->method('findByEmail')->willReturn($user);

        $tokenGenerator = $this->createMock(TokenGeneratorInterface::class);

        $useCase = new LoginUseCase($userRepository, $tokenGenerator);
        $command = new LoginCommand('test@example.com', 'wrong-password');

        $this->expectException(InvalidCredentialsException::class);
        $useCase->execute($command);
    }

    public function test_user_not_found_throws_invalid_credentials_exception(): void
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository->method('findByEmail')->willReturn(null);

        $tokenGenerator = $this->createMock(TokenGeneratorInterface::class);

        $useCase = new LoginUseCase($userRepository, $tokenGenerator);
        $command = new LoginCommand('unknown@example.com', 'password123');

        $this->expectException(InvalidCredentialsException::class);
        $useCase->execute($command);
    }
}
