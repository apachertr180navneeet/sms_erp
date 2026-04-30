<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class CmsPage extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_published' => 'boolean',
    ];
}
