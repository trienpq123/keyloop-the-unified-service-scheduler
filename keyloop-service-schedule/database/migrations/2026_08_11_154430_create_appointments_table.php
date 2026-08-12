<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('vehicle_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('dealership_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('service_type_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('technician_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('service_bay_id')
                ->constrained('service_bays')
                ->restrictOnDelete();

            $table->string('status', 30)->default('confirmed');

            $table->timestampTz('start_at');
            $table->timestampTz('end_at');

            $table->text('customer_note')->nullable();

            $table->timestampTz('cancelled_at')->nullable();
            $table->string('cancellation_reason', 500)->nullable();

            $table->timestampsTz();

            $table->index([
                'dealership_id',
                'start_at',
                'end_at',
            ]);

            $table->index([
                'customer_id',
                'created_at',
            ]);

            $table->index([
                'vehicle_id',
                'created_at',
            ]);

            $table->index([
                'technician_id',
                'start_at',
            ]);

            $table->index([
                'service_bay_id',
                'start_at',
            ]);

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
