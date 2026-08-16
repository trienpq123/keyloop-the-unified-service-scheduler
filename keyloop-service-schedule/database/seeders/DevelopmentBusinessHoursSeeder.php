<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\BusinessHour;
use App\Models\Dealership;
use Illuminate\Database\Seeder;

final class DevelopmentBusinessHoursSeeder extends Seeder
{
    public function run(): void
    {
        Dealership::query()
            ->where('name', 'Keyloop HCMC')
            ->each(function (Dealership $dealership): void {
                foreach (range(1, 5) as $weekday) {
                    BusinessHour::query()->firstOrCreate(
                        ['dealership_id' => $dealership->id, 'weekday' => $weekday],
                        ['opens_at' => '08:00:00', 'closes_at' => '17:00:00'],
                    );
                }
            });
    }
}
