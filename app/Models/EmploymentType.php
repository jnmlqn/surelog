<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmploymentType extends Model
{
    use SoftDeletes;

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'name',
        'regular_ot_rate',
        'legal_hol_rate',
        'legal_hol_ot_rate',
        'night_diff_rate',
        'restday_rate',
        'restday_ot_rate',
        'special_hol_rate',
        'special_hol_ot_rate'
    ];
}
