<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegisteredPhone extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'serial_number',
        'status'
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'user_id' => 'string',
        'name' => 'string',
        'serial_number' => 'string',
        'status' => 'integer'
    ];  
}
