<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Merchant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_name',
        'store_slug',
        'store_description',
        'store_email',
        'store_phone',
        'store_address',
        'store_city',
        'store_country',
        'logo_url',
        'banner_url',
        'status',
        'commission_rate',
        'payment_details',
        'rejection_reason',
        'approved_at',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'payment_details' => 'array',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function activeProducts()
    {
        return $this->products()->where('is_active', true);
    }

    public function orders()
    {
        return Order::whereHas('items.product', function($query) {
            $query->where('merchant_id', $this->id);
        });
    }

    public function earnings()
    {
        return $this->orders()->where('status', 'completed');
    }

    public function getTotalRevenueAttribute()
    {
        return $this->earnings()->sum('total_amount');
    }

    public function getTotalCommissionAttribute()
    {
        $totalRevenue = $this->total_revenue;
        return $totalRevenue * ($this->commission_rate / 100);
    }

    public function getTotalEarningsAttribute()
    {
        return $this->total_revenue - $this->total_commission;
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isSuspended()
    {
        return $this->status === 'suspended';
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pending</span>',
            'approved' => '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Approved</span>',
            'rejected' => '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Rejected</span>',
            'suspended' => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Suspended</span>',
        ];

        return $badges[$this->status] ?? '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">' . $this->status . '</span>';
    }
}
