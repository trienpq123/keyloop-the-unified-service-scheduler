<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AppointmentController;
use App\Http\Controllers\Api\V1\AvailabilityController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — v1
|--------------------------------------------------------------------------
|
| All routes are versioned under /api/v1 and receive the AssignRequestId
| middleware automatically via the 'api' middleware group defined in
| bootstrap/app.php.
|
*/

Route::prefix('v1')->group(function (): void {

    Route::prefix('user')->group(function (): void {
        Route::get('dealerships/{dealership}/availability', [AvailabilityController::class, 'show'])->whereNumber('dealership');
        Route::post('appointments', [AppointmentController::class, 'store']);
    });
});
