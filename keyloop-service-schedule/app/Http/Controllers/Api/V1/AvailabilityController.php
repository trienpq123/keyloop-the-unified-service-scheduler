<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Appointment\Actions\CheckAvailability;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckAvailabilityRequest;
use App\Http\Resources\AvailabilityResource;
use Illuminate\Http\JsonResponse;

final class AvailabilityController extends Controller
{
    public function __construct(
        private readonly CheckAvailability $checkAvailability,
    ) {}

    /**
     * GET /api/v1/user/dealerships/{dealership}/availability
     *
     * Advisory check — does NOT reserve resources.
     */
    public function show(
        CheckAvailabilityRequest $request,
        int $dealership,
    ): JsonResponse {
        $result = $this->checkAvailability->execute(
            $request->toData($dealership),
        );

        return AvailabilityResource::make($result)
            ->response()
            ->setStatusCode(200);
    }
}
