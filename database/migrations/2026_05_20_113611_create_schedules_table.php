<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    Schema::create('schedules', function (Blueprint $table) {
    $table->id('id_jadwal');
    $table->foreignId('id_lab')->constrained('labs', 'id_lab')->onDelete('cascade');
    $table->foreignId('id_asisten')->nullable()->constrained('assistant_schedules', 'id_asisten')->onDelete('cascade');
    $table->date('tanggal');
    $table->string('hari'); 
    $table->time('jam_mulai'); 
    $table->time('jam_selesai'); 
    $table->string('matkul');
    $table->integer('sks');
    $table->string('dosen');
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};