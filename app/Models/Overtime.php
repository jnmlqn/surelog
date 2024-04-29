<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Overtime extends Model
{
    use SoftDeletes;

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'user_id',
        'project_id',
        'time_in',
        'time_out',
        'approved_holiday_hours',
        'approved_ot_hours',
        'approved_night_ot_hours',
        'date',
        'approved_by',
        'declined_by',
        'created_by'
    ];

    protected $casts = [
        'user_id' => 'string',
        'project_id' => 'string',
        'approved_holiday_hours' => 'float',
        'approved_ot_hours' => 'float',
        'approved_by' => 'string',
        'declined_by' => 'string',
        'created_by' => 'string'
    ];

    protected $appends = [
        'total_mins'
    ];

    public function user_id()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function approved_by()
    {
        return $this->hasOne(User::class, 'id', 'approved_by');
    }

    public function declined_by()
    {
        return $this->hasOne(User::class, 'id', 'declined_by');
    }

    public function created_by()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function project_id()
    {
        return $this->hasOne(Project::class, 'id', 'project_id');
    }

    public function getTotalMinsAttribute()
    {
        $dt1 = date_create($this->time_out);
        $dt2 = date_create($this->time_in);
        $difference = date_diff($dt1, $dt2);
        $difference = ($difference->h * 60) + $difference->i;

        return $difference < 0
            ? 0
            : $difference;
    }
}
