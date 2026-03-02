<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_flags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->text('flag_reason');
            $table->json('ai_raw_response')->nullable();
            $table->decimal('ai_confidence', 5, 2)->default(0);
            $table->enum('status', ['pending_review', 'approved', 'dismissed'])->default('pending_review');
            $table->timestamp('flagged_at')->useCurrent();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index('property_id');
            $table->index('status');
            $table->index('flagged_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_flags');
    }
};
