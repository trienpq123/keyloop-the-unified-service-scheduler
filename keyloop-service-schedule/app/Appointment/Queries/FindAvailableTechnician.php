<?php

namespace App\Appointment\Queries;

use App\Appointment\Enums\AppointmentStatus;
use App\Appointment\ValueObjects\TimeRange;
use App\Models\Technician;
use Illuminate\Database\Eloquent\Builder;

final class FindAvailableTechnician
{
    /**
     * Return the first available qualified technician, or null if none exists.
     */
    public function execute(
        int $dealershipId,
        int $serviceTypeId,
        TimeRange $period,
        bool $lockForUpdate = false,
    ): ?Technician {
        $query = $this->baseQuery($dealershipId, $serviceTypeId, $period);

        if ($lockForUpdate) {
            $query->lockForUpdate();
        }

        /** @var Technician|null */
        return $query->first();
    }

    public function count(int $dealershipId, int $serviceTypeId, TimeRange $period): int
    {
        return $this->baseQuery($dealershipId, $serviceTypeId, $period)->count();
    }

    private function baseQuery(int $dealershipId, int $serviceTypeId, TimeRange $period): Builder
    {
        return Technician::query()
            ->where('dealership_id', $dealershipId)
            ->where('is_active', true)
            ->whereHas(
                'serviceTypes',
                static fn (Builder $q): Builder => $q->where('service_types.id', $serviceTypeId),
            )
            ->whereDoesntHave(
                'appointments',
                static fn (Builder $q): Builder => $q
                    ->whereIn('status', AppointmentStatus::resourceReservingValues())
                    ->where('start_at', '<', $period->end)
                    ->where('end_at', '>', $period->start),
            )
            ->orderBy('id');
    }
}
