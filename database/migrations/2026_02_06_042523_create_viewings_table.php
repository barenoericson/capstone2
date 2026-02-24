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
        Schema::create('viewings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('agent_id')->constrained('agents')->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->date('viewing_date');
            $table->time('viewing_time');
            $table->enum('viewing_time_slot', ['morning', 'afternoon', 'evening'])->nullable();
            $table->enum('status', ['requested', 'approved', 'rejected', 'completed'])->default('requested');
            $table->text('rejection_reason')->nullable();
            $table->text('agent_notes')->nullable();
            $table->text('buyer_notes')->nullable();
            $table->boolean('reminder_sent')->default(false);
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('property_id');
            $table->index('agent_id');
            $table->index('buyer_id');
            $table->index('viewing_date');
            $table->index('status');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('viewings');
    }
};