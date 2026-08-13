<?php

namespace App\Appointment\Actions;

use App\Models\Customer;
use App\Models\Vehicle;
use App\Shared\Support\Normalizer;
use App\Vehicle\Data\VehicleData;
use App\Vehicle\Exceptions\VehicleOwnershipConflict;

final class ResolveCustomerVehicle
{
    public function execute(Customer $customer, VehicleData $data): Vehicle
    {
        $registration = Normalizer::registration($data->registrationNumber);
        $vehicle = Vehicle::query()->where('normalized_registration_number', $registration)->lockForUpdate()->first();

        if ($vehicle !== null && $vehicle->customer_id !== $customer->id) {
            throw new VehicleOwnershipConflict;
        }

        return $vehicle ?? Vehicle::query()->create([
            'customer_id' => $customer->id,
            'registration_number' => $data->registrationNumber,
            'normalized_registration_number' => $registration,
            'make' => $data->make,
            'model' => $data->model,
            'manufactured_year' => $data->manufacturedYear,
        ]);
    }
}
