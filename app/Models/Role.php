<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'name',
        'permissions'
    ];

    protected $appends = [
        'access',
        'can_delete'
    ];

    protected $casts = [
        'permissions' => 'array'
    ];

    public function getAccessAttribute()
    {
        $permissions = $this->permissions;
        
        foreach($permissions as $key => $permission) {
            $module = Module::where('id', $permission['module_id'])->first();
            $permissions[$key]['module'] = $module->name;
            $permissions[$key]['slug'] = $module->slug;
        }

        return $permissions;
    }

    public function getCanDeleteAttribute()
    {
        $exist = User::where('role_id', $this->id)->first();
        
        if ($exist) {
            return false;
        }

        return true;
    }
}
