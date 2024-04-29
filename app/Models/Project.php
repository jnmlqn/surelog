<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Project extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'description',
        'location',
        'start_date',
        'end_date',
        'department_id',
        'offset',
        'status',
        'project_schedules'
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'id' => 'string',
        'employment_type_id' => 'integer',
        'department_id' => 'integer',
        'offset' => 'float',
        'project_schedules' => 'array'
    ];

    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($query) {
            $query->id = Str::uuid();
        });
    }

    public function department_id()
    {
        return $this->hasOne(Department::class, 'id', 'department_id');
    }

    public function project_authorities()
    {
        return $this->hasMany(ProjectAuthority::class, 'project_id', 'id');
    }

    public function project_members()
    {
        return $this->hasMany(ProjectMember::class, 'project_id', 'id');
    }

    public function getStatusAttribute($value)
    {
        $today = date('Y-m-d');
        if ($today > $this->end_date) {
            return 'Ended';
        }
        return $value;
    }
}
