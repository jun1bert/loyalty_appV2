<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name',
        'price',
        'is_package',
        'session_count',
        'discount_eligible',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_package' => 'boolean',
        'session_count' => 'integer',
        'discount_eligible' => 'boolean',
        'is_active' => 'boolean',
    ];
}
