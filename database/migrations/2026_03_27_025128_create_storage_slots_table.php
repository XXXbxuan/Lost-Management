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
            $table->string('zone_code');      // GEN / VAULT / BAG
            $table->string('shelf_code');     // S1 / S2 / S3
            $table->string('slot_code');      // 01 ~ 10
            $table->string('full_code')->unique(); // GEN-S1-01
            $table->string('slot_status')->default('Available'); // Available / Service
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