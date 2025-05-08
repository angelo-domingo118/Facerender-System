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
        Schema::create('facial_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feature_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('feature_category_id')->constrained()->onDelete('cascade');
            $table->string('feature_code')->unique();
            $table->string('name');
            $table->string('image_path');
            $table->enum('gender', ['male', 'female']);
            $table->timestamps();
        });

        Schema::create('composite_facial_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('composite_id')->constrained()->onDelete('cascade');
            $table->foreignId('facial_feature_id')->constrained()->onDelete('cascade');
            $table->float('position_x')->default(0);
            $table->float('position_y')->default(0);
            $table->integer('z_index')->default(0);
            $table->float('scale_x')->default(1.0);
            $table->float('scale_y')->default(1.0);
            $table->float('rotation')->default(0);
            $table->float('opacity')->default(1.0);
            $table->boolean('visible')->default(true);
            $table->float('brightness')->default(0); // Typically 0 means no change, adjust range as needed
            $table->float('contrast')->default(1.0);   // Typically 1.0 means no change
            $table->float('saturation')->default(1.0); // Typically 1.0 means no change
            $table->float('sharpness')->default(0);   // Typically 0 means no change
            $table->float('feathering')->default(0);  // Typically 0 means no feathering
            $table->float('skinTone')->default(0);    // Placeholder, adjust range/meaning as needed
            $table->timestamps(); // Add timestamps if needed according to your class diagram/requirements
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('composite_facial_features'); // Add drop table for reverse migration
        Schema::dropIfExists('facial_features');
    }
};
