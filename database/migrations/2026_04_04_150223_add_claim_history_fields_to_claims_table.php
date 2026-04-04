<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->string('receipt_no')->nullable()->after('id');
            $table->dateTime('appointment_at')->nullable()->after('receipt_no');
            $table->string('appointment_venue')->nullable()->after('appointment_at');
            $table->string('processed_by_name')->nullable()->after('processedBy');
            $table->text('handover_notes')->nullable()->after('handover_photo');
        });
    }

    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropColumn([
                'receipt_no',
                'appointment_at',
                'appointment_venue',
                'processed_by_name',
                'handover_notes',
            ]);
        });
    }
};