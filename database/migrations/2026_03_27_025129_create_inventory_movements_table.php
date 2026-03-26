<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('found_items', function (Blueprint $table) {
            $table->timestamp('removed_at')->nullable()->after('status');
            $table->text('removal_reason')->nullable()->after('removed_at');
        });
    }

    public function down(): void
    {
        Schema::table('found_items', function (Blueprint $table) {
            $table->dropColumn(['removed_at', 'removal_reason']);
        });
    }
};