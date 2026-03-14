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
        Schema::create('staff', function (Blueprint $table) {
            $table->id('staff_id'); // [Design文档要求] 使用 staff_id 作为主键
            
            // [Pro级细节 - 外键约束] 
            // 绑定 users 表。如果 User 被删，这个 Staff 档案也会自动清理，保证数据干净。
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // [基础信息]
            $table->string('name');
            
            // [对应 UI] 你的设计图里有 Contact Number
            $table->string('contact_number', 20)->nullable();
            
            // [状态管理] Active / Blocked (加索引 index 提高查询速度)
            $table->string('status')->default('Active')->index();
            
            // [Pro级细节 - 部门模拟] 让系统看起来像真的机场系统
            $table->string('department')->nullable()->default('Terminal Operations');

            $table->timestamps(); // 自动生成 created_at, updated_at
            $table->softDeletes(); // [Pro级细节] 软删除
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
