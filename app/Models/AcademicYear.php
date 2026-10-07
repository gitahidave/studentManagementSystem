<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    protected $fillable = ['name'];

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }
}
