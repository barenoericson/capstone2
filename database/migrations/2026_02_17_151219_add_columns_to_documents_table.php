<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('agent_id')->constrained('agents')->onDelete('cascade')->after('id');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade')->after('agent_id');
            $table->foreignId('property_id')->nullable()->constrained('properties')->onDelete('set null')->after('buyer_id');
            $table->string('document_name')->after('property_id');
            $table->string('document_type')->default('contract')->after('document_name'); // contract, agreement, disclosure, other
            $table->string('document_path')->after('document_type');           // storage path
            $table->string('document_url')->nullable()->after('document_path'); // public URL
            $table->bigInteger('file_size')->nullable()->after('document_url');
            $table->string('status')->default('pending')->after('file_size'); // pending, signed
            $table->timestamp('signed_at')->nullable()->after('status');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn([
                'agent_id', 'buyer_id', 'property_id', 'document_name',
                'document_type', 'document_path', 'document_url', 'file_size',
                'status', 'signed_at', 'deleted_at',
            ]);
        });
    }
};
