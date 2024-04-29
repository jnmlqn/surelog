<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectPing extends Model
{
    protected $hidden = [
        'deleted_at'
    ];

    protected $fillable = [
        'project_id',
        'authority_id',
        'location',
        'created_by'
    ];

    public function project_id()
    {
        return $this->hasOne(Project::class, 'id', 'project_id');
    }

    public function authority_id()
    {
        return $this->hasOne(User::class, 'id', 'authority_id');
    }
}
