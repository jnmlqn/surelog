<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectMember extends Model
{
    protected $fillable = [
        'project_id',
        'user_id',
    ];

    public $timestamps = false;

    public function user_id()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function projectId()
    {
        return $this->hasOne(Project::class, 'id', 'project_id');
    }
    
}
