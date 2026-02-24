<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_signings', function (Blueprint $table) {
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade')->after('id');
            $table->foreignId('signer_id')->constrained('users')->onDelete('cascade')->after('document_id');
            $table->string('signature_path')->after('signer_id');      // storage path of signature image
            $table->string('signature_url')->nullable()->after('signature_path'); // public URL
            $table->string('signature_type')->default('drawn')->after('signature_url'); // drawn | photo
            $table->timestamp('signed_at')->useCurrent()->after('signature_type');
        });
    }

    public function down(): void
    {
        Schema::table('document_signings', function (Blueprint $table) {
            $table->dropColumn([
                'document_id', 'signer_id', 'signature_path',
                'signature_url', 'signature_type', 'signed_at',
            ]);
        });
    }
};
