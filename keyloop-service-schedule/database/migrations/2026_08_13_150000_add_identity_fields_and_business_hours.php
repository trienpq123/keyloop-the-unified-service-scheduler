<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table): void {
            $table->string('normalized_email')->nullable()->unique()->after('email');
            $table->string('normalized_phone', 30)->nullable()->unique()->after('phone');
        });

        Schema::table('vehicles', function (Blueprint $table): void {
            $table->string('normalized_registration_number', 30)->nullable()->unique()->after('registration_number');
        });

        Schema::create('business_hours', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('dealership_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('weekday');
            $table->time('opens_at');
            $table->time('closes_at');
            $table->timestampsTz();
            $table->unique(['dealership_id', 'weekday']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_hours');
        Schema::table('vehicles', function (Blueprint $table): void {
            $table->dropUnique(['normalized_registration_number']);
            $table->dropColumn('normalized_registration_number');
        });
        Schema::table('customers', function (Blueprint $table): void {
            $table->dropUnique(['normalized_email']);
            $table->dropUnique(['normalized_phone']);
            $table->dropColumn(['normalized_email', 'normalized_phone']);
        });
    }
};
