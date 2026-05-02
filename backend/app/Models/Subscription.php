<?php

namespace App\Models;

use App\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'school_id',
        'plan_id',
        'amount',
        'status',
        'start_date',
        'end_date',
        'trial_ends_at',
        'payment_method',
        'transaction_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'trial_ends_at' => 'datetime',
        ];
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->end_date > now();
    }

    public function isExpired(): bool
    {
        return $this->end_date <= now();
    }

    public function isTrial(): bool
    {
        return $this->trial_ends_at !== null && $this->trial_ends_at > now();
    }
}
