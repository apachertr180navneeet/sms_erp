<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'student_limit',
        'staff_limit',
        'storage_limit_mb',
        'billing_cycle',
        'features',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'features' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function schools()
    {
        return $this->hasMany(School::class);
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'plan_module');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
