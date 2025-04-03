<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration updates the foreign key constraint on composites table
     * to prevent cascading deletes when a witness is deleted.
     * Instead, it will keep the composite but make the witness_id nullable.
     */
    public function up(): void
    {
        // Step 1: Drop the existing foreign key constraint
        Schema::table('composites', function (Blueprint $table) {
            $table->dropForeign(['witness_id']);
        });
        
        // Step 2: Modify the column to be nullable using raw SQL
        DB::statement('ALTER TABLE composites MODIFY witness_id BIGINT UNSIGNED NULL');
        
        // Step 3: Add the new foreign key with SET NULL on delete
        Schema::table('composites', function (Blueprint $table) {
            $table->foreign('witness_id')
                  ->references('id')
                  ->on('witnesses')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Drop the modified foreign key
        Schema::table('composites', function (Blueprint $table) {
            $table->dropForeign(['witness_id']);
        });
        
        // Step 2: Modify the column to NOT NULL using raw SQL
        DB::statement('ALTER TABLE composites MODIFY witness_id BIGINT UNSIGNED NOT NULL');
        
        // Step 3: Restore the original foreign key with cascade
        Schema::table('composites', function (Blueprint $table) {
            $table->foreign('witness_id')
                  ->references('id')
                  ->on('witnesses')
                  ->onDelete('cascade');
        });
    }
};
