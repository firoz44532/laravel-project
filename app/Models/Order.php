<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'status',
        'subtotal',
        'tax_amount',
        'shipping_amount',
        'discount_amount',
        'total_amount',
        'currency',
        'notes',
        'user_id',
        'shipping_address_id',
        'billing_address_id',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shippingAddress()
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function billingAddress()
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function courierIntegrations()
    {
        return $this->hasMany(CourierIntegration::class);
    }

    public function getFormattedTotalAttribute()
    {
        return number_format($this->total_amount, 2) . ' ' . $this->currency;
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'paid' => 'blue',
            'processing' => 'purple',
            'shipped' => 'indigo',
            'delivered' => 'green',
            'cancelled' => 'red',
            'refunded' => 'gray',
            default => 'gray'
        };
    }

    public function getTrackingHistory()
    {
        $history = [];
        
        // Add order creation
        $history[] = [
            'date' => $this->created_at->format('M j, Y H:i'),
            'status' => 'Order Placed',
            'description' => 'Your order has been placed successfully.',
            'icon' => 'fas fa-shopping-cart',
            'color' => 'blue'
        ];

        // Add payment confirmation
        if ($this->status !== 'pending') {
            $history[] = [
                'date' => $this->created_at->addHours(1)->format('M j, Y H:i'),
                'status' => 'Payment Confirmed',
                'description' => 'Payment has been confirmed successfully.',
                'icon' => 'fas fa-credit-card',
                'color' => 'green'
            ];
        }

        // Add processing status
        if (in_array($this->status, ['processing', 'shipped', 'delivered'])) {
            $history[] = [
                'date' => $this->created_at->addHours(2)->format('M j, Y H:i'),
                'status' => 'Order Processing',
                'description' => 'Your order is being prepared for shipment.',
                'icon' => 'fas fa-box',
                'color' => 'yellow'
            ];
        }

        // Add shipped status
        if (in_array($this->status, ['shipped', 'delivered'])) {
            $history[] = [
                'date' => $this->created_at->addDays(1)->format('M j, Y H:i'),
                'status' => 'Order Shipped',
                'description' => 'Your order has been shipped and is on its way.',
                'icon' => 'fas fa-truck',
                'color' => 'purple'
            ];
        }

        // Add delivered status
        if ($this->status === 'delivered') {
            $history[] = [
                'date' => $this->updated_at->format('M j, Y H:i'),
                'status' => 'Order Delivered',
                'description' => 'Your order has been delivered successfully.',
                'icon' => 'fas fa-check-circle',
                'color' => 'green'
            ];
        }

        // Add cancelled status
        if ($this->status === 'cancelled') {
            $history[] = [
                'date' => $this->updated_at->format('M j, Y H:i'),
                'status' => 'Order Cancelled',
                'description' => 'Your order has been cancelled.',
                'icon' => 'fas fa-times-circle',
                'color' => 'red'
            ];
        }

        return $history;
    }

    public function getEstimatedDelivery()
    {
        // Calculate estimated delivery based on order status and creation date
        $createdAt = $this->created_at;
        
        switch ($this->status) {
            case 'pending':
                return 'Processing order...';
            case 'paid':
                return 'Preparing for shipment (1-2 days)';
            case 'processing':
                return 'Shipped within 24 hours';
            case 'shipped':
                $deliveryDate = $createdAt->addDays(3);
                return 'Delivered by ' . $deliveryDate->format('M j, Y');
            case 'delivered':
                return 'Delivered on ' . $this->updated_at->format('M j, Y');
            case 'cancelled':
                return 'Order cancelled';
            case 'refunded':
                return 'Order refunded';
            default:
                return 'Processing order...';
        }
    }

    public function getItemsCountAttribute()
    {
        return $this->items->count();
    }
}
