<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_type_technician', function (Blueprint $table) {
            $table->foreignId('technician_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('service_type_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unique([
                'technician_id',
                'service_type_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_type_technician');
    }
};
