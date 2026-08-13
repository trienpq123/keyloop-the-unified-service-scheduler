<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Appointment\Data\CheckAvailabilityData;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates the advisory availability-check query parameters.
 *
 * Responsibilities (SRP)
 * ----------------------
 * Validate structure and types only. Does not perform availability queries
 * or access the database beyond implicit model binding resolution.
 */
final class CheckAvailabilityRequest extends FormRequest
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
            'service_type_id' => ['required', 'integer', 'min:1'],
            'start_at' => ['required', 'string', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'service_type_id.required' => 'A service type is required.',
            'service_type_id.integer' => 'The service type must be a valid identifier.',
            'service_type_id.min' => 'The service type identifier must be a positive integer.',
            'start_at.required' => 'A desired start time is required.',
            'start_at.date' => 'The start time must be a valid date/time string.',
        ];
    }

    /**
     * Convert validated input into an immutable, typed DTO.
     */
    public function toData(int $dealershipId): CheckAvailabilityData
    {
        return new CheckAvailabilityData(
            dealershipId: $dealershipId,
            serviceTypeId: (int) $this->integer('service_type_id'),
            requestedStartAt: CarbonImmutable::parse($this->string('start_at')->toString())->utc(),
        );
    }
}
