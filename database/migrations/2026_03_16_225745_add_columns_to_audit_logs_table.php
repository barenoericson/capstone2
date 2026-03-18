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
        Schema::table('audit_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('audit_logs', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('audit_logs', 'action')) {
                $table->string('action')->after('user_id');
            }
            if (!Schema::hasColumn('audit_logs', 'action_type')) {
                $table->string('action_type')->after('action');
            }
            if (!Schema::hasColumn('audit_logs', 'model_type')) {
                $table->string('model_type')->nullable()->after('action_type');
            }
            if (!Schema::hasColumn('audit_logs', 'model_id')) {
                $table->unsignedBigInteger('model_id')->nullable()->after('model_type');
            }
            if (!Schema::hasColumn('audit_logs', 'description')) {
                $table->text('description')->nullable()->after('model_id');
            }
            if (!Schema::hasColumn('audit_logs', 'old_values')) {
                $table->json('old_values')->nullable()->after('description');
            }
            if (!Schema::hasColumn('audit_logs', 'new_values')) {
                $table->json('new_values')->nullable()->after('old_values');
            }
            if (!Schema::hasColumn('audit_logs', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('new_values');
            }
            if (!Schema::hasColumn('audit_logs', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id', 'action', 'action_type', 'model_type', 'model_id',
                'description', 'old_values', 'new_values', 'ip_address', 'user_agent',
            ]);
        });
    }
};
