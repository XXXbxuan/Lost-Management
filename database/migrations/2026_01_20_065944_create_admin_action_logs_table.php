<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('admin_action_logs', function (Blueprint $table) {
            $table->id();
            

            $table->string('admin_name')->nullable();
            

            $table->string('action_type');
            
            $table->string('target_name');
            
            $table->text('details')->nullable(); 
            
            $table->timestamps(); 
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('admin_action_logs');
    }
};
