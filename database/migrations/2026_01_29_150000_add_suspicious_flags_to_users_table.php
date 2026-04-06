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
        Schema::table('users', function (Blueprint $table) {
            $table->json('suspicious_flags')->nullable()->after('is_merchant_approved');
            $table->integer('risk_score')->default(0)->after('suspicious_flags');
            $table->string('risk_level')->default('low')->after('risk_score');
            $table->timestamp('flagged_at')->nullable()->after('risk_level');
            $table->text('admin_notes')->nullable()->after('flagged_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['suspicious_flags', 'risk_score', 'risk_level', 'flagged_at', 'admin_notes']);
        });
    }
};
