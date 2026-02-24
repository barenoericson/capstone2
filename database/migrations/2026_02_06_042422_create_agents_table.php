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
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('real_estate_license_number');
            $table->string('prc_id_number');
            $table->enum('accreditation', ['REIN', 'PAMI', 'OTHER'])->nullable();
            $table->string('company_name');
            $table->text('company_address');
            $table->text('bio')->nullable();
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by_admin_id')->nullable()->constrained('users');
            $table->text('rejection_reason')->nullable();
            $table->date('license_expiry_date');
            $table->integer('listings_count')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->date('last_listing_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('verification_status');
            $table->index('license_expiry_date');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};