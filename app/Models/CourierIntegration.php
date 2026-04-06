<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourierIntegration extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'courier_type',
        'tracking_number',
        'consignment_id',
        'status',
        'pickup_address',
        'delivery_address',
        'customer_name',
        'customer_phone',
        'package_weight',
        'package_description',
        'cod_amount',
        'delivery_charge',
        'api_response',
        'error_message',
        'synced_at',
    ];

    protected $casts = [
        'cod_amount' => 'decimal:2',
        'delivery_charge' => 'decimal:2',
        'package_weight' => 'decimal:2',
        'api_response' => 'array',
        'synced_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pending</span>',
            'synced' => '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Synced</span>',
            'failed' => '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Failed</span>',
            'cancelled' => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Cancelled</span>',
            'delivered' => '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Delivered</span>',
            default => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">' . ucfirst($this->status) . '</span>'
        };
    }

    public function getCourierNameAttribute()
    {
        return match($this->courier_type) {
            'steadfast' => 'Steadfast Courier',
            'pathao' => 'Pathao Courier',
            'ecourier' => 'eCourier',
            'redx' => 'RedX',
            'paperfly' => 'Paperfly',
            'sundarban' => 'Sundarban Courier',
            'saparibahan' => 'SA Paribahan',
            'janani' => 'Janani Express',
            default => ucfirst($this->courier_type)
        };
    }
}
