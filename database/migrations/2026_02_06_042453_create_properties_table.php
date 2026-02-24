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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->text('address');
            $table->string('city', 100);
            $table->string('province', 100);
            $table->string('postal_code', 20);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->enum('property_type', ['house', 'condo', 'apartment', 'commercial']);
            $table->bigInteger('price');
            $table->integer('bedrooms');
            $table->integer('bathrooms');
            $table->decimal('lot_size', 10, 2)->nullable();
            $table->decimal('floor_area', 10, 2)->nullable();
            $table->integer('year_built')->nullable();
            $table->enum('status', ['available', 'sold', 'rented', 'unlisted'])->default('available');
            $table->boolean('featured')->default(false);
            $table->integer('view_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('agent_id');
            $table->index('city');
            $table->index('property_type');
            $table->index('status');
            $table->index('price');
            $table->index('created_at');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};