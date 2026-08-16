<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class DealershipResource extends JsonResource
{
    /** @return array{id: int, name: string, timezone: string, service_types: list<array{id: int, name: string, duration_minutes: int}>} */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'timezone' => $this->resource->timezone,
            'service_types' => $this->resource->technicians
                ->flatMap(static fn ($technician) => $technician->serviceTypes)
                ->unique('id')
                ->sortBy('name')
                ->values()
                ->map(static fn ($serviceType): array => [
                    'id' => $serviceType->id,
                    'name' => $serviceType->name,
                    'duration_minutes' => $serviceType->duration_minutes,
                ])
                ->all(),
        ];
    }
}
