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
            $table->string('item_name');
            $table->string('category');
            $table->string('brand')->nullable();
            $table->string('color')->nullable();
            
            $table->string('serial_number')->nullable();
            
            $table->string('found_location'); 
            $table->string('flight_number')->nullable();
            
            $table->dateTime('found_time');
            $table->text('description')->nullable();
            
            $table->string('storage_location')->nullable();
            
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