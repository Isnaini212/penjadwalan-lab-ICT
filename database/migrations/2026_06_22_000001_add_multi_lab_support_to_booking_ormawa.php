<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_ormawa', function (Blueprint $table) {
            $table->unsignedSmallInteger('jumlah_lab')->default(1)->after('kapasitas');
        });

        Schema::create('booking_ormawa_labs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_booking')->constrained('booking_ormawa', 'id_booking')->onDelete('cascade');
            $table->foreignId('id_lab')->constrained('labs', 'id_lab')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['id_booking', 'id_lab']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_ormawa_labs');

        Schema::table('booking_ormawa', function (Blueprint $table) {
            $table->dropColumn('jumlah_lab');
        });
    }
};
