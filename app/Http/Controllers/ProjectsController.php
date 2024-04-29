<?php

namespace App\Http\Controllers;

use App\Http\Requests\Projects\ProjectsCreateRequest;
use App\Http\Requests\Projects\ProjectsIndexRequest;
use App\Http\Requests\Projects\ProjectLocationsCreateRequest;
use App\Http\Requests\Projects\ProjectsUpdateRequest;
use App\Services\ProjectService;
use App\Services\UserService;
use App\Traits\ApiResponser;
use App\Traits\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Illuminate\Support\Str;

class ProjectsController extends Controller
{
    use ApiResponser;
    use AuditTrail;

    /**
     * @var App\Service\UserService $userService
     */
    private UserService $userService;

    /**
     * @var App\Service\ProjectService $projectService
     */
    private ProjectService $projectService;

    public function __construct(
        UserService $userService,
        ProjectService $projectService
    ) {
        $this->userService = $userService;
        $this->projectService = $projectService;
    }

    /**
     * @var App\Http\Requests\Projects\ProjectsIndexRequest $request
     * 
     * @return Illuminate\Http\Response
     */
    public function index(ProjectsIndexRequest $request): Response
    {
        $data = $request->validated();

        $projects = $this->projectService
            ->get([
                'keyword' => $data['keyword'] ?? null,
                'departmentId' => $data['department_id'] ?? null,
                'sortBy' => $data['sort_by'] ?? 'created_at',
                'sorting' => $data['sorting'] ?? 'DESC',
                'limit' => $data['limit'] ?? 10,
                'get' => $data['get'] ?? false
            ]);

        return $this->apiResponse(
            'Projects were successfully retrieved',
            $projects,
            200
        );
    }

    /**
     * @var string $id
     * 
     * @return Illuminate\Http\Response
     */
    public function show(string $id): Response
    {
        $project = $this->projectService
            ->getByIdWithRelationship(
                $id,
                [
                    'project_authorities',
                    'project_members'
                ]
            );

        return $this->apiResponse(
            'Project details were successfully retrieved',
            $project,
            200
        );
    }

    /**
     * @var App\Http\Requests\Projects\ProjectsCreateRequest $request
     * 
     * @return Illuminate\Http\Response
     */
    public function store(ProjectsCreateRequest $request): Response
    {
        $data = $request->validated();
        
        $project = $this->projectService->create($data);

        $this->saveProjectMembers($project->id, $data);

        $this->saveLogs(
            'Projects',
            $data,
            'Created a project'
        );

        return $this->apiResponse(
            'Project was stored successfully',
            $project,
            201
        );
    }

    /**
     * @var App\Http\Requests\Projects\ProjectsUpdateRequest $request
     * @var string $id
     * 
     * @return Illuminate\Http\Response
     */
    public function update(
        ProjectsUpdateRequest $request,
        string $id
    ): Response {
        $data = $request->validated();

        $project = $this->projectService->update($id, $data);

        $this->saveProjectMembers($id, $data);

        $this->saveLogs(
            'Projects',
            $data,
            'Updated the project details'
        );

        return $this->apiResponse(
            'Project details were updated successfully',
            $project,
            200
        );
    }

    /**
     * @var string $id
     * 
     * @return Illuminate\Http\Response
     */
    public function destroy(string $id): Response
    {
        $project = $this->projectService->delete($id);

        if (!$project) {
            return $this->apiResponse(
                'Project was not found',
                null,
                404
            );
        }

        $this->saveLogs(
            'Projects',
            $id,
            'Deleted a project'
        );

        return $this->apiResponse(
            'Project was deleted successfully',
            null,
            200
        );
    }

    /**
     * @var string $pid
     * @var array $data
     * 
     * @return void
     */
    public function saveProjectMembers(
        string $pid,
        array $data
    ): void {
        foreach($data['project_authorities'] as $authority) {
            $exist = $this->userService->findById($authority);
            if ($exist) {
                $this->projectService
                    ->saveProjectAuthority([
                        'project_id' => $pid,
                        'user_id' => $authority
                    ]);
            }
        }

        foreach($data['project_members'] as $member) {
            $exist = $this->userService->findById($member);
            if ($exist) {
                $this->projectService
                    ->saveProjectMember([
                        'project_id' => $pid,
                        'user_id' => $authority
                    ]);
            }
        }
    }

    /**
     * @return Illuminate\Http\Response
     */
    public function myProjects(): Response
    {
        $user = config('user');

        $projects = $this->projectService->myprojects($user['id']);

        return $this->apiResponse(
            'Projects were successfully retrieved',
            $projects,
            200
        );
    }

    /**
     * @param string $id
     * 
     * @return Illuminate\Http\Response
     */
    public function getProjectAuthorities(string $id): Response
    {
        $authorities = $this->projectService->getProjectAuthorities($id);

        foreach ($authorities as $key => $value) {
            $value['location'] = null;
            $value['created_at'] = null;

            $ping = $this->projectService->getLatestPing($id, $value->user_id);

            if ($ping) {
                $value['location'] = $ping->location ? "https://google.com/maps/place/{$ping->location}" : null;
                $value['created_at'] = $ping->created_at ?? null;
            }
        }

        return $this->apiResponse(
            'Project authorities were successfully retrieved',
            $authorities,
            200
        );
    }

    /**
     * @var App\Http\Requests\Projects\ProjectLocationsCreateRequest $request
     * 
     * @return Illuminate\Http\Response
     */
    public function saveLocationRequest(ProjectLocationsCreateRequest $request): Response
    {
        $user = config('user');
        $data = $request->validated();
        $data['created_by'] = $user['id'];

        $location = $this->projectService->createProjectPing($data);

        $location->location = "https://google.com/maps/place/{$location->location}";

        return $this->apiResponse('Location request successfully saved', $location, 200);
    }
}
