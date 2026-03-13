<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('found_items', function (Blueprint $table) {
            $table->id();
            // 基础信息
            $table->string('item_name');
            $table->string('category');
            $table->string('brand')->nullable();
            $table->string('color')->nullable();
            
            // 核心匹配 & 追踪
            $table->string('serial_number')->nullable();
            
            // 地点 & 航班
            $table->string('found_location'); 
            $table->string('flight_number')->nullable();
            
            $table->dateTime('found_time');
            $table->text('description')->nullable();
            
            // 仓库位置
            $table->string('storage_location')->nullable();
            
            // 其他
            $table->string('image_path')->nullable(); 
            $table->string('status')->default('Unclaimed'); 
            $table->foreignId('staff_id')->nullable()->constrained('staff', 'staff_id')->onDelete('set null');
            $table->string('registered_by_name')->nullable(); 

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('found_items');
    }
};