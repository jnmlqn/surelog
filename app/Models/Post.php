<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Post extends Model
{
    use SoftDeletes;

    protected $hidden = [
        'deleted_at'
    ];

    protected $fillable = [
        'id',
        'post',
        'department_id',
        'project_id',
        'is_important',
        'created_by'
    ];

    public $incrementing = false;
    
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($query) {
            $query->id = Str::uuid();
        });
    }

    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function projectId()
    {
        return $this->hasOne(Project::class, 'id', 'project_id');
    }

    public function getCreatedAtAttribute($value) {
        return date('M d, Y h:i:s A', strtotime($value));
    }
}
