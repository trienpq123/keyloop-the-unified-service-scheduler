<?php

namespace App\Appointment\Actions;

use App\Appointment\Data\CreateAppointmentData;
use App\Appointment\Data\CreateAppointmentResult;
use App\Appointment\Enums\AppointmentStatus;
use App\Appointment\Enums\IdempotencyStatus;
use App\Appointment\Exceptions\AppointmentSlotUnavailable;
use App\Appointment\Exceptions\IdempotencyConflict;
use App\Appointment\Exceptions\IdempotencyRequestInProgress;
use App\Appointment\Queries\FindAvailableServiceBay;
use App\Appointment\Queries\FindAvailableTechnician;
use App\Appointment\ValueObjects\TimeRange;
use App\Models\Appointment;
use App\Models\Dealership;
use App\Models\IdempotencyKey;
use App\Models\ServiceBay;
use App\Models\ServiceType;
use App\Models\Technician;
use App\Shared\Exceptions\DomainException;
use App\Shared\Exceptions\ResourceNotFoundException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class CreateAppointment
{
    private const IDEMPOTENCY_SCOPE = 'appointments.create';

    public function __construct(
        private readonly ResolveGuestCustomer $resolveGuestCustomer,
        private readonly ResolveCustomerVehicle $resolveCustomerVehicle,
        private readonly AssertBusinessHours $assertBusinessHours,
        private readonly FindAvailableTechnician $findTechnician,
        private readonly FindAvailableServiceBay $findServiceBay,
    ) {}

    public function execute(CreateAppointmentData $data): CreateAppointmentResult
    {
        $startedAt = microtime(true);

        try {
            $result = $this->createWithIdempotencyRetry($data);

            return $result;
        } catch (\Throwable $exception) {
            $this->logRejected($data, $startedAt, $exception);

            throw $exception;
        }
    }

    private function createWithIdempotencyRetry(CreateAppointmentData $data): CreateAppointmentResult
    {
        try {
            return DB::transaction(
                fn (): CreateAppointmentResult => $this->createInTransaction($data),
                attempts: 3,
            );
        } catch (UniqueConstraintViolationException $exception) {
            throw $exception;
        }
    }

    private function createInTransaction(CreateAppointmentData $data): CreateAppointmentResult
    {
        $existingResult = $this->replayExistingRequest($data);
        if ($existingResult !== null) {
            return $existingResult;
        }

        $idempotencyKey = $this->claimIdempotencyKey($data);
        [$dealership, $serviceType, $period] = $this->loadBookingContext($data);
        $this->assertBusinessHours->execute($dealership, $period);

        $customer = $this->resolveGuestCustomer->execute($data->customer);
        $vehicle = $this->resolveCustomerVehicle->execute($customer, $data->vehicle);
        [$technician, $serviceBay] = $this->allocateResources($dealership, $serviceType, $period);

        $appointment = $this->persistAppointment(
            $customer->id,
            $vehicle->id,
            $dealership->id,
            $serviceType->id,
            $technician->id,
            $serviceBay->id,
            $period,
        );
        $this->completeIdempotencyKey($idempotencyKey, $appointment);

        return new CreateAppointmentResult($appointment);
    }

    private function replayExistingRequest(CreateAppointmentData $data): ?CreateAppointmentResult
    {
        $key = IdempotencyKey::query()
            ->where('scope', self::IDEMPOTENCY_SCOPE)
            ->where('idempotency_key', $data->idempotencyKey)
            ->lockForUpdate()
            ->first();

        if ($key === null) {
            return null;
        }
        if ($key->expires_at->isPast()) {
            $key->delete();

            return null;
        }
        if ($key->request_hash !== $data->requestHash) {
            throw new IdempotencyConflict;
        }
        if ($key->status !== IdempotencyStatus::Completed || $key->response_body === null) {
            throw new IdempotencyRequestInProgress;
        }

        return new CreateAppointmentResult(
            Appointment::query()->findOrFail($key->response_body['id']),
            replayed: true,
        );
    }

    private function claimIdempotencyKey(CreateAppointmentData $data): IdempotencyKey
    {
        return IdempotencyKey::query()->create([
            'scope' => self::IDEMPOTENCY_SCOPE,
            'idempotency_key' => $data->idempotencyKey,
            'request_hash' => $data->requestHash,
            'status' => IdempotencyStatus::Processing->value,
            'locked_at' => now(),
            'expires_at' => now()->addDay(),
        ]);
    }

    private function loadBookingContext(CreateAppointmentData $data): array
    {
        $dealership = Dealership::query()->where('is_active', true)->find($data->dealershipId);
        if ($dealership === null) {
            throw new ResourceNotFoundException('Dealership', $data->dealershipId);
        }
        $serviceType = ServiceType::query()->where('is_active', true)->find($data->serviceTypeId);
        if ($serviceType === null) {
            throw new ResourceNotFoundException('ServiceType', $data->serviceTypeId);
        }

        return [$dealership, $serviceType, TimeRange::fromServiceType($data->requestedStartAt, $serviceType)];
    }

    private function allocateResources(Dealership $dealership, ServiceType $serviceType, TimeRange $period): array
    {
        $technician = $this->findTechnician->execute($dealership->id, $serviceType->id, $period, true);
        if ($technician === null) {
            throw new AppointmentSlotUnavailable;
        }
        $serviceBay = $this->findServiceBay->findFirst($dealership->id, $period, true);
        if ($serviceBay === null || $this->hasResourceOverlap($technician, $serviceBay, $period)) {
            throw new AppointmentSlotUnavailable;
        }

        return [$technician, $serviceBay];
    }

    private function hasResourceOverlap(Technician $technician, ServiceBay $serviceBay, TimeRange $period): bool
    {
        return Appointment::query()
            ->whereIn('status', AppointmentStatus::resourceReservingValues())
            ->where('start_at', '<', $period->end)
            ->where('end_at', '>', $period->start)
            ->where(static function ($query) use ($technician, $serviceBay): void {
                $query->where('technician_id', $technician->id)
                    ->orWhere('service_bay_id', $serviceBay->id);
            })
            ->exists();
    }

    private function persistAppointment(
        int $customerId,
        int $vehicleId,
        int $dealershipId,
        int $serviceTypeId,
        int $technicianId,
        int $serviceBayId,
        TimeRange $period,
    ): Appointment {
        return Appointment::query()->create([
            'customer_id' => $customerId,
            'vehicle_id' => $vehicleId,
            'dealership_id' => $dealershipId,
            'service_type_id' => $serviceTypeId,
            'technician_id' => $technicianId,
            'service_bay_id' => $serviceBayId,
            'status' => AppointmentStatus::Confirmed->value,
            'start_at' => $period->start,
            'end_at' => $period->end,
        ]);
    }

    private function completeIdempotencyKey(IdempotencyKey $key, Appointment $appointment): void
    {
        $key->update([
            'status' => IdempotencyStatus::Completed->value,
            'response_status_code' => 201,
            'response_body' => ['id' => $appointment->id],
            'completed_at' => now(),
        ]);
    }

    private function logRejected(CreateAppointmentData $data, float $startedAt, \Throwable $exception): void
    {
        Log::channel('app')->warning('appointment.rejected', [
            'dealership_id' => $data->dealershipId,
            'service_type_id' => $data->serviceTypeId,
            'duration_ms' => (int) ((microtime(true) - $startedAt) * 1000),
            'reason' => $exception instanceof DomainException ? $exception->errorCode()->value : 'INTERNAL_ERROR',
        ]);
    }
}
