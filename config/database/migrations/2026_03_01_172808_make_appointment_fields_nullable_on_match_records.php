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
        Schema::table('match_records', function (Blueprint $table) {
            // 🌟 將這兩個欄位改為可為空 (nullable)
            $table->string('appointment_venue')->nullable()->change();
            $table->dateTime('appointment_at')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_records', function (Blueprint $table) {
            $table->string('appointment_venue')->change(); // 如果要還原，則改回不可為空
            $table->dateTime('appointment_at')->change();
        });
    }
    };
