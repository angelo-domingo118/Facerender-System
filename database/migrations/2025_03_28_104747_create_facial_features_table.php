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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facial_features');
    }
};
