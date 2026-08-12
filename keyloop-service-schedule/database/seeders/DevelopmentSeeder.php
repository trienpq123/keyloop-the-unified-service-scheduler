<?php

namespace Database\Seeders;

use App\Models\Dealership;
use App\Models\ServiceBay;
use App\Models\ServiceType;
use App\Models\Technician;
use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    public function run(): void
    {
        $dealership = Dealership::create([
            'name' => 'Keyloop HCMC',
            'timezone' => 'Asia/Ho_Chi_Minh',
            'is_active' => true,
        ]);

        ServiceBay::insert([
            [
                'dealership_id' => $dealership->id,
                'name' => 'Bay 01',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'dealership_id' => $dealership->id,
                'name' => 'Bay 02',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'dealership_id' => $dealership->id,
                'name' => 'Bay 03',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $oilChange = ServiceType::create([
            'name' => 'Oil Change',
            'duration_minutes' => 30,
            'is_active' => true,
        ]);

        $brakeInspection = ServiceType::create([
            'name' => 'Brake Inspection',
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        $tireReplacement = ServiceType::create([
            'name' => 'Tire Replacement',
            'duration_minutes' => 90,
            'is_active' => true,
        ]);

        $generalMaintenance = ServiceType::create([
            'name' => 'General Maintenance',
            'duration_minutes' => 120,
            'is_active' => true,
        ]);

        $technicianA = Technician::create([
            'dealership_id' => $dealership->id,
            'name' => 'Technician A',
            'is_active' => true,
        ]);

        $technicianB = Technician::create([
            'dealership_id' => $dealership->id,
            'name' => 'Technician B',
            'is_active' => true,
        ]);

        $technicianC = Technician::create([
            'dealership_id' => $dealership->id,
            'name' => 'Technician C',
            'is_active' => true,
        ]);

        $technicianA->serviceTypes()->attach([
            $oilChange->id,
            $brakeInspection->id,
        ]);

        $technicianB->serviceTypes()->attach([
            $oilChange->id,
            $tireReplacement->id,
        ]);

        $technicianC->serviceTypes()->attach([
            $brakeInspection->id,
            $generalMaintenance->id,
        ]);
    }
}
