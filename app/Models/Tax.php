<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tax extends Model
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
        'deduction',
        'percentage'
    ];

    protected $casts = [
        'range_from' => 'float',
        'range_to' => 'float',
        'deduction' => 'float',
        'percentage' => 'float'
    ];

    public static function getTax(float $income): float
    {
        $tax = self::where('range_from', '<=', $income)
            ->where('range_to', '>=', $income)
            ->first();

        $value = $income - ($tax->range_from ?? 0);
        $value = $value * (
            ($tax->percentage ?? null)
                ? $tax->percentage/100
                : 0
        );
        $value = $value + ($tax->deduction ?? 0);

        return $value;
    }
}
