<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function schools()
    {
        return $this->belongsToMany(School::class, 'school_module')
            ->withPivot('is_enabled')
            ->withTimestamps();
    }

    public function plans()
    {
        return $this->belongsToMany(Plan::class, 'plan_module');
    }
}
