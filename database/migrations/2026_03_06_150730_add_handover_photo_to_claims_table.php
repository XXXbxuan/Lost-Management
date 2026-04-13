<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->string('handover_photo')->nullable()->after('claimerIcPassport');
        });
    }

    public function down()
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropColumn('handover_photo');
        });
    }
};