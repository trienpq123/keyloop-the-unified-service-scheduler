<?php

declare(strict_types=1);

use App\Http\Middleware\AssignRequestId;
use App\Http\Responses\ApiResponse;
use App\Shared\Exceptions\DomainException;
use App\Shared\Exceptions\ErrorCode;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            AssignRequestId::class, // Assign a unique request ID to each API request
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Handle domain exceptions (business rule violations)
        $exceptions->render(function (DomainException $e, Request $request): ?JsonResponse {
            if (! $request->expectsJson()) {
                return null;
            }

            return ApiResponse::error(
                code: $e->errorCode(),
                message: $e->getMessage(),
                details: $e->details(),
            );
        });

        // Handle validation exceptions
        $exceptions->render(function (ValidationException $e, Request $request): ?JsonResponse {
            if (! $request->expectsJson()) {
                return null;
            }

            $details = [];
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $details[] = ['field' => $field, 'message' => $message];
                }
            }

            return ApiResponse::validationError($details, $e->getMessage());
        });

        // Handle model not found exceptions
        $exceptions->render(function (ModelNotFoundException $e, Request $request): ?JsonResponse {
            if (! $request->expectsJson()) {
                return null;
            }

            $resource = $e->getModel() !== '' ? class_basename($e->getModel()) : 'Resource';
            $ids = implode(', ', (array) $e->getIds());

            return ApiResponse::error(
                code: ErrorCode::ResourceNotFound,
                message: $ids !== ''
                    ? "{$resource} [{$ids}] was not found."
                    : ErrorCode::ResourceNotFound->defaultMessage(),
            );
        });

        // Handle not found exceptions
        $exceptions->render(function (NotFoundHttpException $e, Request $request): ?JsonResponse {
            if (! $request->expectsJson()) {
                return null;
            }

            // Unwrap model-not-found if it arrived wrapped in a HTTP 404.
            $previous = $e->getPrevious();
            if ($previous instanceof ModelNotFoundException) {
                $resource = $previous->getModel() !== '' ? class_basename($previous->getModel()) : 'Resource';
                $ids = implode(', ', (array) $previous->getIds());

                return ApiResponse::error(
                    code: ErrorCode::ResourceNotFound,
                    message: $ids !== ''
                        ? "{$resource} [{$ids}] was not found."
                        : ErrorCode::ResourceNotFound->defaultMessage(),
                );
            }

            return ApiResponse::error(
                code: ErrorCode::ResourceNotFound,
                message: $e->getMessage() ?: ErrorCode::ResourceNotFound->defaultMessage(),
            );
        });

        // Handle unexpected exceptions
        $exceptions->render(function (Throwable $e, Request $request): ?JsonResponse {
            if (! $request->expectsJson()) {
                return null;
            }

            return ApiResponse::error(ErrorCode::InternalServerError);
        });

    })->create();
