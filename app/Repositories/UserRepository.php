<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserAddress;
use App\Models\OfficeSchedule;

class UserRepository
{
    /**
     * @var App\Models\User $user
     */
	public User $user;

    /**
     * @var App\Models\UserAddress $userAddress
     */
    public UserAddress $userAddress;
    
    /**
     * @var App\Models\OfficeSchedule $officeSchedule
     */
    public OfficeSchedule $officeSchedule;

	public function __construct(
        User $user,
        UserAddress $userAddress,
        OfficeSchedule $officeSchedule
    ) {
		$this->user = $user;
        $this->userAddress = $userAddress;
        $this->officeSchedule = $officeSchedule;
	}

    /**
     * @param array $sort
     * @param int|null $departmentId
     * @param string|null $projectId
     * @param string|null $employeeId
     * 
     * @return object
     */
    public function getEmployeesByDepartmentIdandProjectId(
    	array $sort,
    	?int $departmentId = null,
    	?string $projectId = null,
        ?string $employeeId = null
    ): object {
    	return $this->user
            ->when(!empty($departmentId), function ($q) use ($departmentId) {
	            $q->where('department_id', $departmentId);
	        })
	        ->with('employmentTypeId')
	        ->select(
	            'users.id',
	            'users.employee_id',
	            'users.first_name',
	            'users.middle_name',
	            'users.last_name',
	            'users.extension',
	            'users.position',
	            'users.image',
	            'users.tin',
	            'users.sss_number',
	            'users.pagibig_number',
	            'users.philhealth_number',
	            'users.rate',
	            'users.employment_type_id',
	            'd.name as department'
	        )
	        ->leftJoin('departments as d', 'd.id', 'users.department_id')
	        ->orderBy($sort['sortBy'], $sort['sorting'])
            ->when(!empty($projectId), function ($q) use ($projectId) {
                $q->whereHas('projectMember', function ($q) use ($projectId) {
                    $q->where('project_id', $projectId);
                })
                ->orWhereHas('projectAuthority', function ($q) use ($projectId) {
                    $q->where('project_id', $projectId);
                });
            })
            ->when(!empty($employeeId), function ($q) use ($employeeId) {
                $q->where('users.id', $employeeId);
            })
	        ->get();
    }

    /**
     * @param string $id
     * @param array $details
     * 
     * @return object
     */
    public function findById(
        string $id,
        array $details = []
    ): object {
    	$user = $this->user;

    	if (!empty($details)) {
    		$user = $user->with($details);
    	}

    	return $user->findOrFail($id);
    }

    /**
     * @param string $email
     * @param array $details
     * 
     * @return object
     */
    public function findByEmail(
        string $email,
        array $details = []
    ): object {
    	$user = $this->user;

    	if (!empty($details)) {
    		$user = $user->with($details);
    	}

    	return $user->where('email', $email)->first();
    }

    /**
     * @param array $params
     * 
     * @return object
     */
    public function searchUser(array $params): object
    {
    	$users = $this->user
            ->with([
                'departmentId',
                'civilStatusId',
                'roleId',
                'employmentTypeId:id,name'
            ])
            ->where(function ($q) use ($params) {
                if (!empty($params['keyword'])) {
                    $q->where(function ($q) use ($params) {
                        $q->where('first_name', 'LIKE', "%{$params['keyword']}%")
                        ->orWhere('middle_name', 'LIKE', "%{$params['keyword']}%")
                        ->orWhere('last_name', 'LIKE', "%{$params['keyword']}%")
                        ->orWhere('email', 'LIKE', "%{$params['keyword']}%");
                    });
                }

                if (!empty($params['departmentId'])) {
                    $q->where('department_id', $params['departmentId']);
                }

                if (!empty($params['roleId'])) {
                    $q->where('role_id', $params['roleId']);
                }
            })
            ->orderBy($params['sortBy'], $params['sorting']);

        $users = $params['get']
            ? $users->limit($params['limit'])->get()
            : $users->paginate($params['limit']);

        return $users;
    }

    /**
     * @param array $data
     * 
     * @return object
     */
    public function createUser(array $data): object
    {
        return $this->user->create($data);
    }

    /**
     * @param array $data
     * 
     * @return object
     */
    public function createUserAddress(array $data): object
    {
        return $this->userAddress->create($data);
    }

    /**
     * @param array $data
     * 
     * @return object
     */
    public function createOfficeSchedule(array $data): object
    {
        return $this->officeSchedule->create($data);
    }
}
