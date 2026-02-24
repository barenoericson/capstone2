<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agent_reviews', function (Blueprint $table) {
            $table->foreignId('agent_id')->constrained('agents')->onDelete('cascade')->after('id');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade')->after('agent_id');
            $table->foreignId('property_id')->nullable()->constrained('properties')->onDelete('set null')->after('buyer_id');
            $table->tinyInteger('rating')->after('property_id');             // 1–5
            $table->string('review_title', 100)->nullable()->after('rating');
            $table->text('review_content')->nullable()->after('review_title');
            $table->boolean('is_verified_transaction')->default(false)->after('review_content');
        });
    }

    public function down(): void
    {
        Schema::table('agent_reviews', function (Blueprint $table) {
            $table->dropColumn([
                'agent_id', 'buyer_id', 'property_id',
                'rating', 'review_title', 'review_content', 'is_verified_transaction',
            ]);
        });
    }
};
