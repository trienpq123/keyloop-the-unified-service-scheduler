<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

final class AppointmentEndpoint
{
    #[OA\Post(
        path: '/api/v1/appointments',
        operationId: 'createAppointment',
        summary: 'Create a confirmed service appointment',
        tags: ['Appointments'],
        parameters: [new OA\HeaderParameter(name: 'Idempotency-Key', required: true, schema: new OA\Schema(type: 'string', format: 'uuid'))],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['customer', 'vehicle', 'dealership_id', 'service_type_id', 'requested_start_at'],
                properties: [
                    new OA\Property(property: 'customer', type: 'object'),
                    new OA\Property(property: 'vehicle', type: 'object'),
                    new OA\Property(property: 'dealership_id', type: 'integer'),
                    new OA\Property(property: 'service_type_id', type: 'integer'),
                    new OA\Property(property: 'requested_start_at', type: 'string', format: 'date-time'),
                ],
            ),
        ),
        responses: [
            new OA\Response(response: 201, description: 'Appointment confirmed.'),
            new OA\Response(response: 409, description: 'Resource, identity, ownership, or idempotency conflict.', content: new OA\JsonContent(ref: '#/components/schemas/ApiErrorEnvelope')),
            new OA\Response(response: 422, description: 'Invalid request or outside business hours.', content: new OA\JsonContent(ref: '#/components/schemas/ApiErrorEnvelope')),
            new OA\Response(response: 500, description: 'Unexpected server error.', content: new OA\JsonContent(ref: '#/components/schemas/ApiErrorEnvelope')),
        ],
    )]
    public function create(): void {}

    #[OA\Get(
        path: '/api/v1/appointments/{appointment}',
        operationId: 'getAppointment',
        summary: 'Get a confirmed appointment',
        tags: ['Appointments'],
        parameters: [new OA\PathParameter(name: 'appointment', schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Appointment found.'), new OA\Response(response: 404, description: 'Appointment not found.', content: new OA\JsonContent(ref: '#/components/schemas/ApiErrorEnvelope'))],
    )]
    public function show(): void {}

    #[OA\Patch(
        path: '/api/v1/appointments/{appointment}/cancel',
        operationId: 'cancelAppointment',
        summary: 'Cancel a confirmed appointment and release its resources',
        tags: ['Appointments'],
        parameters: [new OA\PathParameter(name: 'appointment', schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(content: new OA\JsonContent(properties: [new OA\Property(property: 'reason', type: 'string', maxLength: 500)])),
        responses: [new OA\Response(response: 200, description: 'Appointment cancelled.'), new OA\Response(response: 404, description: 'Appointment not found.', content: new OA\JsonContent(ref: '#/components/schemas/ApiErrorEnvelope'))],
    )]
    public function cancel(): void {}
}
