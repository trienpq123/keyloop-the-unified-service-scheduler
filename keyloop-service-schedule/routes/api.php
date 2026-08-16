<?php

use App\Http\Controllers\Api\V1\AppointmentController;
use App\Http\Controllers\Api\V1\AvailabilityController;
use App\Http\Controllers\Api\V1\CatalogController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('dealerships', [CatalogController::class, 'dealerships']);
    Route::get('dealerships/{dealership}/availability', [AvailabilityController::class, 'show'])->whereNumber('dealership');
    Route::post('appointments', [AppointmentController::class, 'store']);
    Route::get('appointments/{appointment}', [AppointmentController::class, 'show'])->whereNumber('appointment');
    Route::patch('appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->whereNumber('appointment');
});
