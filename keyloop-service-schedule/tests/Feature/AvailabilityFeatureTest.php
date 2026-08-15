<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\BusinessHour;
use App\Models\Dealership;
use App\Models\ServiceBay;
use App\Models\ServiceType;
use App\Models\Technician;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AvailabilityFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_advisory_availability_without_creating_an_appointment(): void
    {
        $dealership = Dealership::query()->create(['name' => 'Availability Dealer', 'timezone' => 'Asia/Ho_Chi_Minh', 'is_active' => true]);
        BusinessHour::query()->create(['dealership_id' => $dealership->id, 'weekday' => 1, 'opens_at' => '08:00:00', 'closes_at' => '17:00:00']);
        $serviceType = ServiceType::query()->create(['name' => 'Availability Service', 'duration_minutes' => 30, 'is_active' => true]);
        $technician = Technician::query()->create(['dealership_id' => $dealership->id, 'name' => 'Available Technician', 'is_active' => true]);
        $technician->serviceTypes()->attach($serviceType->id);
        ServiceBay::query()->create(['dealership_id' => $dealership->id, 'name' => 'Available Bay', 'is_active' => true]);

        $this->getJson("/api/v1/dealerships/{$dealership->id}/availability?service_type_id={$serviceType->id}&start_at=2026-08-17T09:00:00%2B07:00")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.available', true)
            ->assertJsonPath('data.available_technicians', 1)
            ->assertJsonPath('data.available_service_bays', 1);

        $this->assertDatabaseCount('appointments', 0);
    }
}
