<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditTrail extends Model
{
    protected $fillable = [
        'user_id',
        'module',
        'data',
        'message'
    ];

    public function user_id()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    
}
