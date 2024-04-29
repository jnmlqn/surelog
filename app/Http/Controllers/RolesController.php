<?php

namespace App\Http\Controllers;

use App\Http\Requests\Roles\RolesCreateRequest;
use App\Http\Requests\Roles\RolesIndexRequest;
use App\Http\Requests\Roles\RolesUpdateRequest;
use App\Services\RoleService;
use App\Traits\ApiResponser;
use App\Traits\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RolesController extends Controller
{
    use ApiResponser;

    use AuditTrail;

    /**
     * @var App\Services\RoleService $roleService
     */
    private RoleService $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index(RolesIndexRequest $request): Response
    {
        $data = $request->validated();

        $roles = $this->roleService->getRoles([
            'keyword' => $data->keyword ?? null,
            'date' => $data->date ?? null,
            'sortBy' => $data->sortBy ?? 'created_at',
            'sorting' => $data->sorting ?? 'DESC',
            'limit' => $data->limit ?? 10
        ]);

        return $this->apiResponse(
            'Roles were successfully retrieved',
            $roles,
            200
        );
    }

    public function getModules(): Response
    {
        $modules = $this->roleService->getModules();

        return $this->apiResponse(
            'Modules were successfully retrieved',
            $modules,
            200
        );
    }

    public function show(int $id): Response
    {
        $role = $this->roleService->findRoleById($id);

        return $this->apiResponse(
            'Role permissions were successfully retrieved',
            $role,
            200
        );
    }

    public function store(RolesCreateRequest $request): Response
    {
        $data = $request->validated();

        $role = $this->roleService->createRole($data);

        $this->saveLogs(
            'Roles',
            $data,
            "Created a new role: {$data['name']}"
        );

        return $this->apiResponse(
            'Role was stored successfully',
            $role,
            201
        );
    }

    public function update(
        RolesUpdateRequest $request,
        int $id
    ): Response {
        $data = $request->validated();

        $role = $this->roleService->updateRole($id, $data);

        $this->saveLogs(
            'Roles',
            $data,
            "Updated a role: {$data['name']}"
        );

        return $this->apiResponse(
            'Role was updated successfully',
            null,
            200
        );
    }

    public function destroy(int $id): Response
    {
        $role = $this->roleService->findRoleById($id);

        if ($role->can_delete) {
            $role->delete();

            $this->saveLogs(
                'Roles',
                $id,
                "Deleted a role: {$role->name}"
            );

            return $this->apiResponse(
                'Role was deleted successfully',
                null,
                200
            );
        }

        return $this->apiResponse(
            'Unable to delete this role',
            null,
            500
        );
    }
}
