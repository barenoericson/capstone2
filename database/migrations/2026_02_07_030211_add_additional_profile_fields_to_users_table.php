<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Only add if they don't exist
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('profile_photo_path');
            }
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'province')) {
                $table->string('province')->nullable()->after('city');
            }
            if (!Schema::hasColumn('users', 'postal_code')) {
                $table->string('postal_code')->nullable()->after('province');
            }
            if (!Schema::hasColumn('users', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('postal_code');
            }
            if (!Schema::hasColumn('users', 'preferences')) {
                $table->json('preferences')->nullable()->after('status');
            }
            if (!Schema::hasColumn('users', 'two_factor_secret')) {
                $table->string('two_factor_secret')->nullable()->after('preferences');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumnIfExists([
                'address',
                'city',
                'province',
                'postal_code',
                'date_of_birth',
                'preferences',
                'two_factor_secret',
            ]);
        });
    }
};