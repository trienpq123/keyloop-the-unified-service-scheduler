<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('registration_number', 30);
            $table->string('make', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->unsignedSmallInteger('manufactured_year')->nullable();
            $table->timestampsTz();

            $table->unique('registration_number');
            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
