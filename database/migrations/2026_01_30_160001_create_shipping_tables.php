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
        Schema::create('shipping_zones', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // dhaka_metro, outside_dhaka, etc.
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('default_cost', 8, 2)->default(0);
            $table->decimal('express_cost', 8, 2)->default(0);
            $table->string('delivery_days')->nullable();
            $table->string('express_days')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('cities')->nullable(); // Array of cities in this zone
            $table->json('areas')->nullable(); // Array of specific areas
            $table->timestamps();
        });

        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // standard, express, pickup
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('estimated_days')->nullable();
            $table->decimal('base_cost', 8, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('tracking_available')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('settings')->nullable(); // Additional method-specific settings
            $table->timestamps();
        });

        Schema::create('shipping_zone_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_zone_id')->constrained()->onDelete('cascade');
            $table->foreignId('shipping_method_id')->constrained()->onDelete('cascade');
            $table->decimal('cost', 8, 2)->default(0);
            $table->decimal('additional_cost_per_kg', 8, 2)->default(0);
            $table->decimal('free_shipping_threshold', 8, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['shipping_zone_id', 'shipping_method_id']);
        });

        Schema::create('shipping_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, number, boolean, json
            $table->text('description')->nullable();
            $table->string('group')->default('general'); // general, tax, zones, methods
            $table->boolean('is_public')->default(false); // Whether setting affects frontend
            $table->timestamps();
        });

        // Insert default shipping settings
        $this->seedDefaultSettings();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_zone_rates');
        Schema::dropIfExists('shipping_settings');
        Schema::dropIfExists('shipping_methods');
        Schema::dropIfExists('shipping_zones');
    }

    /**
     * Seed default shipping settings.
     */
    private function seedDefaultSettings(): void
    {
        // Insert default shipping settings
        DB::table('shipping_settings')->insert([
            [
                'key' => 'default_shipping_cost',
                'value' => '50',
                'type' => 'number',
                'description' => 'Default shipping cost when no zone matches',
                'group' => 'general',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'free_shipping_threshold',
                'value' => '2000',
                'type' => 'number',
                'description' => 'Order amount for free shipping',
                'group' => 'general',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'tax_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable tax calculation',
                'group' => 'tax',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'vat_rate',
                'value' => '15',
                'type' => 'number',
                'description' => 'VAT rate percentage',
                'group' => 'tax',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'shipping_taxable',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Apply tax on shipping cost',
                'group' => 'tax',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'tax_inclusive',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Whether product prices include tax',
                'group' => 'tax',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'weight_based_enabled',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Enable weight-based shipping calculation',
                'group' => 'general',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'order_value_based_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable order value-based shipping calculation',
                'group' => 'general',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
};
