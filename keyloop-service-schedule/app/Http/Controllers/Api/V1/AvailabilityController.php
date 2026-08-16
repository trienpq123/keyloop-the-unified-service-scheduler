<?php

namespace App\Http\Controllers\Api\V1;

use App\Appointment\Actions\CheckAvailability;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckAvailabilityRequest;
use App\Http\Resources\AvailabilityResource;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

final class AvailabilityController extends Controller
{
    public function __construct(
        private readonly CheckAvailability $checkAvailability,
    ) {}

    /**
     * GET /api/v1/dealerships/{dealership}/availability
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

        return ApiResponse::success(AvailabilityResource::make($result)->resolve());
    }
}
