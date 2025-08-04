<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('vendor_apis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->string('api_name'); // Nama API
            $table->string('base_url'); // URL dasar API
            $table->string('version')->default('v1'); // Versi API
            $table->enum('auth_type', ['none', 'api_key', 'bearer_token', 'basic_auth', 'oauth2'])->default('api_key');
            $table->text('auth_credentials')->nullable(); // Kredensial autentikasi (encrypted)
            $table->json('headers')->nullable(); // Header tambahan
            $table->integer('timeout')->default(30); // Timeout dalam detik
            $table->integer('rate_limit')->nullable(); // Limit request per menit
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->timestamp('last_tested_at')->nullable(); // Terakhir ditest
            $table->boolean('is_healthy')->default(true); // Status kesehatan API
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendor_apis');
    }
};
