<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPackage extends Model
{
    protected $guarded = [];

    protected $casts = [
        'features' => 'array',
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
