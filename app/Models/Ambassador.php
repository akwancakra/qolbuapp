<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ambassador extends Model
{
    /** @use HasFactory<\Database\Factories\AmbassadorFactory> */
    use HasFactory;

    public function revenues() {
        return $this->hasMany(Income::class);
    }
}
