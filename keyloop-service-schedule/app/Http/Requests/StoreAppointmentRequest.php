<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Appointment\Data\CreateAppointmentData;
use App\Customer\Data\GuestCustomerData;
use App\Vehicle\Data\VehicleData;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

final class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            // Customer
            'customer' => ['required', 'array'],
            'customer.name' => ['required', 'string', 'max:150'],
            'customer.email' => ['nullable', 'string', 'email:rfc', 'max:255'],
            'customer.phone' => ['nullable', 'string', 'max:30'],

            // Vehicle
            'vehicle' => ['required', 'array'],
            'vehicle.registration_number' => ['required', 'string', 'max:30'],
            'vehicle.make' => ['nullable', 'string', 'max:100'],
            'vehicle.model' => ['nullable', 'string', 'max:100'],
            'vehicle.manufactured_year' => ['nullable', 'integer', 'digits:4', 'min:1900'],

            // Booking details
            'dealership_id' => ['required', 'integer', 'min:1'],
            'service_type_id' => ['required', 'integer', 'min:1'],
            'requested_start_at' => ['required', 'string', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'customer.required' => 'Customer information is required.',
            'customer.name.required' => 'Customer name is required.',
            'customer.name.max' => 'Customer name must not exceed 150 characters.',
            'customer.email.email' => 'Customer email must be a valid email address.',
            'customer.phone.max' => 'Customer phone must not exceed 30 characters.',
            'vehicle.required' => 'Vehicle information is required.',
            'vehicle.registration_number.required' => 'Vehicle registration number is required.',
            'dealership_id.required' => 'A dealership is required.',
            'dealership_id.integer' => 'The dealership must be a valid identifier.',
            'service_type_id.required' => 'A service type is required.',
            'service_type_id.integer' => 'The service type must be a valid identifier.',
            'requested_start_at.required' => 'A desired start time is required.',
            'requested_start_at.date' => 'The start time must be a valid date/time string.',
        ];
    }

    /**
     * Additional cross-field validation.
     *
     * Ensures at least one contact identifier is present, as required by
     * the guest-customer matching policy.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            $email = $this->input('customer.email');
            $phone = $this->input('customer.phone');

            if (empty($email) && empty($phone)) {
                $v->errors()->add(
                    'customer',
                    'At least one contact identifier (email or phone) must be provided.',
                );
            }
        });
    }

    /**
     * Validate and return the Idempotency-Key header value.
     *
     * @throws ValidationException when the header is absent or not a valid UUID.
     */
    public function idempotencyKey(): string
    {
        $key = (string) $this->header('Idempotency-Key', '');

        if ($key === '' || ! $this->isValidUuid($key)) {
            throw ValidationException::withMessages([
                'Idempotency-Key' => 'A valid UUID Idempotency-Key header is required.',
            ]);
        }

        return $key;
    }

    /**
     * Convert validated input into an immutable, typed DTO.
     */
    public function toData(): CreateAppointmentData
    {
        $key = $this->idempotencyKey();

        return new CreateAppointmentData(
            customer: new GuestCustomerData(
                name: $this->string('customer.name')->toString(),
                email: $this->input('customer.email'),
                phone: $this->input('customer.phone'),
            ),
            vehicle: new VehicleData(
                registrationNumber: $this->string('vehicle.registration_number')->toString(),
                make: $this->input('vehicle.make'),
                model: $this->input('vehicle.model'),
                manufacturedYear: $this->input('vehicle.manufactured_year') !== null
                                        ? (int) $this->input('vehicle.manufactured_year')
                                        : null,
            ),
            dealershipId: (int) $this->integer('dealership_id'),
            serviceTypeId: (int) $this->integer('service_type_id'),
            requestedStartAt: CarbonImmutable::parse($this->string('requested_start_at'))->utc(),
            idempotencyKey: $key,
            requestHash: $this->buildRequestHash(),
        );
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Builds a deterministic SHA-256 hash of the canonical request payload.
     *
     * The hash is used to detect when the same idempotency key is reused
     * with a different payload (IDEMPOTENCY_CONFLICT).
     *
     * Only the business payload is hashed; request metadata such as headers
     * and the idempotency key itself are excluded.
     */
    private function buildRequestHash(): string
    {
        $canonical = [
            'customer' => $this->input('customer'),
            'vehicle' => $this->input('vehicle'),
            'dealership_id' => $this->integer('dealership_id'),
            'service_type_id' => $this->integer('service_type_id'),
            'requested_start_at' => $this->string('requested_start_at')->toString(),
        ];

        ksort($canonical);

        return hash('sha256', json_encode($canonical, JSON_THROW_ON_ERROR));
    }

    /**
     * Returns true when the value matches a UUID format (v4 or v7).
     */
    private function isValidUuid(string $value): bool
    {
        return (bool) preg_match(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            $value,
        );
    }
}
