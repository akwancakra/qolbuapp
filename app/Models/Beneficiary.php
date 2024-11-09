<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = [
        'nik',
        'place_of_birth',
        'date_of_birth',
        'phone_number',
    ];

    public function scopeSearch($query, $term)
    {
        if ($term) {
            return $query->where('name', 'LIKE', ''%' . $term . '%'');
        }
    }

    public function scopeAge($query, $min_age, $max_age)
    {
        if ($min_age && $max_age) {
            return $query->where('birth_date', '>=', now()->subYears($max_age))
                ->where('birth_date', '<=', now()->subYears($min_age));
        }
    }

    public function scopeEducationLevel($query, $education_level)
    {
        if ($education_level) {
            return $query->where('education_level', $education_level);
        }
    }

    public function scopeSchoolGrade($query, $school_grade)
    {
        if ($school_grade) {
            return $query->where('school_grade', $school_grade);
        }
    }

    public function scopeStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
    }

    public function scopeSortBy($query, $column = 'created_at', $direction = 'desc')
    {
        return $query->orderBy($column, $direction);
    }
}
