<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lostId');
            $table->unsignedBigInteger('foundId');
            $table->string('claimerName'); // 领物人姓名
            $table->string('claimerIcPassport'); // 领物人证件号
            $table->string('claimerPhone');
            $table->unsignedBigInteger('processedBy'); // 处理的员工
            $table->timestamp('claimedAt'); // 认领时间
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};
