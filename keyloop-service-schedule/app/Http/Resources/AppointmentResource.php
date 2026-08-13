<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'customer_id' => $this->customer_id,
            'vehicle_id' => $this->vehicle_id,
            'dealership_id' => $this->dealership_id,
            'service_type_id' => $this->service_type_id,
            'technician_id' => $this->technician_id,
            'service_bay_id' => $this->service_bay_id,
            'start_at' => $this->start_at->toIso8601String(),
            'end_at' => $this->end_at->toIso8601String(),
        ];
    }
}
