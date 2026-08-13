<?php

namespace App\Appointment\Data;

use App\Models\Appointment;

final readonly class CreateAppointmentResult
{
    public function __construct(public Appointment $appointment, public bool $replayed = false) {}
}
