<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::table('customers')->whereNull('normalized_email')->whereNotNull('email')
            ->update(['normalized_email' => DB::raw('lower(trim(email))')]);
        DB::table('customers')->whereNull('normalized_phone')->whereNotNull('phone')
            ->update(['normalized_phone' => DB::raw("nullif(regexp_replace(phone, '[^0-9]', '', 'g'), '')")]);
        DB::table('vehicles')->whereNull('normalized_registration_number')
            ->update(['normalized_registration_number' => DB::raw("upper(regexp_replace(registration_number, '[^a-zA-Z0-9]', '', 'g'))")]);
    }

    public function down(): void
    {
        // Normalized fields are source-derived and intentionally retained on rollback.
    }
};
