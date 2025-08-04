<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('api_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_api_id')->constrained()->onDelete('cascade');
            $table->foreignId('api_endpoint_id')->constrained()->onDelete('cascade');
            $table->string('request_id')->unique(); // ID unik request
            $table->enum('method', ['GET', 'POST', 'PUT', 'PATCH', 'DELETE']);
            $table->text('url'); // URL lengkap request
            $table->json('headers')->nullable(); // Headers request
            $table->json('parameters')->nullable(); // Parameter request
            $table->longText('request_body')->nullable(); // Body request
            $table->integer('response_code')->nullable(); // HTTP response code
            $table->json('response_headers')->nullable(); // Response headers
            $table->longText('response_body')->nullable(); // Response body
            $table->integer('response_time')->nullable(); // Response time dalam ms
            $table->enum('status', ['pending', 'success', 'failed', 'timeout'])->default('pending');
            $table->text('error_message')->nullable(); // Pesan error jika ada
            $table->timestamp('requested_at'); // Waktu request
            $table->timestamp('responded_at')->nullable(); // Waktu response
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('api_requests');
    }
};
