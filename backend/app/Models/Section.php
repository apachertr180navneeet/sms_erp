<?php

namespace App\Models;

use App\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = ['class_id', 'name', 'capacity', 'room_number'];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
