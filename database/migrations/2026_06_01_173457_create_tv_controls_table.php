<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel untuk teks pengumuman berjalan
        Schema::create('pengunguman', function (Blueprint $table) {
            $table->id();
            $table->text('message');
            $table->timestamps();
        });

        // Tabel untuk daftar gambar slide JPG
        Schema::create('slide_tv', function (Blueprint $table) {
            $table->id();
            $table->string('image_path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tv_announcements');
        Schema::dropIfExists('tv_slides');
    }
};