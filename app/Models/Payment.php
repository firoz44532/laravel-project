<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'transaction_id',
        'order_id',
        'method',
        'status',
        'amount',
        'currency',
        'phone_number',
        'account_number',
        'card_last_four',
        'gateway_transaction_id',
        'gateway_response',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'paid_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getMethodDisplayNameAttribute()
    {
        $methods = [
            'bkash' => 'bKash',
            'nagad' => 'Nagad',
            'rocket' => 'Rocket',
            'upay' => 'Upay',
            'card' => 'Credit/Debit Card',
            'cash_on_delivery' => 'Cash on Delivery',
        ];

        return $methods[$this->method] ?? $this->method;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pending</span>',
            'processing' => '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Processing</span>',
            'completed' => '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span>',
            'failed' => '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Failed</span>',
            'cancelled' => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Cancelled</span>',
        ];

        return $badges[$this->status] ?? '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">' . $this->status . '</span>';
    }

    public function isPaid()
    {
        return in_array($this->status, ['completed']);
    }

    public function isPending()
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function isFailed()
    {
        return in_array($this->status, ['failed', 'cancelled']);
    }
}
