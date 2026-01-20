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
        Schema::create('lost_items', function (Blueprint $table) {
            $table->id();
            
            // 1. 基础信息
            $table->string('item_name');
            $table->string('category');
            $table->string('brand')->nullable(); // [关键] 必须有这个，否则报错 column not found
            $table->string('color')->nullable();
            
            // 2. 核心匹配 & 追踪
            $table->string('serial_number')->nullable();
            
            // 3. 地点 & 航班 (Smart Logic)
            $table->string('found_location'); // Area (大范围)
            $table->string('flight_number')->nullable(); // [关键] 只有飞机上捡到才填
            
            $table->dateTime('found_time');
            $table->text('description')->nullable(); // 具体位置 (小范围)
            
            // 4. 仓库位置 (Inventory)
            $table->string('storage_location')->nullable(); // [关键] e.g. GEN-S1-05
            
            // 5. 其他辅助
            $table->string('image_path')->nullable(); 
            $table->string('status')->default('Unclaimed'); 
            
            // 6. 外键关联 (注意这里指定了 staff_id，防止报错)
            $table->foreignId('staff_id')->nullable()->constrained('staff', 'staff_id')->onDelete('set null');
            $table->string('registered_by_name')->nullable(); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lost_items');
    }
};