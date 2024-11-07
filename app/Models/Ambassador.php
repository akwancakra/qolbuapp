<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ambassador extends Model
{
    /** @use HasFactory<\Database\Factories\AmbassadorFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $hidden = ['phone_number'];

    public function revenues() {
        return $this->hasMany(Income::class);
    }
}
