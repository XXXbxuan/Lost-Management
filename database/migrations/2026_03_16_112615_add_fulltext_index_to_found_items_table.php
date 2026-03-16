<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('found_items', function (Blueprint $table) {
            // Full-text index for smarter text matching
            $table->fullText(['item_name', 'brand', 'description'], 'found_items_fulltext');
        });
    }

    public function down(): void
    {
        Schema::table('found_items', function (Blueprint $table) {
            $table->dropFullText('found_items_fulltext');
        });
    }
};