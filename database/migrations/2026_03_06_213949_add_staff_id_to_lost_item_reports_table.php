<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lost_item_reports', function (Blueprint $table) {
            // 🌟 增加 staff_id 欄位，允許為空 (因為有些是旅客自己填的)
            $table->unsignedBigInteger('staff_id')->nullable()->after('id')->comment('紀錄是哪位員工代為登記的');
        });
    }

    public function down()
    {
        Schema::table('lost_item_reports', function (Blueprint $table) {
            $table->dropColumn('staff_id');
        });
    }
};