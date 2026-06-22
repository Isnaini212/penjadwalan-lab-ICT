<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengunguman', function (Blueprint $table) {
            $table->integer('schedule_delay')->default(15)->after('message');
        });
    }

    public function down(): void
    {
        Schema::table('pengunguman', function (Blueprint $table) {
            $table->dropColumn('schedule_delay');
        });
    }
};
