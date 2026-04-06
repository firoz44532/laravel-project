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
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('gateway')->unique(); // bkash, nagad, rocket, bank_transfer
            $table->string('display_name');
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable(); // Store gateway-specific settings
            $table->text('instructions')->nullable(); // Payment instructions for customers
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Insert default payment settings
        DB::table('payment_settings')->insert([
            [
                'gateway' => 'bkash',
                'display_name' => 'bKash',
                'is_active' => true,
                'settings' => json_encode([
                    'merchant_number' => '',
                    'account_name' => '',
                    'transaction_fee' => 0,
                    'min_amount' => 0,
                    'max_amount' => 50000
                ]),
                'instructions' => '1. Go to your bKash mobile menu\n2. Select Send Money\n3. Enter the merchant number\n4. Enter the amount\n5. Enter your PIN\n6. Save the transaction ID',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'gateway' => 'nagad',
                'display_name' => 'Nagad',
                'is_active' => true,
                'settings' => json_encode([
                    'merchant_number' => '',
                    'account_name' => '',
                    'transaction_fee' => 0,
                    'min_amount' => 0,
                    'max_amount' => 50000
                ]),
                'instructions' => '1. Open Nagad app\n2. Tap Send Money\n3. Enter merchant number\n4. Enter amount\n5. Confirm with PIN\n6. Keep transaction ID',
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'gateway' => 'rocket',
                'display_name' => 'Rocket',
                'is_active' => true,
                'settings' => json_encode([
                    'merchant_number' => '',
                    'account_name' => '',
                    'transaction_fee' => 0,
                    'min_amount' => 0,
                    'max_amount' => 50000
                ]),
                'instructions' => '1. Dial *322# from your Rocket number\n2. Select Send Money\n3. Enter merchant number\n4. Enter amount\n5. Confirm with PIN\n6. Save transaction ID',
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'gateway' => 'bank_transfer',
                'display_name' => 'Bank Transfer',
                'is_active' => true,
                'settings' => json_encode([
                    'bank_name' => '',
                    'account_name' => '',
                    'account_number' => '',
                    'branch_name' => '',
                    'routing_number' => '',
                    'swift_code' => '',
                    'transaction_fee' => 0,
                    'min_amount' => 0,
                    'max_amount' => 100000
                ]),
                'instructions' => '1. Go to your bank or mobile banking\n2. Select Transfer/Payment\n3. Enter the account details\n4. Enter the amount\n5. Upload payment receipt',
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'gateway' => 'cash_on_delivery',
                'display_name' => 'Cash on Delivery',
                'is_active' => true,
                'settings' => json_encode([
                    'delivery_fee' => 50,
                    'min_amount' => 0,
                    'max_amount' => 20000
                ]),
                'instructions' => 'Pay when you receive your order. Please keep exact amount ready.',
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
