<?php

namespace App\Appointment\Actions;

use App\Appointment\Enums\AppointmentStatus;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;

final class CancelAppointment
{
    public function execute(int $appointmentId, ?string $reason): Appointment
    {
        return DB::transaction(function () use ($appointmentId, $reason): Appointment {
            $appointment = Appointment::query()->lockForUpdate()->findOrFail($appointmentId);

            if ($appointment->status === AppointmentStatus::Cancelled) {
                return $appointment;
            }

            $appointment->update([
                'status' => AppointmentStatus::Cancelled,
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
            ]);

            return $appointment->fresh();
        });
    }
}
