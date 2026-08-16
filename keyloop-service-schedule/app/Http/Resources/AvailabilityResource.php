<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class AvailabilityResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'available' => $this->resource->available,
            'start_at' => $this->resource->period->start->toIso8601String(),
            'end_at' => $this->resource->period->end->toIso8601String(),
            'available_technicians' => $this->resource->availableTechnicians,
            'available_service_bays' => $this->resource->availableServiceBays,
            'technicians' => $this->resource->technicians,
            'service_bays' => $this->resource->serviceBays,
        ];
    }
}
