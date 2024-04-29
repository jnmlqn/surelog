<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAddress extends Model
{
    use SoftDeletes;

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'user_id',
        'address',
        'zipcode_id'
    ];

    public function zipcode_id()
    {
        return $this->hasOne(Zipcode::class, 'id', 'zipcode_id');
    }
}
