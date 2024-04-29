<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceLog extends Model
{
    use SoftDeletes;

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'project_id',
        'date',
        'locations',
        'dar',
        'user_id'
    ];

    protected $casts = [
        'project_id' => 'string',
        'locations' => 'array',
        'dar' => 'array',
        'user_id' => 'string'
    ];
}
