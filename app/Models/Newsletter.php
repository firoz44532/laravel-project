<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Newsletter extends Model
{
    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'is_active',
        'unsubscribe_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    protected $dates = [
        'subscribed_at',
        'unsubscribed_at',
        'created_at',
        'updated_at',
    ];

    public static function subscribe($email, $firstName = null, $lastName = null)
    {
        $newsletter = self::where('email', $email)->first();
        
        if ($newsletter) {
            // Reactivate if unsubscribed
            if (!$newsletter->is_active) {
                $newsletter->update([
                    'is_active' => true,
                    'unsubscribed_at' => null,
                    'unsubscribe_token' => null,
                ]);
            }
            return $newsletter;
        }

        // Create new subscription
        return self::create([
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'is_active' => true,
            'unsubscribe_token' => Str::random(40),
        ]);
    }

    public function unsubscribe($token = null)
    {
        if ($token && $this->unsubscribe_token !== $token) {
            return false;
        }

        $this->update([
            'is_active' => false,
            'unsubscribed_at' => now(),
        ]);

        return true;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getFormattedSubscribedAtAttribute()
    {
        return $this->subscribed_at->format('M j, Y H:i');
    }

    public function getFormattedUnsubscribedAtAttribute()
    {
        return $this->unsubscribed_at ? $this->unsubscribed_at->format('M j, Y H:i') : null;
    }
}
