<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\ProjectAuthority;
use App\Models\ProjectMember;
use App\Models\ProjectPing;

class ProjectRepository
{
    /**
     * @var App\Models\Project $project
     */
	private Project $project;

    /**
     * @var App\Models\ProjectAuthority $projectAuthority
     */
    private ProjectAuthority $projectAuthority;

    /**
     * @var App\Models\ProjectMember $projectMember
     */
    private ProjectMember $projectMember;

    /**
     * @var App\Models\ProjectPing $projectPing
     */
    private ProjectPing $projectPing;

	public function __construct(
        Project $project,
        ProjectAuthority $projectAuthority,
        ProjectMember $projectMember,
        ProjectPing $projectPing
    ) {
		$this->project = $project;
        $this->projectAuthority = $projectAuthority;
        $this->projectMember = $projectMember;
        $this->projectPing = $projectPing;
	}

    /**
     * @param array $params
     * 
     * @return object
     */
    public function get(array $params): object
    {
        $projects = $this->project
            ->with([
                'department_id',
            ])
            ->where(function ($q) use ($params) {
                if ($params['keyword']) {
                    $q->where(function ($q) use ($params) {
                        $q->where('name', 'LIKE', "%{$params['keyword']}%")
                        ->orWhere('description', 'LIKE', "%{$params['keyword']}%")
                        ->orWhere('location', 'LIKE', "%{$params['keyword']}%");
                    });
                }

                if ($params['departmentId']) {
                    $q->where('department_id', $params['departmentId']);
                }
            })
            ->orderBy($params['sortBy'], $params['sorting']);

        $projects = $params['get']
            ? $projects->limit($params['limit'])->get()
            : $projects->paginate($params['limit']);

        return $projects;
    }

    /**
     * @param string $id
     * @param array relationships
     * 
     * @return object 
     */
    public function getByIdWithRelationship(
        string $id,
        array $relationships = []
    ): object {
        return $this->project
            ->with($relationships)
            ->findOrFail($id);
    }

    /**
     * @param array $data
     * 
     * @return object
     */
    public function create(array $data): object
    {
        return $this->project->create($data);
    }

    /**
     * @param string $id
     * @param array $data
     * 
     * @return object
     */
    public function update(
        string $id,
        array $data
    ): object {
        $project = $this->project->findOrFail($id);
        $project->fill($data)->save();
        $project->project_authorities()->delete();
        $project->project_members()->delete();

        return $project;
    }

    /**
     * @param string $id
     * 
     * @return bool
     */
    public function delete(string $id)
    {
        return $this->project->where('id', $id)->delete();
    }

    /**
     * @param array $data
     * 
     * @return object
     */
    public function saveProjectAuthority(array $data): object
    {
        return $this->projectAuthority->create($data);
    }

    /**
     * @param array $data
     * 
     * @return object
     */
    public function saveProjectMember(array $data): object
    {
        return $this->projectMember->create($data);
    }

    /**
     * @param string $userId
     * 
     * @return object
     */
    public function myprojects(string $userId): object
    {
        return $this->project
            ->with([
                'project_authorities.user_id:id,first_name,middle_name,last_name,extension',
                'project_members.user_id:id,first_name,middle_name,last_name,extension',
            ])
            ->whereHas('project_authorities', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->get();
    }

    /**
     * @param string $id
     * 
     * @return object
     */
    public function getProjectAuthorities(string $id): object
    {
        return $this->projectAuthority
            ->where('project_id', $id)
            ->with('user_id:id,first_name,middle_name,last_name,extension')
            ->get();
    }

    /**
     * @param string $id
     * @param string $userId
     * 
     * @return object|null
     */
    public function getLatestPing(
        string $id,
        string $userId
    ): ?object {
        return $this->projectPing
            ->where('project_id', $id)
            ->where('authority_id', $userId)
            ->latest()
            ->first();
    }

    /**
     * @param array $data
     * 
     * @return object
     */
    public function createProjectPing(array $data): object
    {
        return $this->projectPing->create($data);
    }
}
