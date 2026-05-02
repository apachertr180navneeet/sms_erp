<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use HasFactory, SoftDeletes;

    protected $appends = ['website_url'];

    protected $fillable = [
        'name',
        'slug',
        'code',
        'email',
        'phone',
        'address',
        'logo',
        'subdomain',
        'url',
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

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->latest('start_date');
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'school_module')
            ->withPivot('is_enabled')
            ->withTimestamps();
    }

    public function enabledModules()
    {
        return $this->modules()->wherePivot('is_enabled', true);
    }

    public function hasModule(string $slug): bool
    {
        return $this->modules()
            ->where('slug', $slug)
            ->wherePivot('is_enabled', true)
            ->exists();
    }

    public function isSubscriptionActive(): bool
    {
        $sub = $this->activeSubscription;
        return $sub !== null && $sub->isActive();
    }

    public function getStudentCount(): int
    {
        return $this->users()->whereHas('roles', function ($q) {
            $q->where('name', 'student');
        })->count();
    }

    public function hasExceededStudentLimit(): bool
    {
        if (!$this->plan || $this->plan->student_limit <= 0) {
            return false;
        }
        return $this->getStudentCount() >= $this->plan->student_limit;
    }

    public function getWebsiteUrlAttribute(): string
    {
        if ($this->url) {
            return $this->url;
        }
        if ($this->subdomain) {
            $baseUrl = parse_url(config('app.url'), PHP_URL_HOST);
            return "https://{$this->subdomain}.{$baseUrl}";
        }
        return config('app.url');
    }
}
