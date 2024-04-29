<?php

namespace App\Services;

use App\Repositories\RoleRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleService
{
    /**
     * @var App\Repositories\RoleRepository $roleRepository
     */
    private RoleRepository $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * @param array $params
     * 
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    public function getRoles(array $params): LengthAwarePaginator
    {
        return $this->roleRepository->getRoles($params);
    }

    /**
     * @return object
     */
    public function getModules(): object
    {
        return $this->roleRepository->getModules();
    }

    /**
     * @param int $id
     * 
     * @return object
     */
    public function findRoleById(int $id): object
    {
        return $this->roleRepository->findRoleById($id);
    }

    /**
     * @param array $data
     * 
     * @return object
     */
    public function createRole(array $data): object
    {
        return $this->roleRepository->createRole($data);
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
        return $this->roleRepository
            ->updateRole(
                $id,
                $data
            );
    }

    /**
     * @param int $id
     * 
     * @return bool
     */
    public function deleteRole(int $id): bool
    {
        return $this->roleRepository->deleteRole($id);
    }
}
