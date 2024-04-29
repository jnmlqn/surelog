<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppVersion extends Model
{
    protected $fillable = [
        'platform',
        'version',
        'force_update'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $cast = [
        'version' => 'string',
    ];

    public function getForceUpdateAttribute($value)
    {
        if ($value == 0 || $value == '0') {
            return false;
        }

        return true;
    }
}
