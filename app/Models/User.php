<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Laravel\Lumen\Auth\Authorizable;
use Laravel\Passport\HasApiTokens;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use HasApiTokens, Authenticatable, Authorizable, HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'employee_id',
        'first_name',
        'middle_name',
        'last_name',
        'extension',
        'email',
        'password',
        'birthday',
        'position',
        'image',
        'mobile',
        'tin',
        'sss_number',
        'pagibig_number',
        'philhealth_number',
        'rate',
        'taxable_allowance',
        'employment_type_id',
        'department_id',
        'civil_status_id',
        'role_id',
        'supervisor'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'id' => 'string',
        'employment_type_id' => 'integer',
        'department_id' => 'integer',
        'civil_status_id' => 'integer',
        'role_id' => 'integer'
    ];

    protected $appends = [
        'name'
    ];

    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($query) {
            $query->id = Str::uuid();
        });
    }

    public function getNameAttribute()
    {
        return "{$this->first_name} {$this->middle_name} {$this->last_name}";
    }

    public function address()
    {
        return $this->hasOne(UserAddress::class, 'user_id', 'id');
    }

    public function officeSchedule()
    {
        return $this->hasOne(OfficeSchedule::class, 'user_id', 'id');
    }

    public function departmentId()
    {
        return $this->hasOne(Department::class, 'id', 'department_id');
    }

    public function civilStatusId()
    {
        return $this->hasOne(CivilStatus::class, 'id', 'civil_status_id');
    }

    public function employmentTypeId()
    {
        return $this->hasOne(EmploymentType::class, 'id', 'employment_type_id');
    }

    public function supervisor()
    {
        return $this->hasOne(User::class, 'id', 'supervisor');
    }

    public function roleId()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }

    public function projectMember()
    {
        return $this->hasMany(ProjectMember::class, 'user_id', 'id')
                ->whereHas('projectId', function ($q) {
                    $q->where('status', 'Running')
                    ->where('end_date', '>=', date('Y-m-d'));
                });
    }

    public function projectAuthority()
    {
        return $this->hasMany(ProjectAuthority::class, 'user_id', 'id')
                ->whereHas('projectId', function ($q) {
                    $q->where('status', 'Running')
                    ->where('end_date', '>=', date('Y-m-d'));
                });
    }
}
