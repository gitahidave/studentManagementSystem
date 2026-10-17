<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $attributes = [
        'status' => 'Active',
    ];

    protected $fillable = [
        'id',
        'first_name',
        'second_name',
        'course_id',
        'gender',
        'phone_number',
        'email',
        'status',
    ];

    protected static function booted(): void
    {
        static::created(function (Student $student): void {
            $student->forceFill([
                'student_number' => sprintf('STU-%06d', $student->id),
            ])->saveQuietly();
        });
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function fees(): HasMany
    {
        return $this->hasMany(Fee::class);
    }
}
