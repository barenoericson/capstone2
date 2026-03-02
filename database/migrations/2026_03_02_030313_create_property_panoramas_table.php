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
        Schema::create('property_panoramas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->string('image_url');
            $table->string('room_name', 100);
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->json('hotspots')->nullable();
            $table->decimal('default_yaw', 8, 4)->default(0);
            $table->decimal('default_pitch', 8, 4)->default(0);
            $table->decimal('hfov', 8, 4)->default(100);
            $table->timestamps();
            $table->softDeletes();

            $table->index('property_id');
            $table->index('sort_order');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_panoramas');
    }
};
