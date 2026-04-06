<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Order;
use App\Models\Address;
use App\Models\User;

class TrackingSearchApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_api_returns_empty_when_no_query()
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($admin)->getJson(route('admin.tracking.search'));

        $response->assertStatus(200)
            ->assertJsonStructure(['orders'])
            ->assertJson(['orders' => []]);
    }

    public function test_search_api_finds_order_by_order_number()
    {
        // create user and shipping address (addresses.user_id is required)
        $user = User::factory()->create();

        $address = Address::create([
            'type' => 'shipping',
            'first_name' => 'Test',
            'last_name' => 'User',
            'phone' => '01700000000',
            'email' => 'test@example.com',
            'address_line_1' => 'Street 1',
            'city' => 'Dhaka',
            'division' => 'Dhaka',
            'country' => 'Bangladesh',
            'user_id' => $user->id,
        ]);

        $order = Order::create([
            'order_number' => 'ORDER12345',
            'status' => 'pending',
            'subtotal' => 100,
            'tax_amount' => 0,
            'shipping_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 100,
            'currency' => 'BDT',
            'shipping_address_id' => $address->id,
            'user_id' => $user->id,
        ]);

        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($admin)->getJson(route('admin.tracking.search', ['q' => 'ORDER12345']));

        $response->assertStatus(200)
            ->assertJsonStructure(['orders'])
            ->assertJsonCount(1, 'orders');
    }
}
