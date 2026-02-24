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
        Schema::table('agent_applications', function (Blueprint $table) {
            $table->string('applicant_name')->nullable()->after('user_id');
            $table->string('company_address')->nullable()->after('company_name');
            $table->string('accreditation')->nullable()->after('company_address');
            $table->string('prc_license_photo')->nullable()->after('prc_id');
            $table->date('license_expiry_date')->nullable()->after('prc_license_photo');
            $table->text('ai_verification_result')->nullable()->after('license_expiry_date');
            $table->enum('ai_decision', ['approved', 'rejected', 'error'])->nullable()->after('ai_verification_result');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agent_applications', function (Blueprint $table) {
            $table->dropColumn([
                'applicant_name',
                'company_address',
                'accreditation',
                'prc_license_photo',
                'license_expiry_date',
                'ai_verification_result',
                'ai_decision',
            ]);
        });
    }
};
