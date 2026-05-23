<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id('id_jadwal');

            $table->foreignId('id_lab')->constrained('labs', 'id_lab')->onDelete('cascade');
            $table->foreignId('id_asisten')->constrained('assistant_schedules', 'id_asisten')->onDelete('cascade');
            
            $table->date('tanggal');
            $table->string('hari');
            $table->string('lab');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('nama_asisten');
            $table->string('matkul');
            $table->integer('sks');
            $table->string('dosen');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
