<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip')->nullable()->unique();
            $table->string('avatar')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('unit_kerja_id')->nullable()->after('password');
            $table->foreign('unit_kerja_id')->references('id')->on('unit_kerjas')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['unit_kerja_id']);
            $table->dropColumn(['nip', 'avatar', 'status', 'unit_kerja_id']);
        });
    }
};
