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
            $table->boolean('locked')->default(false);
            $table->json('visual_adjustments')->nullable()->comment('Stores brightness, contrast, saturation, etc.');
            $table->timestamps();
            
            // Add indexes for frequently queried fields
            $table->index(['composite_id', 'z_index']);
            $table->index('facial_feature_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('composite_facial_features');
        Schema::dropIfExists('facial_features');
    }
};
