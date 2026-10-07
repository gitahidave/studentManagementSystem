<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    protected $fillable = [
        'name',
        'course_code',
        'duration',
        'status',
    ];

    public function getCourseNameAttribute(): string
    {
        return $this->name;
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'enrollments')
            ->withPivot(['semester_id', 'academic_year_id'])
            ->withTimestamps();
    }
}
