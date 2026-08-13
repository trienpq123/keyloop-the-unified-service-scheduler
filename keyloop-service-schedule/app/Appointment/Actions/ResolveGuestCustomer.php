<?php

namespace App\Appointment\Actions;

use App\Customer\Data\GuestCustomerData;
use App\Customer\Exceptions\CustomerIdentityConflict;
use App\Models\Customer;
use App\Shared\Support\Normalizer;

final class ResolveGuestCustomer
{
    public function execute(GuestCustomerData $data): Customer
    {
        $email = Normalizer::email($data->email);
        $phone = Normalizer::phone($data->phone);
        $emailMatch = $email === null ? null : Customer::query()->where('normalized_email', $email)->lockForUpdate()->first();
        $phoneMatch = $phone === null ? null : Customer::query()->where('normalized_phone', $phone)->lockForUpdate()->first();

        if ($emailMatch !== null && $phoneMatch !== null && ! $emailMatch->is($phoneMatch)) {
            throw new CustomerIdentityConflict;
        }

        $customer = $emailMatch ?? $phoneMatch;
        if ($customer === null) {
            return Customer::query()->create([
                'name' => $data->name,
                'email' => $data->email,
                'phone' => $data->phone,
                'normalized_email' => $email,
                'normalized_phone' => $phone,
            ]);
        }

        if (($email !== null && $customer->normalized_email !== null && $customer->normalized_email !== $email)
            || ($phone !== null && $customer->normalized_phone !== null && $customer->normalized_phone !== $phone)) {
            throw new CustomerIdentityConflict;
        }

        $customer->fill([
            'normalized_email' => $customer->normalized_email ?? $email,
            'email' => $customer->email ?? $data->email,
            'normalized_phone' => $customer->normalized_phone ?? $phone,
            'phone' => $customer->phone ?? $data->phone,
        ])->save();

        return $customer;
    }
}
