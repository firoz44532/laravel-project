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
        Schema::create('suspicious_customers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('name')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->text('reason')->nullable();
            $table->integer('fake_order_count')->default(0);
            $table->integer('cancelled_order_count')->default(0);
            $table->boolean('is_banned')->default(false);
            $table->timestamp('banned_until')->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('detection_method')->nullable(); // auto, manual, etc.
            $table->json('risk_factors')->nullable(); // JSON array of risk factors
            $table->decimal('risk_score', 5, 2)->default(0); // 0-100 risk score
            $table->timestamps();
            
            $table->index(['email', 'is_banned']);
            $table->index('ip_address');
            $table->index('risk_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suspicious_customers');
    }
};
