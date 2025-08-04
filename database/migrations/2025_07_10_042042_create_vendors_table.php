<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorsTable extends Migration
{
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama vendor
            $table->string('code')->unique(); // Kode unik vendor
            $table->string('company_name'); // Nama perusahaan
            $table->text('description')->nullable(); // Deskripsi vendor
            $table->string('contact_person'); // Nama kontak person
            $table->string('email'); // Email vendor
            $table->string('phone'); // Nomor telepon
            $table->text('address'); // Alamat vendor
            $table->string('city'); // Kota
            $table->string('province'); // Provinsi
            $table->string('postal_code'); // Kode pos
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendors');
    }
}
