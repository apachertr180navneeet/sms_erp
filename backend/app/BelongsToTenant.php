<?php

namespace App;

use App\Models\School;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::creating(function ($model) {
            if (auth()->check()) {
                $user = auth()->user();
                if ($user->school_id && !$model->school_id) {
                    $model->school_id = $user->school_id;
                }
            }
        });

        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->check() && auth()->user()->school_id) {
                $builder->where('school_id', auth()->user()->school_id);
            }
        });
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
