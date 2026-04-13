<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('match_records', function (Blueprint $table) {
            $table->string('appointment_venue')->nullable()->change();
            $table->dateTime('appointment_at')->nullable()->change();
        });
    }


    public function down(): void
    {
        Schema::table('match_records', function (Blueprint $table) {
            $table->string('appointment_venue')->change(); 
            $table->dateTime('appointment_at')->change();
        });
    }
    };
