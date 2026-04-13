<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id('staff_id'); 
            

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->string('name');
            
            $table->string('contact_number', 20)->nullable();
            
            $table->string('status')->default('Active')->index();
            
            $table->string('department')->nullable()->default('Terminal Operations');

            $table->timestamps(); 
            $table->softDeletes(); 
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
