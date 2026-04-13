<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lostId');
            $table->unsignedBigInteger('foundId');
            $table->unsignedBigInteger('processedBy');
            $table->string('claimerName');
            $table->string('claimerIcPassport');
            $table->string('claimerPhone');
            $table->timestamp('claimedAt');
            $table->timestamps();

            $table->foreign('lostId')->references('id')->on('lost_item_reports')->onDelete('cascade');
            $table->foreign('foundId')->references('id')->on('found_items')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};
