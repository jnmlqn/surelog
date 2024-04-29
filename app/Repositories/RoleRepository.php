<?php

namespace App\Repositories;

use App\Models\Module;
use App\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleRepository
{
    /**
     * @var App\Models\Module $module
     */
    private Module $module;

    /**
     * @var App\Models\Role $role
     */
	private Role $role;

	public function __construct(
        Module $module,
        Role $role
    ) {
        $this->module = $module;
		$this->role = $role;
	}

    /**
     * @param array $params
     * 
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    public function getRoles(array $params): LengthAwarePaginator
    {
        return $this->role
            ->where('name', 'LIKE', "%{$params['keyword']}%")
            ->orderBy($params['sortBy'], $params['sorting'])
            ->paginate($params['limit']);
    }

    /**
     * @return object
     */
    public function getModules(): object
    {
        return $this->module
            ->orderBy('name', 'ASC')
            ->get();
    }

    /**
     * @param int $id
     * 
     * @return object
     */
    public function findRoleById(int $id): object
    {
        return $this->role->findOrFail($id);
    }

    /**
     * @param array $data
     * 
     * @return object
     */
    public function createRole(array $data): object
    {
        return $this->role->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * 
     * @return object
     */
    public function updateRole(
        int $id,
        array $data
    ): object {
        $role = $this->role->findOrFail($id);
        $role->fill($data)->save();
        return $role;
    }

    /**
     * @param int $id
     * 
     * @return bool
     */
    public function deleteRole(int $id): bool
    {
        return $this->role->where('id', $id)->delete();
    }
}
