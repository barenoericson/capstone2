<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('uploaded_by')->nullable()->after('id')->constrained('users')->onDelete('cascade');
            $table->string('mime_type')->nullable()->after('file_size');
            $table->json('annotations')->nullable()->after('mime_type');
        });

        // Make agent_id and buyer_id nullable for personal uploads
        Schema::table('documents', function (Blueprint $table) {
            $table->unsignedBigInteger('agent_id')->nullable()->change();
            $table->unsignedBigInteger('buyer_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['uploaded_by']);
            $table->dropColumn(['uploaded_by', 'mime_type', 'annotations']);
        });
    }
};
