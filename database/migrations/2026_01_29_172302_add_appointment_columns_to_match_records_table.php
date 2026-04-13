<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::table('match_records', function (Blueprint $table) {
            $table->timestamp('appointment_at')->nullable()->after('status');
            
            $table->string('appointment_venue')->default('Lost & Found Centre (Admin Office)')->after('appointment_at');
            
            $table->string('verification_token')->nullable()->after('appointment_venue');
            
            $table->boolean('is_confirmed')->default(false)->after('verification_token');
            
            $table->timestamp('confirmed_at')->nullable()->after('is_confirmed');
        });
    }

    public function down()
    {
        Schema::table('match_records', function (Blueprint $table) {
            $table->dropColumn([
                'appointment_at', 
                'appointment_venue', 
                'verification_token', 
                'is_confirmed', 
                'confirmed_at'
            ]);
        });
    }
};
