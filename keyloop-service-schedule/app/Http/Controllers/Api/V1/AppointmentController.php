<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AppointmentController extends Controller
{
    /**
     * POST /api/v1/appointments
     *
     * Resolve guest customer and vehicle, allocate a qualified technician and
     * service bay, and persist the confirmed appointment atomically.
     *
     * Full implementation wired in when CreateAppointment action and
     * StoreAppointmentRequest are built (TASK-018).
     */
    public function store(Request $request): JsonResponse
    {
        // TODO: replace stub with real implementation
        return ApiResponse::success(['message' => 'Appointment endpoint coming soon.'], 501);
    }
}
