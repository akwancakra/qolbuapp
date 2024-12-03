<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    use HasFactory;

    // protected $guarded = [];

    // protected $hidden = [
    //     'nik',
    //     'place_of_birth',
    //     'date_of_birth',
    //     'phone_number',
    // ];

    protected $primaryKey = 'nik';

    protected $fillable = [
        'nik',
        'place_of_birth',
        'date_of_birth',
        'name',
        'neighborhood_unit',
        'gender',
        'last_education',
        'school_grade',
        'photo',
        'father',
        'father_photo',
        'mother',
        'mother_photo',
        'shirt_size',
        'shoe_size',
        'father_death_certificate_number',
        'mother_death_certificate_number',
        'phone_number',
        'status',
        'description'
    ];

    public function scopeSearch($query, $term)
    {
        if ($term) {
            return $query->where('name', 'LIKE', '%' . $term . '%');
        }
    }

    public function scopeAge($query, $min_age, $max_age)
    {
        if ($min_age && $max_age && $max_age !== 0) {
            return $query->where('date_of_birth', '>=', now()->subYears($max_age))
                ->where('date_of_birth', '<=', now()->subYears($min_age));
        } elseif ($min_age) {
            return $query->where('date_of_birth', '<=', now()->subYears($min_age));
        }
    }

    public function scopeLastEducation($query, $last_education)
    {
        if ($last_education && $last_education !== 'default') {
            return $query->where('last_education', $last_education);
        }
    }

    public function scopeSchoolGrade($query, $school_grade)
    {
        if ($school_grade && $school_grade !== 0) {
            return $query->where('school_grade', $school_grade);
        }
    }

    public function scopeStatus($query, $status)
    {
        if ($status && $status !== 'default') {
            return $query->where('status', $status);
        }
    }

    public function scopeShirt($query, $shirt)
    {
        if ($shirt && ($shirt !== 'default' || $shirt !== '')) {
            return $query->where('shirt_size', 'LIKE', '%' . $shirt . '%');
        }
    }

    public function scopeShoe($query, $shoe)
    {
        if ($shoe && ($shoe !== 'default' || $shoe != 0)) {
            return $query->where('shoe_size', $shoe);
        }
    }

    public function scopeSortBy($query, $column = 'created_at', $direction = 'desc')
    {
        return $query->orderBy($column, $direction);
    }

    public function scopeGender($query, $gender)
    {
        if ($gender && $gender !== 'default') {
            return $query->where('gender', $gender);
        }
    }
}
