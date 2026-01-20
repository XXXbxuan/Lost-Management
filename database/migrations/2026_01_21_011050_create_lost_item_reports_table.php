<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lost_item_reports', function (Blueprint $table) {
            $table->id();
            
            // --- 1. 乘客信息 ---
            $table->string('passenger_name');
            $table->string('passenger_email');
            $table->string('passenger_phone');

            // --- 2. 物品详情 (复用组件字段) ---
            $table->string('item_name');
            $table->string('category');
            $table->string('brand')->nullable();
            $table->string('color');
            $table->string('serial_number')->nullable();
            $table->string('image_path')->nullable();

            // --- 3. 丢失信息 ---
            $table->string('lost_location');
            $table->string('flight_number')->nullable();
            $table->dateTime('lost_time');
            $table->text('description')->nullable();

            // --- 4. 系统状态 ---
            $table->string('status')->default('Lost'); // 默认状态为丢失

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lost_item_reports');
    }
};