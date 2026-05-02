<?php

namespace App;

use App\Models\School;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::creating(function ($model) {
            if (!$model->school_id) {
                // Priority 1: Subdomain middleware context
                if (config('app.tenant.school')) {
                    $model->school_id = config('app.tenant.school')->id;
                }
                // Priority 2: Authenticated user's school
                elseif (auth()->check() && auth()->user()->school_id) {
                    $model->school_id = auth()->user()->school_id;
                }
            }
        });

        static::addGlobalScope('tenant', function (Builder $builder) {
            // Skip scoping for super_admin users
            if (auth()->check() && auth()->user()->hasRole('super_admin')) {
                return;
            }

            $schoolId = null;

            // Check subdomain middleware context first
            if (config('app.tenant.school')) {
                $schoolId = config('app.tenant.school')->id;
            }
            // Fall back to authenticated user's school
            elseif (auth()->check() && auth()->user()->school_id) {
                $schoolId = auth()->user()->school_id;
            }

            if ($schoolId) {
                $builder->where('school_id', $schoolId);
            }
        });
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
