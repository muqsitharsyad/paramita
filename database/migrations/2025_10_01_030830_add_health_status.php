<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('api_endpoints', function (Blueprint $table) {
            $table->enum('health_status', ['unknown', 'healthy', 'unhealthy', 'warning'])->default('unknown')->after('status');
            $table->timestamp('last_tested_at')->nullable()->after('health_status');
            $table->text('health_message')->nullable()->after('last_tested_at');
        });
    }

    public function down()
    {
        Schema::table('api_endpoints', function (Blueprint $table) {
            $table->dropColumn(['health_status', 'last_tested_at', 'health_message']);
        });
    }
};
