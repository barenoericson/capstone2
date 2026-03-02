<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'negotiating' to the status enum
        DB::statement("ALTER TABLE viewings MODIFY COLUMN status ENUM('requested','approved','rejected','completed','negotiating') DEFAULT 'requested'");

        Schema::table('viewings', function (Blueprint $table) {
            $table->string('google_calendar_event_id')->nullable()->after('reminder_sent_at');
            $table->timestamp('approved_at')->nullable()->after('google_calendar_event_id');
            $table->unsignedBigInteger('approved_by_user_id')->nullable()->after('approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('viewings', function (Blueprint $table) {
            $table->dropColumn(['google_calendar_event_id', 'approved_at', 'approved_by_user_id']);
        });

        DB::statement("ALTER TABLE viewings MODIFY COLUMN status ENUM('requested','approved','rejected','completed') DEFAULT 'requested'");
    }
};
