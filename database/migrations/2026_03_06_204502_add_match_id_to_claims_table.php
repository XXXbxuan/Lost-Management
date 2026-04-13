<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('claims', function (Blueprint $table) {

            $table->unsignedBigInteger('match_id')->nullable()->after('id');

            $table->foreign('match_id')
                  ->references('id')
                  ->on('match_records')
                  ->onDelete('set null'); 
        });
    }

    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropForeign(['match_id']);
            $table->dropColumn('match_id');
        });
    }
};