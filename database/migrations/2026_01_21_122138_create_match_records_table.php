<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('match_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lostId');
            $table->unsignedBigInteger('foundId');
            
            $table->text('notes'); 
            $table->string('status'); 
            $table->unsignedBigInteger('verifiedBy'); 
            $table->timestamp('verifiedAt'); 
            $table->integer('similarityScore')->default(0); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_records');
    }
};