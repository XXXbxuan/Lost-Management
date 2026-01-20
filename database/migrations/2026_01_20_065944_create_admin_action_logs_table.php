<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('admin_action_logs', function (Blueprint $table) {
            $table->id();
            
            // 1. 谁操作的？(Admin 的名字)
            $table->string('admin_name')->nullable();
            
            // 2. 做了什么？(Delete, Block, Create)
            $table->string('action_type');
            
            // 3. 目标是谁？(只存名字，不存 ID，因为 ID 可能会变或消失)
            $table->string('target_name');
            
            // 4. [关键] “记事本”字段：把被删的人的所有信息（Email, 电话, 部门）打包成一段文字存进去
            $table->text('details')->nullable(); 
            
            $table->timestamps(); // 记录时间
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_action_logs');
    }
};
