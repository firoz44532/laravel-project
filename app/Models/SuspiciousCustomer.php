<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SuspiciousCustomer extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'phone',
        'name',
        'ip_address',
        'reason',
        'fake_order_count',
        'cancelled_order_count',
        'is_banned',
        'banned_until',
        'admin_notes',
        'detection_method',
        'risk_factors',
        'risk_score',
    ];

    protected $casts = [
        'is_banned' => 'boolean',
        'banned_until' => 'datetime',
        'risk_factors' => 'array',
        'risk_score' => 'decimal:2',
    ];

    // Scopes
    public function scopeBanned($query)
    {
        return $query->where('is_banned', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_banned', false);
    }

    public function scopeHighRisk($query, $threshold = 70)
    {
        return $query->where('risk_score', '>=', $threshold);
    }

    public function scopeMediumRisk($query, $min = 40, $max = 69)
    {
        return $query->whereBetween('risk_score', [$min, $max]);
    }

    // Methods
    public function ban($reason = null, $bannedUntil = null)
    {
        $this->update([
            'is_banned' => true,
            'banned_until' => $bannedUntil,
            'reason' => $reason ?? $this->reason,
        ]);
    }

    public function unban()
    {
        $this->update([
            'is_banned' => false,
            'banned_until' => null,
        ]);
    }

    public function isCurrentlyBanned()
    {
        if (!$this->is_banned) {
            return false;
        }
        
        if ($this->banned_until && $this->banned_until->isPast()) {
            $this->unban();
            return false;
        }
        
        return true;
    }

    public function increaseRiskScore($points, $factor = null)
    {
        $this->risk_score = min(100, $this->risk_score + $points);
        
        if ($factor) {
            $riskFactors = $this->risk_factors ?? [];
            if (!in_array($factor, $riskFactors)) {
                $riskFactors[] = $factor;
                $this->risk_factors = $riskFactors;
            }
        }
        
        $this->save();
    }

    public function addFakeOrder()
    {
        $this->increment('fake_order_count');
        $this->increaseRiskScore(15, 'fake_order');
    }

    public function addCancelledOrder()
    {
        $this->increment('cancelled_order_count');
        $this->increaseRiskScore(5, 'cancelled_order');
    }

    public function getRiskLevelAttribute()
    {
        if ($this->risk_score >= 70) return 'high';
        if ($this->risk_score >= 40) return 'medium';
        return 'low';
    }

    public function getRiskLevelColorAttribute()
    {
        return match($this->risk_level) {
            'high' => 'red',
            'medium' => 'yellow', 
            'low' => 'green',
            default => 'gray'
        };
    }
}
