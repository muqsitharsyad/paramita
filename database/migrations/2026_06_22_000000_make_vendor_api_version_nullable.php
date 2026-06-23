<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE vendor_apis MODIFY version VARCHAR(255) NULL DEFAULT NULL");
    }

    public function down(): void
    {
        DB::table('vendor_apis')->whereNull('version')->update(['version' => '']);
        DB::statement("ALTER TABLE vendor_apis MODIFY version VARCHAR(255) NOT NULL DEFAULT ''");
    }
};
