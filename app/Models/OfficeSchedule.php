<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfficeSchedule extends Model
{
    use SoftDeletes;

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'user_id',
        'monday_in',
        'monday_out',
        'tuesday_in',
        'tuesday_out',
        'wednesday_in',
        'wednesday_out',
        'thursday_in',
        'thursday_out',
        'friday_in',
        'friday_out',
        'saturday_in',
        'saturday_out',
        'sunday_in',
        'sunday_out'
    ];

    public function user_id()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
