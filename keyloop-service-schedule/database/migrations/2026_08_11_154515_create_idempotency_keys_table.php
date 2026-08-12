<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('idempotency_keys', function (Blueprint $table) {
            $table->id();
            $table->string('scope', 100);
            $table->string('idempotency_key', 255);
            $table->string('request_hash', 64);
            $table->string('status', 20)->default('processing');
            $table->unsignedSmallInteger('response_status_code')->nullable();
            $table->jsonb('response_body')->nullable();
            $table->timestampTz('locked_at')->nullable();
            $table->timestampTz('completed_at')->nullable();
            $table->timestampTz('expires_at');
            $table->timestampsTz();

            $table->unique(['scope', 'idempotency_key']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('idempotency_keys');
    }
};
