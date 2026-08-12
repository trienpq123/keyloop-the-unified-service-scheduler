<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AvailabilityController extends Controller
{
    /**
     * GET /api/v1/dealerships/{dealership}/availability
     */
    public function show(Request $request, int $dealership): JsonResponse
    {
        // TODO: replace stub with real implementation
        return ApiResponse::success(['message' => 'Availability endpoint coming soon.'], 501);
    }
}
