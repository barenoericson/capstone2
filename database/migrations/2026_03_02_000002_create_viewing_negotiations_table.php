<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('viewing_negotiations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('viewing_id')->constrained()->onDelete('cascade');
            $table->foreignId('proposed_by_user_id')->constrained('users')->onDelete('cascade');
            $table->enum('proposed_by_role', ['buyer', 'agent']);
            $table->date('proposed_date');
            $table->time('proposed_time');
            $table->text('note')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'superseded'])->default('pending');
            $table->timestamps();

            $table->index('viewing_id');
            $table->index('proposed_by_user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('viewing_negotiations');
    }
};
