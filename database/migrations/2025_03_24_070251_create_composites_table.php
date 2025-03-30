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
        Schema::create('composites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained()->onDelete('cascade');
            $table->foreignId('witness_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('canvas_width');
            $table->integer('canvas_height');
            $table->string('final_image_path')->nullable();
            $table->string('suspect_gender')->nullable();
            $table->string('suspect_ethnicity')->nullable();
            $table->string('suspect_age_range')->nullable();
            $table->string('suspect_height')->nullable();
            $table->string('suspect_body_build')->nullable();
            $table->text('suspect_additional_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('composites');
    }
};
