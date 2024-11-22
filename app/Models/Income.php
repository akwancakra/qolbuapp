<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    /** @use HasFactory<\Database\Factories\IncomeFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ambassador_id',
        'transfer_date',
        'amount',
        'donor',
        'payment_method',
        'type',
        'on_behalf_of',
    ];

    public function ambassador()
    {
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
