<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('match_records', function (Blueprint $table) {
            // 1. 预约时间 (允许为空，因为刚开始还没预约)
            $table->timestamp('appointment_at')->nullable()->after('status');
            
            // 2. 领取地点 (给个默认值，省得每次都填)
            $table->string('appointment_venue')->default('Lost & Found Centre (Admin Office)')->after('appointment_at');
            
            // 3. 验证 Token (生成的随机乱码，用来做 SMS 链接)
            $table->string('verification_token')->nullable()->after('appointment_venue');
            
            // 4. 用户是否确认 (默认是 false)
            $table->boolean('is_confirmed')->default(false)->after('verification_token');
            
            // 5. 确认时间
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
