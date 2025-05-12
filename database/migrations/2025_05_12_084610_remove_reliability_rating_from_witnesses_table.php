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
        Schema::table('witnesses', function (Blueprint $table) {
            $table->dropColumn('reliability_rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('witnesses', function (Blueprint $table) {
            $table->integer('reliability_rating')->nullable()->after('relationship_to_case');
        });
    }
};
