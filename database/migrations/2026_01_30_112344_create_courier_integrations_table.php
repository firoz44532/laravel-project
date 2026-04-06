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
        Schema::create('courier_integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->enum('courier_type', ['steadfast', 'pathao', 'ecourier', 'redx', 'paperfly', 'sundarban', 'saparibahan', 'janani']);
            $table->string('tracking_number')->nullable();
            $table->string('consignment_id')->nullable();
            $table->enum('status', ['pending', 'synced', 'failed', 'cancelled', 'delivered'])->default('pending');
            $table->text('pickup_address')->nullable();
            $table->text('delivery_address')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->decimal('package_weight', 8, 2)->default(0.50);
            $table->text('package_description')->nullable();
            $table->decimal('cod_amount', 10, 2)->default(0);
            $table->decimal('delivery_charge', 10, 2)->default(0);
            $table->json('api_response')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->index(['courier_type', 'status']);
            $table->index('tracking_number');
            $table->index('consignment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courier_integrations');
    }
};
