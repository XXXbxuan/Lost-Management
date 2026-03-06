<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            // 🌟 1. 增加 match_id 欄位，放在 id 後面
            // 使用 nullable 是為了防止現有的舊資料因為沒有值而報錯
            $table->unsignedBigInteger('match_id')->nullable()->after('id');

            // 🌟 2. 設定外鍵約束，確保數據完整性
            $table->foreign('match_id')
                  ->references('id')
                  ->on('match_records')
                  ->onDelete('set null'); // 如果匹配紀錄被刪，結案單保留但 match_id 變 null
        });
    }

    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            // 倒回時先刪除外鍵，再刪除欄位
            $table->dropForeign(['match_id']);
            $table->dropColumn('match_id');
        });
    }
};