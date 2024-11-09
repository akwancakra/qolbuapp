<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    /** @use HasFactory<\Database\Factories\IncomeFactory> */
    use HasFactory;

    protected $guarded = [];

    public function ambassador() {
        return $this->belongsTo(Ambassador::class);
    }

    public function scopeSearch($query, $term)
    {
        if ($term) {
            return $query->where('name', 'LIKE', '%' . $term . '%');
        }
    }

    public function scopeTeam($query, $team)
    {
        if ($team) {
            return $query->where('team', $team);
        }
    }

    public function scopeTransferDate($query, $date)
    {
        if ($date) {
            return $query->where('transfer_date', $date);
        }
    }

    public function scopeSortBy($query, $column = 'created_at', $direction = 'desc')
    {
        return $query->orderBy($column, $direction);
    }
}
