<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_ormawa', function (Blueprint $table) {
            $table->id('id_booking'); // Primary Key
            
            $table->string('nama_ormawa');
            $table->string('penanggung_jawab');
            $table->date('tanggal');
            $table->string('hari');
            
            // Gua tambahin lab default TBD, biar nanti SPV punya tempat buat ngisi lab-nya pas di-approve
            $table->string('lab')->default('TBD'); 
            
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->integer('kapasitas');
            $table->string('keperluan');
            $table->string('file_surat'); // Wajib untuk Ormawa
            
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_ormawa');
    }
};