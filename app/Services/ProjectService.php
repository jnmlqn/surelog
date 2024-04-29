<?php

namespace App\Services;

use App\Repositories\ProjectRepository;

class ProjectService
{
    /**
     * @var App\Repositories\ProjectRepository $projectRepository
     */
    private ProjectRepository $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    /**
     * @param array $params
     * 
     * @return object
     */
    public function get(array $params): object
    {
        return $this->projectRepository->get($params);
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
        return $this->projectRepository
            ->getByIdWithRelationship(
                $id,
                $relationships
            );
    }

    /**
     * @param array $data
     * 
     * @return object
     */
    public function create(array $data): object
    {
        return $this->projectRepository->create($data);
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
        return $this->projectRepository
            ->update(
                $id,
                $data
            );
    }

    /**
     * @param string $id
     * 
     * @return bool
     */
    public function delete(string $id): bool
    {
        return $this->projectRepository->delete($id);
    }

    /**
     * @param array $data
     * 
     * @return object
     */
    public function saveProjectAuthority(array $data): object
    {
        return $this->projectRepository->saveProjectAuthority($data);
    }

    /**
     * @param array $data
     * 
     * @return object
     */
    public function saveProjectMember(array $data): object
    {
        return $this->projectRepository->saveProjectMember($data);
    }

    /**
     * @param string $userId
     * 
     * @return object
     */
    public function myprojects(string $userId): object
    {
        return $this->projectRepository->myprojects($userId);
    }

    /**
     * @param string $id
     * 
     * @return object
     */
    public function getProjectAuthorities(string $id): object
    {
        return $this->projectRepository->getProjectAuthorities($id);
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
        return $this->projectRepository->getLatestPing($id, $userId);
    }

    /**
     * @param array $data
     * 
     * @return object
     */
    public function createProjectPing(array $data): object
    {
        return $this->projectRepository->createProjectPing($data);
    }
}
