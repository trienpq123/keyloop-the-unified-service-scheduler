<?php

declare(strict_types=1);

namespace App\Appointment\Actions;

use App\Appointment\Data\AvailabilityCandidate;
use App\Appointment\Data\AvailabilityResult;
use App\Appointment\Data\CheckAvailabilityData;
use App\Appointment\Queries\FindAvailableServiceBay;
use App\Appointment\Queries\FindAvailableTechnician;
use App\Appointment\ValueObjects\TimeRange;
use App\Models\Dealership;
use App\Models\ServiceType;
use App\Shared\Exceptions\ResourceNotFoundException;

/**
 * Advisory availability check action.
 *
 * Calculates the full appointment period and reports qualified technician and
 * service-bay candidates that are free within the requested dealership.
 *
 * This action is intentionally advisory:
 *   - It does NOT acquire row locks.
 *   - It does NOT reserve any resources.
 *   - The result can become stale by the time the client acts on it.
 *
 * Appointment creation always re-checks availability inside its own
 * transaction with row locks, so the advisory result is a convenience
 * only — not a guarantee.
 *
 * Responsibilities (SRP)
 * ----------------------
 * Orchestrate the availability check only. HTTP handling, transaction
 * control, and resource locking are outside this class's responsibility.
 *
 * DRY / DI
 * --------
 * Delegates all query logic to the injected Query Objects, which are the
 * single source of truth for availability criteria. No raw Eloquent queries
 * live here.
 */
final class CheckAvailability
{
    public function __construct(
        private readonly FindAvailableTechnician $findTechnician,
        private readonly FindAvailableServiceBay $findServiceBay,
        private readonly AssertBusinessHours $assertBusinessHours,
    ) {}

    /**
     * @throws ResourceNotFoundException when the service type does not exist
     */
    public function execute(CheckAvailabilityData $data): AvailabilityResult
    {
        $dealership = Dealership::query()
            ->where('id', $data->dealershipId)
            ->where('is_active', true)
            ->first();

        if ($dealership === null) {
            throw new ResourceNotFoundException('Dealership', $data->dealershipId);
        }

        $serviceType = ServiceType::query()
            ->where('id', $data->serviceTypeId)
            ->where('is_active', true)
            ->first();

        if ($serviceType === null) {
            throw new ResourceNotFoundException('ServiceType', $data->serviceTypeId);
        }

        $period = TimeRange::fromServiceType($data->requestedStartAt, $serviceType);
        $this->assertBusinessHours->execute($dealership, $period);

        $technicians = $this->findTechnician->all(
            $data->dealershipId,
            $data->serviceTypeId,
            $period,
        );
        $serviceBays = $this->findServiceBay->all(
            $data->dealershipId,
            $period,
        );

        return new AvailabilityResult(
            available: $technicians->isNotEmpty() && $serviceBays->isNotEmpty(),
            period: $period,
            availableTechnicians: $technicians->count(),
            availableServiceBays: $serviceBays->count(),
            technicians: $technicians->map(static fn ($technician): AvailabilityCandidate => new AvailabilityCandidate($technician->id, $technician->name))->all(),
            serviceBays: $serviceBays->map(static fn ($serviceBay): AvailabilityCandidate => new AvailabilityCandidate($serviceBay->id, $serviceBay->name))->all(),
        );
    }
}
