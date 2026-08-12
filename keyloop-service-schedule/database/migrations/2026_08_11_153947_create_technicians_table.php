<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technicians', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dealership_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('name', 150);
            $table->boolean('is_active')->default(true);
            $table->timestampsTz();

            $table->index([
                'dealership_id',
                'is_active',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technicians');
    }
};
