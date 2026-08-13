<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AppointmentController;
use App\Http\Controllers\Api\V1\AvailabilityController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {

    Route::prefix('user')->group(function (): void {
        Route::get('dealerships/{dealership}/availability', [AvailabilityController::class, 'show'])->whereNumber('dealership');
        Route::post('appointments', [AppointmentController::class, 'store']);
    });
});
