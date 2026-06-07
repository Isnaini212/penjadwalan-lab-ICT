<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_dosen', function (Blueprint $table) {
            $table->id('id_booking'); // Primary Key

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->string('nm_dosen');
            $table->date('tanggal');
            $table->string('hari');
            
            
            $table->foreignId('id_lab')->constrained('labs', 'id_lab')->onDelete('cascade');
            
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->integer('kapasitas');
            $table->string('keperluan');
            $table->integer('sks');
            
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_dosen');
    }
};