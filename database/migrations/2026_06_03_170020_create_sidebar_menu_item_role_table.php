<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sidebar_menu_item_role', function (Blueprint $table) {
            $table->foreignId('sidebar_menu_item_id')->constrained('sidebar_menu_items')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['sidebar_menu_item_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sidebar_menu_item_role');
    }
};
