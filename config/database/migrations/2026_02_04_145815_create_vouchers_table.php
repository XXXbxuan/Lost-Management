<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('name');       // e.g., "Starbucks Coffee"
            $table->string('category');   // e.g., "Food & Beverage"
            $table->integer('points');    // e.g., 500
            $table->text('description');  // e.g., "Redeem a tall latte..."
            $table->timestamps();
        });
    }
};
