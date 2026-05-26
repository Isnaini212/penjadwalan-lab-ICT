<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assistant_schedules', function (Blueprint $table) {
            $table->id('id_asisten');
            $table->string('nama_asisten');
            $table->string('hari');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('mata_kuliah');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assistant_schedules');
    }
};