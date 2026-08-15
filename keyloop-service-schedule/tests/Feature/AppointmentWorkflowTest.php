<?php

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
use Tests\TestCase;

final class AppointmentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_appointment_and_replays_the_same_idempotent_request(): void
    {
        $fixture = $this->bookingFixture();
        $payload = $this->payload($fixture);
        $key = '30d8cf55-9a07-4a76-a13c-ecf63a371cb2';

        $first = $this->postJson('/api/v1/appointments', $payload, ['Idempotency-Key' => $key]);
        $first->assertCreated()->assertJsonPath('success', true)->assertJsonPath('data.status', 'confirmed');
        $appointmentId = $first->json('data.id');

        $this->postJson('/api/v1/appointments', $payload, ['Idempotency-Key' => $key])
            ->assertCreated()->assertJsonPath('data.id', $appointmentId);

        $this->assertDatabaseCount('appointments', 1);
        $this->assertDatabaseHas('appointments', [
            'id' => $appointmentId,
            'dealership_id' => $fixture['dealership']->id,
            'technician_id' => $fixture['technician']->id,
            'service_bay_id' => $fixture['serviceBay']->id,
        ]);
    }

    public function test_it_rejects_the_same_idempotency_key_with_a_different_payload(): void
    {
        $fixture = $this->bookingFixture();
        $key = 'e83a3e74-6997-4328-9bd3-ac52bb5672d7';
        $this->postJson('/api/v1/appointments', $this->payload($fixture), ['Idempotency-Key' => $key])->assertCreated();

        $changed = $this->payload($fixture);
        $changed['requested_start_at'] = '2026-08-17T10:00:00+07:00';
        $this->postJson('/api/v1/appointments', $changed, ['Idempotency-Key' => $key])
            ->assertConflict()->assertJsonPath('error.code', 'IDEMPOTENCY_CONFLICT');
    }

    public function test_it_rolls_back_guest_records_when_the_booking_is_outside_business_hours(): void
    {
        $fixture = $this->bookingFixture();
        $payload = $this->payload($fixture);
        $payload['customer']['email'] = 'outside-hours@example.com';
        $payload['vehicle']['registration_number'] = 'OUT-2026';
        $payload['requested_start_at'] = '2026-08-16T09:00:00+07:00'; // Sunday

        $this->postJson('/api/v1/appointments', $payload, ['Idempotency-Key' => 'c113e04d-6939-439b-a588-39672531843c'])
            ->assertUnprocessable()->assertJsonPath('error.code', 'OUTSIDE_BUSINESS_HOURS');

        $this->assertDatabaseCount('customers', 0);
        $this->assertDatabaseCount('vehicles', 0);
        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_it_rejects_a_vehicle_owned_by_another_customer(): void
    {
        $fixture = $this->bookingFixture();
        $owner = Customer::query()->create(['name' => 'Vehicle Owner', 'normalized_email' => 'owner@example.com']);
        Vehicle::query()->create([
            'customer_id' => $owner->id,
            'registration_number' => 'OWN-2026',
            'normalized_registration_number' => 'OWN2026',
        ]);
        $payload = $this->payload($fixture);
        $payload['vehicle']['registration_number'] = 'OWN-2026';

        $this->postJson('/api/v1/appointments', $payload, ['Idempotency-Key' => '902c3ce0-4b0b-4852-932f-6132e1d14e85'])
            ->assertConflict()
            ->assertJsonPath('error.code', 'VEHICLE_OWNERSHIP_CONFLICT');

        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_it_rolls_back_new_customer_and_vehicle_when_allocation_fails(): void
    {
        $fixture = $this->bookingFixture();
        $fixture['technician']->update(['is_active' => false]);
        $payload = $this->payload($fixture);
        $payload['customer']['email'] = 'no-slot@example.com';
        $payload['vehicle']['registration_number'] = 'NO-SLOT-2026';

        $this->postJson('/api/v1/appointments', $payload, ['Idempotency-Key' => '4529428a-7df1-4f19-8253-3f99e9c388e2'])
            ->assertConflict()
            ->assertJsonPath('error.code', 'APPOINTMENT_SLOT_UNAVAILABLE');

        $this->assertDatabaseCount('customers', 0);
        $this->assertDatabaseCount('vehicles', 0);
        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_cancellation_releases_an_appointment_resource(): void
    {
        $fixture = $this->bookingFixture();
        $customer = Customer::query()->create(['name' => 'Existing Customer', 'normalized_email' => 'existing@example.com']);
        $vehicle = Vehicle::query()->create(['customer_id' => $customer->id, 'registration_number' => 'EXIST-2026', 'normalized_registration_number' => 'EXIST2026']);
        $appointment = Appointment::query()->create([
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'dealership_id' => $fixture['dealership']->id,
            'service_type_id' => $fixture['serviceType']->id,
            'technician_id' => $fixture['technician']->id,
            'service_bay_id' => $fixture['serviceBay']->id,
            'status' => AppointmentStatus::Confirmed,
            'start_at' => '2026-08-17T02:00:00+00:00',
            'end_at' => '2026-08-17T02:30:00+00:00',
        ]);

        $this->patchJson("/api/v1/appointments/{$appointment->id}/cancel", ['reason' => 'Customer request'])
            ->assertOk()->assertJsonPath('data.status', 'cancelled');

        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'status' => 'cancelled']);
        $this->getJson("/api/v1/dealerships/{$fixture['dealership']->id}/availability?service_type_id={$fixture['serviceType']->id}&start_at=2026-08-17T09:00:00%2B07:00")
            ->assertOk()->assertJsonPath('data.available', true);
    }

    /** @return array{dealership: Dealership, serviceType: ServiceType, technician: Technician, serviceBay: ServiceBay} */
    private function bookingFixture(): array
    {
        $dealership = Dealership::query()->create(['name' => 'Test Dealer', 'timezone' => 'Asia/Ho_Chi_Minh', 'is_active' => true]);
        BusinessHour::query()->create(['dealership_id' => $dealership->id, 'weekday' => 1, 'opens_at' => '08:00:00', 'closes_at' => '17:00:00']);
        $serviceType = ServiceType::query()->create(['name' => 'Test Service', 'duration_minutes' => 30, 'is_active' => true]);
        $technician = Technician::query()->create(['dealership_id' => $dealership->id, 'name' => 'Test Technician', 'is_active' => true]);
        $technician->serviceTypes()->attach($serviceType->id);
        $serviceBay = ServiceBay::query()->create(['dealership_id' => $dealership->id, 'name' => 'Test Bay', 'is_active' => true]);

        return compact('dealership', 'serviceType', 'technician', 'serviceBay');
    }

    /** @param array{dealership: Dealership, serviceType: ServiceType, technician: Technician, serviceBay: ServiceBay} $fixture */
    private function payload(array $fixture): array
    {
        return [
            'customer' => ['name' => 'Test Customer', 'email' => 'test@example.com'],
            'vehicle' => ['registration_number' => 'TEST-2026'],
            'dealership_id' => $fixture['dealership']->id,
            'service_type_id' => $fixture['serviceType']->id,
            'requested_start_at' => '2026-08-17T09:00:00+07:00',
        ];
    }
}
