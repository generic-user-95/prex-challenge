<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Middleware;

use App\Application\InteractionLog\Commands\LogInteractionCommand;
use App\Application\InteractionLog\UseCases\LogInteractionUseCase;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class LogInteractionMiddleware
{
    public function __construct(
        private LogInteractionUseCase $logInteractionUseCase
    ) {
    }

    /**
     * Handle an incoming request and log the interaction.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->is('api/*')) {
            return $next($request);
        }

        try {
            $response = $next($request);
        } catch (Throwable $e) {
            $renderedResponse = app(\Illuminate\Contracts\Debug\ExceptionHandler::class)->render($request, $e);
            $this->logInteraction($request, $renderedResponse);
            throw $e;
        }

        $this->logInteraction($request, $response);

        return $response;
    }

    private function logInteraction(Request $request, Response $response): void
    {
        try {
            $user = $request->user();
            $userId = $user?->id !== null ? (int) $user->id : null;

            // If not authenticated via token, check if user_id was passed in request (e.g., in favorites request)
            if ($userId === null && $request->has('user_id') && is_numeric($request->input('user_id'))) {
                $userId = (int) $request->input('user_id');
            }

            // Sanitize sensitive request parameters
            $requestPayload = $request->all();
            if (isset($requestPayload['password'])) {
                $requestPayload['password'] = '********';
            }

            $service = $request->method() . ' ' . $request->path();
            $requestBody = !empty($requestPayload) ? json_encode($requestPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null;
            $statusCode = $response->getStatusCode();

            $content = $response->getContent();
            // Truncate response body if overly large (e.g. > 60KB) to ensure DB safety
            $responseBody = is_string($content) ? mb_substr($content, 0, 60000) : null;

            $ipAddress = $request->ip();

            $command = new LogInteractionCommand(
                userId: $userId,
                service: $service,
                requestBody: $requestBody,
                responseStatusCode: $statusCode,
                responseBody: $responseBody,
                ipAddress: $ipAddress
            );

            $this->logInteractionUseCase->execute($command);
        } catch (Throwable $e) {
            // Silently log exception to Laravel log so interaction logging never causes request failure
            report($e);
        }
    }
}
