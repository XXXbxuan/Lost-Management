<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('voucher_id')->constrained()->onDelete('cascade');
            
            // 🌟 NEW: Add the status column and set the default to 'Active'
            $table->string('status')->default('Active'); 
            
            $table->timestamps(); // Keeps track of WHEN they redeemed it
        });
    }

    public function down()
    {
        Schema::dropIfExists('redemptions');
    }
};