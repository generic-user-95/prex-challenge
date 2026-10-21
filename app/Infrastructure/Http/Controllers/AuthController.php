<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controllers;

use App\Application\Auth\Commands\LoginCommand;
use App\Application\Auth\UseCases\LoginUseCase;
use App\Domain\Auth\Exceptions\InvalidCredentialsException;
use App\Infrastructure\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class AuthController extends Controller
{
    public function login(LoginRequest $request, LoginUseCase $loginUseCase): JsonResponse
    {
        try {
            $command = new LoginCommand(
                email: (string) $request->input('email'),
                password: (string) $request->input('password')
            );

            $tokenDTO = $loginUseCase->execute($command);

            return response()->json($tokenDTO->toArray(), Response::HTTP_OK);
        } catch (InvalidCredentialsException $e) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => $e->getMessage(),
            ], Response::HTTP_UNAUTHORIZED);
        }
    }
}
