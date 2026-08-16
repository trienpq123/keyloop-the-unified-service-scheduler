<?php

declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

final class ApiEnvelopeSchemas
{
    #[OA\Schema(
        schema: 'ApiErrorEnvelope',
        required: ['success', 'error', 'meta'],
        properties: [
            new OA\Property(property: 'success', type: 'boolean', example: false),
            new OA\Property(
                property: 'error',
                required: ['code', 'message', 'details'],
                properties: [
                    new OA\Property(property: 'code', type: 'string', example: 'IDEMPOTENCY_CONFLICT'),
                    new OA\Property(property: 'message', type: 'string'),
                    new OA\Property(property: 'details', type: 'array', items: new OA\Items(type: 'object')),
                ],
                type: 'object',
            ),
            new OA\Property(
                property: 'meta',
                required: ['request_id'],
                properties: [new OA\Property(property: 'request_id', type: 'string', format: 'uuid')],
                type: 'object',
            ),
        ],
    )]
    public function errorEnvelope(): void {}
}
