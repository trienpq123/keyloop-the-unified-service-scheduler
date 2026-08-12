<?php

namespace App\Http\Responses;

use App\Shared\Exceptions\ErrorCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;

final class ApiResponse
{
    // -------------------------------------------------------------------------
    // Success responses
    // -------------------------------------------------------------------------
    public static function success(array|object $data, int $status = 200): JsonResponse
    {
        return response()->json(
            self::successBody($data),
            $status,
        );
    }

    public static function created(array|object $data): JsonResponse
    {
        return self::success($data, 201);
    }

    // -------------------------------------------------------------------------
    // Error responses
    // -------------------------------------------------------------------------

    public static function error(
        ErrorCode $code,
        string $message = '',
        array $details = [],
        ?int $status = null,
    ): JsonResponse {
        return response()->json(
            self::errorBody($code, $message, $details),
            $status ?? $code->httpStatus(),
        );
    }

    public static function validationError(array $details = [], string $message = ''): JsonResponse
    {
        return self::error(ErrorCode::ValidationFailed, $message, $details);
    }

    // -------------------------------------------------------------------------
    // Raw envelope builders (used by exception renderer to build the body only)
    // -------------------------------------------------------------------------

    public static function successBody(array|object $data): array
    {
        return [
            'success' => true,
            'data' => $data,
            'meta' => self::meta(),
        ];
    }

    public static function errorBody(
        ErrorCode $code,
        string $message = '',
        array $details = [],
    ): array {
        return [
            'success' => false,
            'error' => [
                'code' => $code->value,
                'message' => $message !== '' ? $message : $code->defaultMessage(),
                'details' => $details,
            ],
            'meta' => self::meta(),
        ];
    }

    // -------------------------------------------------------------------------
    // Internal helpers
    // -------------------------------------------------------------------------

    private static function meta(): array
    {
        return [
            'request_id' => (string) Request::header('X-Request-ID', ''),
        ];
    }
}
