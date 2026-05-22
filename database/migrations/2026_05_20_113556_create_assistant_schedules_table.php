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
        Schema::create('assistant_schedules', function (Blueprint $table) {
            $table->id('id_asisten');
            $table->string('nama_asisten');
            $table->time('jm_mulai');
            $table->time('jm_selesai');
            $table->string('matkul');
            $table->string('hari_matkul');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assistant_schedules');
    }
};
