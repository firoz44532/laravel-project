<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'first_name',
        'last_name',
        'is_active',
        'role',
        'is_merchant_approved',
        'email_verified_at',
        'suspicious_flags',
        'risk_score',
        'risk_level',
        'flagged_at',
        'admin_notes',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_merchant_approved' => 'boolean',
            'suspicious_flags' => 'array',
            'flagged_at' => 'datetime',
        ];
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function cart()
    {
        return $this->hasMany(Cart::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function getDefaultAddress()
    {
        return $this->addresses()->where('is_default', true)->first();
    }

    public function merchant()
    {
        return $this->hasOne(Merchant::class);
    }

    public function isMerchant()
    {
        return $this->role === 'merchant';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isCustomer()
    {
        return $this->role === 'customer';
    }

    public function isApprovedMerchant()
    {
        return $this->isMerchant() && $this->is_merchant_approved;
    }

    public function isSuspicious()
    {
        return $this->risk_score > 0;
    }

    public function isHighRisk()
    {
        return $this->risk_level === 'high';
    }

    public function isMediumRisk()
    {
        return $this->risk_level === 'medium';
    }

    public function isLowRisk()
    {
        return $this->risk_level === 'low';
    }

    public function needsReview()
    {
        return $this->risk_score >= 8 || !$this->is_active;
    }

    public function getRiskLevelColorAttribute()
    {
        return match($this->risk_level) {
            'high' => 'danger',
            'medium' => 'warning',
            'low' => 'success',
            default => 'secondary'
        };
    }

    public function getSuspiciousFlagsListAttribute()
    {
        if (!$this->suspicious_flags) {
            return [];
        }

        return collect($this->suspicious_flags)->map(function ($flag) {
            return [
                'flag' => $flag,
                'label' => $this->getFlagLabel($flag)
            ];
        });
    }

    private function getFlagLabel(string $flag): string
    {
        return match($flag) {
            'disposable_email_domain' => 'Disposable Email Domain',
            'numeric_email_prefix' => 'Numeric Email Prefix',
            'suspicious_email_prefix' => 'Suspicious Email Prefix',
            'random_email_pattern' => 'Random Email Pattern',
            'repeated_chars_email' => 'Repeated Characters in Email',
            'suspicious_phone_pattern' => 'Suspicious Phone Pattern',
            'obviously_fake_phone' => 'Obviously Fake Phone',
            'invalid_phone_length' => 'Invalid Phone Length',
            'suspicious_name_pattern' => 'Suspicious Name Pattern',
            'too_short_name' => 'Too Short Name',
            'numeric_name' => 'Numeric Name',
            'keyboard_pattern_name' => 'Keyboard Pattern Name',
            'multiple_registrations_same_ip' => 'Multiple Registrations from Same IP',
            'few_registrations_same_ip' => 'Few Registrations from Same IP',
            'recent_account_same_ip' => 'Recent Account from Same IP',
            'rapid_registration_attempts' => 'Rapid Registration Attempts',
            'high_failure_rate' => 'High Failure Rate',
            default => ucfirst(str_replace('_', ' ', $flag))
        };
    }
}
