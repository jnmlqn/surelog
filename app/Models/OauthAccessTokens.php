<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OauthAccessTokens extends Model
{
    protected $casts = [
    	'id' => 'string',
        'revoked' => 'integer',
        'user_id' => 'string'
    ];
}
