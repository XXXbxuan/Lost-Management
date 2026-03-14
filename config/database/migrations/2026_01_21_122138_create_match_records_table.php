<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('match_records', function (Blueprint $table) {
            $table->id();
            // 关联报失单和拾获单
            $table->unsignedBigInteger('lostId');
            $table->unsignedBigInteger('foundId');
            
            // 验证信息
            $table->text('notes'); // 审核备注
            $table->string('status'); // 'Verified' 或 'Rejected'
            $table->unsignedBigInteger('verifiedBy'); // 处理的 Staff ID
            $table->timestamp('verifiedAt'); // 处理时间
            $table->integer('similarityScore')->default(0); // 存下当时的匹配分
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_records');
    }
};