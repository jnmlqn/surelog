<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhilhealthContribution extends Model
{
    use SoftDeletes;

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'range_from',
        'range_to',
        'rate'
    ];

    public static function getDeduction(float $basicPay): float
    {
        $rate = self::where('range_from', '<=', $basicPay)
            ->where('range_to', '>=', $basicPay)
            ->first()
            ->rate ?? 0;

        return $basicPay * ($rate/100);
    }
}
