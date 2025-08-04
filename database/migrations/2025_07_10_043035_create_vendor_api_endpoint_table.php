<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('api_endpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_api_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Nama endpoint
            $table->string('path'); // Path endpoint
            $table->enum('method', ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'])->default('GET');
            $table->text('description')->nullable(); // Deskripsi endpoint
            $table->json('parameters')->nullable(); // Parameter yang diperlukan
            $table->json('response_format')->nullable(); // Format response
            $table->boolean('requires_auth')->default(true); // Apakah butuh autentikasi
            $table->enum('status', ['active', 'inactive', 'deprecated'])->default('active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('api_endpoints');
    }
};
