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
}
