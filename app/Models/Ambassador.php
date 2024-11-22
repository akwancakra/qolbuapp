<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ambassador extends Model
{
    /** @use HasFactory<\Database\Factories\AmbassadorFactory> */
    use HasFactory;

    // protected $guarded = [];
    // protected $hidden = ['phone_number'];
    protected $fillable = ['name', 'phone_number', 'code'];

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public static function topTenByIncome()
    {
        return self::withSum('incomes', 'amount')
            ->orderByDesc('incomes_sum_amount')
            ->take(10)
            ->get();
    }
}
