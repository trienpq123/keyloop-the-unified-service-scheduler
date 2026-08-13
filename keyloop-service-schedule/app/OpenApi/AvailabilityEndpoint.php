<?php

declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

final class AvailabilityEndpoint
{
    #[OA\Get(
        path: '/api/v1/user/dealerships/{dealership}/availability',
        operationId: 'checkDealershipAvailability',
        summary: 'Check advisory appointment availability for a dealership',
        description: 'Calculates availability for the complete service duration. This endpoint does not reserve a technician or service bay.',
        tags: ['Availability'],
        parameters: [
            new OA\PathParameter(
                name: 'dealership',
                description: 'Dealership identifier.',
                schema: new OA\Schema(type: 'integer', minimum: 1),
            ),
            new OA\QueryParameter(
                name: 'service_type_id',
                description: 'Service type used to calculate the appointment duration.',
                required: true,
                schema: new OA\Schema(type: 'integer', minimum: 1),
            ),
            new OA\QueryParameter(
                name: 'start_at',
                description: 'Requested appointment start time in ISO 8601 format with timezone.',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'date-time'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Advisory availability result.',
                content: new OA\JsonContent(
                    type: 'object',
                    required: ['data'],
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            required: [
                                'available',
                                'start_at',
                                'end_at',
                                'available_technicians',
                                'available_service_bays',
                            ],
                            properties: [
                                new OA\Property(property: 'available', type: 'boolean'),
                                new OA\Property(property: 'start_at', type: 'string', format: 'date-time'),
                                new OA\Property(property: 'end_at', type: 'string', format: 'date-time'),
                                new OA\Property(property: 'available_technicians', type: 'integer', minimum: 0),
                                new OA\Property(property: 'available_service_bays', type: 'integer', minimum: 0),
                            ],
                        ),
                    ],
                ),
            ),
            new OA\Response(response: 404, description: 'The requested service type does not exist or is inactive.'),
            new OA\Response(response: 422, description: 'A required query parameter is missing or invalid.'),
            new OA\Response(response: 500, description: 'An unexpected server error occurred.'),
        ],
    )]
    public function show(): void {}
}
