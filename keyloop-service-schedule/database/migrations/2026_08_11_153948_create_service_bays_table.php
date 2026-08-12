<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_bays', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dealership_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('name', 100);
            $table->boolean('is_active')->default(true);
            $table->timestampsTz();

            $table->unique([
                'dealership_id',
                'name',
            ]);

            $table->index([
                'dealership_id',
                'is_active',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_bays');
    }
};
