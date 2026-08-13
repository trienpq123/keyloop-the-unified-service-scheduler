<?php

declare(strict_types=1);

namespace App\Appointment\Queries;

use App\Appointment\Enums\AppointmentStatus;
use App\Appointment\ValueObjects\TimeRange;
use App\Models\ServiceBay;
use Illuminate\Database\Eloquent\Builder;

final class FindAvailableServiceBay
{
    /**
     * Return the first available service bay, or null if none exists.
     */
    public function findFirst(
        int $dealershipId,
        TimeRange $period,
        bool $lockForUpdate = false,
    ): ?ServiceBay {
        $query = $this->baseQuery($dealershipId, $period);

        if ($lockForUpdate) {
            $query->lockForUpdate();
        }

        /** @var ServiceBay|null */
        return $query->first();
    }

    /**
     * Count all available service bays for the given period.
     */
    public function count(int $dealershipId, TimeRange $period): int
    {
        return $this->baseQuery($dealershipId, $period)->count();
    }

    /**
     * Build the shared base query for both findFirst() and count().
     */
    private function baseQuery(int $dealershipId, TimeRange $period): Builder
    {
        return ServiceBay::query()
            ->where('dealership_id', $dealershipId)
            ->where('is_active', true)
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
