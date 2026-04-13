<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('match_records', function (Blueprint $table) {
            $table->dateTime('suggested_time_1')->nullable()->after('appointment_venue');
            $table->dateTime('suggested_time_2')->nullable()->after('suggested_time_1');
            $table->text('suggested_remarks')->nullable()->after('suggested_time_2');
            $table->timestamp('rejected_at')->nullable()->after('suggested_remarks');
        });
    }

    public function down(): void
    {
        Schema::table('match_records', function (Blueprint $table) {
            $table->dropColumn([
                'suggested_time_1', 
                'suggested_time_2', 
                'suggested_remarks', 
                'rejected_at'
            ]);
        });
    }
};