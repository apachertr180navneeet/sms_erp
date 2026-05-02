<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'code',
        'email',
        'phone',
        'address',
        'logo',
        'subdomain',
        'is_active',
        'plan_id',
        'subscription_ends_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'subscription_ends_at' => 'datetime',
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
