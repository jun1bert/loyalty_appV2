<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    protected $fillable = [
        'code',
        'name',
        'discount_type',
        'discount_value',
        'starts_at',
        'expires_at',
        'usage_limit',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'starts_at' => 'date',
        'expires_at' => 'date',
        'is_active' => 'boolean',
    ];

    public function transactions()
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public function isAvailable(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->endOfDay()->isPast()) {
            return false;
        }

        if ($this->usage_limit !== null && $this->transactions()->count() >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function discountFor(float $amount): float
    {
        if ($this->discount_type === 'fixed') {
            return min((float) $this->discount_value, $amount);
        }

        return $amount * ((float) $this->discount_value / 100);
    }
}
