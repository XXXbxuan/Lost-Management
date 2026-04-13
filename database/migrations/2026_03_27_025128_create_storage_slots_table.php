<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('storage_slots', function (Blueprint $table) {
            $table->id();
            $table->string('zone_code');      
            $table->string('shelf_code');    
            $table->string('slot_code');      
            $table->string('full_code')->unique(); 
            $table->string('slot_status')->default('Available'); 
            $table->text('remark')->nullable();
            $table->timestamps();

            $table->unique(['zone_code', 'shelf_code', 'slot_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('storage_slots');
    }
};