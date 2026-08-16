<?php

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
