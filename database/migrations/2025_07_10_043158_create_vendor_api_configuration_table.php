<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('api_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_api_id')->constrained()->onDelete('cascade');
            $table->string('config_key'); // Kunci konfigurasi
            $table->text('config_value'); // Nilai konfigurasi
            $table->enum('data_type', ['string', 'integer', 'boolean', 'json', 'encrypted'])->default('string');
            $table->text('description')->nullable(); // Deskripsi konfigurasi
            $table->boolean('is_sensitive')->default(false); // Apakah data sensitif
            $table->timestamps();
            
            $table->unique(['vendor_api_id', 'config_key']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('api_configurations');
    }
};
