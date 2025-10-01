<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('api_endpoints', function (Blueprint $table) {
            // Tambah kolom foreign key
            $table->foreignId('json_template_id')
                  ->nullable()
                  ->constrained('json_templates')
                  ->onDelete('set null');

            // Hapus kolom lama
            $table->dropColumn('response_format');
        });
    }

    public function down()
    {
        Schema::table('api_endpoints', function (Blueprint $table) {
            // Rollback: hapus foreign key & kolom
            $table->dropForeign(['json_template_id']);
            $table->dropColumn('json_template_id');

            // Rollback: kembalikan kolom response_format
            $table->text('response_format')->nullable();
        });
    }
};
