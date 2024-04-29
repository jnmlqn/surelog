<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SssContribution extends Model
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
        'employer',
        'employee'
    ];

    public static function getDeduction(float $grosspay): float
    {
        return self::where('range_from', '<=', $grosspay)
            ->where('range_to', '>=', $grosspay)
            ->first()
            ->employee ?? 0;
    }
}
