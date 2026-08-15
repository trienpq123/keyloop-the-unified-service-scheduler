<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Appointment\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\BusinessHour;
use App\Models\Customer;
use App\Models\Dealership;
use App\Models\ServiceBay;
use App\Models\ServiceType;
use App\Models\Technician;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

final class AvailabilityConstraintsFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_qualified_technicians_busy_are_excluded(): void
    {
        $fixture = $this->fixture();
        ServiceBay::query()->create(['dealership_id' => $fixture['dealership']->id, 'name' => 'Free Bay', 'is_active' => true]);
        $this->occupy($fixture['technician'], $fixture['serviceBay'], $fixture);

        $this->availability($fixture)
            ->assertOk()
            ->assertJsonPath('data.available', false)
            ->assertJsonPath('data.available_technicians', 0)
            ->assertJsonPath('data.available_service_bays', 1);
    }

    public function test_all_bays_busy_are_excluded(): void
    {
        $fixture = $this->fixture();
        $otherTechnician = Technician::query()->create(['dealership_id' => $fixture['dealership']->id, 'name' => 'Busy Tech', 'is_active' => true]);
        $otherTechnician->serviceTypes()->attach($fixture['serviceType']->id);
        $this->occupy($otherTechnician, $fixture['serviceBay'], $fixture);

        $this->availability($fixture)
            ->assertOk()
            ->assertJsonPath('data.available', false)
            ->assertJsonPath('data.available_technicians', 1)
            ->assertJsonPath('data.available_service_bays', 0);
    }

    public function test_technician_without_the_requested_skill_is_excluded(): void
    {
        $fixture = $this->fixture(attachSkill: false);

        $this->availability($fixture)
            ->assertOk()
            ->assertJsonPath('data.available', false)
            ->assertJsonPath('data.available_technicians', 0)
            ->assertJsonPath('data.available_service_bays', 1);
    }

    public function test_resources_from_another_dealership_are_excluded(): void
    {
        $fixture = $this->fixture(createResources: false);
        $otherDealership = Dealership::query()->create(['name' => 'Other Dealer', 'timezone' => 'Asia/Ho_Chi_Minh', 'is_active' => true]);
        $otherTechnician = Technician::query()->create(['dealership_id' => $otherDealership->id, 'name' => 'Other Technician', 'is_active' => true]);
        $otherTechnician->serviceTypes()->attach($fixture['serviceType']->id);
        ServiceBay::query()->create(['dealership_id' => $otherDealership->id, 'name' => 'Other Bay', 'is_active' => true]);

        $this->availability($fixture)
            ->assertOk()
            ->assertJsonPath('data.available', false)
            ->assertJsonPath('data.available_technicians', 0)
            ->assertJsonPath('data.available_service_bays', 0);
    }

    /** @return array{dealership: Dealership, serviceType: ServiceType, technician: Technician|null, serviceBay: ServiceBay|null} */
    private function fixture(bool $attachSkill = true, bool $createResources = true): array
    {
        $dealership = Dealership::query()->create(['name' => 'Constraint Dealer', 'timezone' => 'Asia/Ho_Chi_Minh', 'is_active' => true]);
        BusinessHour::query()->create(['dealership_id' => $dealership->id, 'weekday' => 1, 'opens_at' => '08:00:00', 'closes_at' => '17:00:00']);
        $serviceType = ServiceType::query()->create(['name' => 'Constraint Service', 'duration_minutes' => 30, 'is_active' => true]);

        if (! $createResources) {
            return compact('dealership', 'serviceType') + ['technician' => null, 'serviceBay' => null];
        }

        $technician = Technician::query()->create(['dealership_id' => $dealership->id, 'name' => 'Constraint Technician', 'is_active' => true]);
        if ($attachSkill) {
            $technician->serviceTypes()->attach($serviceType->id);
        }
        $serviceBay = ServiceBay::query()->create(['dealership_id' => $dealership->id, 'name' => 'Constraint Bay', 'is_active' => true]);

        return compact('dealership', 'serviceType', 'technician', 'serviceBay');
    }

    /** @param array{dealership: Dealership, serviceType: ServiceType, technician: Technician|null, serviceBay: ServiceBay|null} $fixture */
    private function availability(array $fixture): TestResponse
    {
        return $this->getJson("/api/v1/dealerships/{$fixture['dealership']->id}/availability?service_type_id={$fixture['serviceType']->id}&start_at=2026-08-17T09:00:00%2B07:00");
    }

    /** @param array{dealership: Dealership, serviceType: ServiceType, technician: Technician|null, serviceBay: ServiceBay|null} $fixture */
    private function occupy(Technician $technician, ServiceBay $serviceBay, array $fixture): void
    {
        $customer = Customer::query()->create(['name' => 'Busy Customer', 'normalized_email' => 'busy@example.com']);
        $vehicle = Vehicle::query()->create(['customer_id' => $customer->id, 'registration_number' => 'BUSY-2026', 'normalized_registration_number' => 'BUSY2026']);
        Appointment::query()->create([
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'dealership_id' => $fixture['dealership']->id,
            'service_type_id' => $fixture['serviceType']->id,
            'technician_id' => $technician->id,
            'service_bay_id' => $serviceBay->id,
            'status' => AppointmentStatus::Confirmed,
            'start_at' => '2026-08-17T02:00:00+00:00',
            'end_at' => '2026-08-17T02:30:00+00:00',
        ]);
    }
}
