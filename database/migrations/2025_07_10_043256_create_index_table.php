<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->index(['status', 'created_at']);
            $table->index('code');
        });

        Schema::table('vendor_apis', function (Blueprint $table) {
            $table->index(['vendor_id', 'status']);
            $table->index('last_tested_at');
        });

        Schema::table('api_requests', function (Blueprint $table) {
            $table->index(['vendor_api_id', 'status']);
            $table->index(['requested_at', 'status']);
            $table->index('request_id');
        });
    }

    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['code']);
        });

        Schema::table('vendor_apis', function (Blueprint $table) {
            $table->dropIndex(['vendor_id', 'status']);
            $table->dropIndex(['last_tested_at']);
        });

        Schema::table('api_requests', function (Blueprint $table) {
            $table->dropIndex(['vendor_api_id', 'status']);
            $table->dropIndex(['requested_at', 'status']);
            $table->dropIndex(['request_id']);
        });
    }
};
