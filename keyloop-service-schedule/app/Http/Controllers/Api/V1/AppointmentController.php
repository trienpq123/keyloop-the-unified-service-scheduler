<?php

namespace App\Http\Controllers\Api\V1;

use App\Appointment\Actions\CancelAppointment;
use App\Appointment\Actions\CreateAppointment;
use App\Http\Controllers\Controller;
use App\Http\Requests\CancelAppointmentRequest;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Http\Responses\ApiResponse;
use App\Models\Appointment;
use Illuminate\Http\JsonResponse;

final class AppointmentController extends Controller
{
    public function __construct(
        private readonly CreateAppointment $createAppointment,
        private readonly CancelAppointment $cancelAppointment,
    ) {}

    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $result = $this->createAppointment->execute($request->toData());

        return ApiResponse::created((new AppointmentResource($result->appointment))->resolve());
    }

    public function show(Appointment $appointment): JsonResponse
    {
        return ApiResponse::success((new AppointmentResource($appointment))->resolve());
    }

    public function cancel(CancelAppointmentRequest $request, int $appointment): JsonResponse
    {
        $cancelledAppointment = $this->cancelAppointment->execute(
            $appointment,
            $request->string('reason')->toString() ?: null,
        );

        return ApiResponse::success((new AppointmentResource($cancelledAppointment))->resolve());
    }
}
