<?php

namespace App\Customer\Data;

final readonly class GuestCustomerData
{
    public function __construct(
        public string $name,
        public ?string $email,
        public ?string $phone,
    ) {}
}
