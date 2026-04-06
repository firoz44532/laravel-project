<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GuestCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_checkout_validation_errors_returned()
    {
        $response = $this->postJson(route('checkout.store'), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'shipping_first_name',
                'shipping_last_name',
                'shipping_phone',
                'shipping_address_line_1',
                'shipping_city',
                'shipping_division',
            ]);
    }
}
