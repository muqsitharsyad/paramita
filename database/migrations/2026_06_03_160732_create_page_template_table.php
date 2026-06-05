<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_template', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->foreignId('json_template_id')->constrained()->cascadeOnDelete();
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->unique(['page_id', 'json_template_id']);
            $table->index('page_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_template');
    }
};
