<?php

namespace App\Models\Central;

use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $casts = [
        'subscription_active' => 'boolean',
        'subscription_starts_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'subscription_amount' => 'decimal:2',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'subscription_active',
            'subscription_starts_at',
            'subscription_ends_at',
            'subscription_amount',
            'subscription_package_id',
            'created_at',
            'updated_at',
        ];
    }
}
