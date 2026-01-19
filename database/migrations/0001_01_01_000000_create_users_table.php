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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // 默认的主键 ID
            
            // [对应 UI] 新增 Username，必须唯一
            $table->string('username')->unique(); 
            
            // [基础字段] Email 和 密码
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            // [角色控制] 区分 Admin 和 Staff
            $table->string('role')->default('Staff');
            
            // [Pro级细节 - 安全审计] 记录最后登录信息
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();

            $table->rememberToken();
            $table->timestamps(); // 自动生成 created_at, updated_at
            
            // [Pro级细节 - 数据留存] 软删除
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
