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
            ->assertJsonPath('data.available_service_bays', 1)
            ->assertJsonPath('data.technicians.0.id', $technician->id)
            ->assertJsonPath('data.technicians.0.name', 'Available Technician')
            ->assertJsonPath('data.service_bays.0.name', 'Available Bay');

        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_it_rejects_an_inactive_or_unknown_dealership_and_outside_business_hours(): void
    {
        $dealership = Dealership::query()->create(['name' => 'Closed Dealer', 'timezone' => 'Asia/Ho_Chi_Minh', 'is_active' => true]);
        BusinessHour::query()->create(['dealership_id' => $dealership->id, 'weekday' => 1, 'opens_at' => '08:00:00', 'closes_at' => '17:00:00']);
        $serviceType = ServiceType::query()->create(['name' => 'Closed Service', 'duration_minutes' => 30, 'is_active' => true]);

        $this->getJson("/api/v1/dealerships/{$dealership->id}/availability?service_type_id={$serviceType->id}&start_at=2026-08-16T09:00:00%2B07:00")
            ->assertUnprocessable()
            ->assertJsonPath('error.code', 'OUTSIDE_BUSINESS_HOURS');

        $this->getJson("/api/v1/dealerships/999999/availability?service_type_id={$serviceType->id}&start_at=2026-08-17T09:00:00%2B07:00")
            ->assertNotFound()
            ->assertJsonPath('error.code', 'RESOURCE_NOT_FOUND');
    }

    public function test_api_errors_use_the_json_envelope_when_the_client_accepts_any_content_type(): void
    {
        $this->withHeaders(['Accept' => '*/*'])
            ->get('/api/v1/dealerships/1/availability')
            ->assertUnprocessable()
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonPath('success', false)
            ->assertJsonPath('error.code', 'VALIDATION_FAILED');
    }
}
