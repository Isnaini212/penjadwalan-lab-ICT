<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat pivot table untuk relasi many-to-many
        Schema::create('schedule_assistant', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('schedule_id');
            $table->unsignedBigInteger('assistant_schedule_id');
            $table->timestamps();

            $table->foreign('schedule_id')
                  ->references('id_jadwal')
                  ->on('schedules')
                  ->onDelete('cascade');

            $table->foreign('assistant_schedule_id')
                  ->references('id_asisten')
                  ->on('assistant_schedules')
                  ->onDelete('cascade');

            $table->unique(['schedule_id', 'assistant_schedule_id'], 'schedule_assistant_unique');
        });

        // 2. Migrasi data existing dari kolom id_asisten ke pivot table
        $existingData = DB::table('schedules')
            ->whereNotNull('id_asisten')
            ->select('id_jadwal', 'id_asisten')
            ->get();

        foreach ($existingData as $row) {
            DB::table('schedule_assistant')->insert([
                'schedule_id' => $row->id_jadwal,
                'assistant_schedule_id' => $row->id_asisten,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Hapus foreign key dan kolom id_asisten dari tabel schedules
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['id_asisten']);
            $table->dropColumn('id_asisten');
        });
    }

    public function down(): void
    {
        // Kembalikan kolom id_asisten ke tabel schedules
        Schema::table('schedules', function (Blueprint $table) {
            $table->foreignId('id_asisten')
                  ->nullable()
                  ->after('id_lab')
                  ->constrained('assistant_schedules', 'id_asisten')
                  ->onDelete('cascade');
        });

        // Kembalikan data dari pivot table ke kolom id_asisten (ambil yang pertama saja)
        $pivotData = DB::table('schedule_assistant')
            ->select('schedule_id', DB::raw('MIN(assistant_schedule_id) as assistant_schedule_id'))
            ->groupBy('schedule_id')
            ->get();

        foreach ($pivotData as $row) {
            DB::table('schedules')
                ->where('id_jadwal', $row->schedule_id)
                ->update(['id_asisten' => $row->assistant_schedule_id]);
        }

        // Hapus pivot table
        Schema::dropIfExists('schedule_assistant');
    }
};
