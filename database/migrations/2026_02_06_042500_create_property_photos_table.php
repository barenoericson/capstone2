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
        Schema::create('property_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->string('photo_path');
            $table->string('photo_url');
            $table->integer('quality_score')->nullable();
            $table->boolean('is_cover')->default(false);
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('property_id');
            $table->index('is_cover');
            $table->index('approval_status');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_photos');
    }
};