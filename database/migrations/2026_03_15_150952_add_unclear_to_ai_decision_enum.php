<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE agent_applications MODIFY COLUMN ai_decision ENUM('approved', 'rejected', 'unclear', 'error') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE agent_applications MODIFY COLUMN ai_decision ENUM('approved', 'rejected', 'error') NULL");
    }
};
