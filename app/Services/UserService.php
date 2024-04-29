<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{

    private UserRepository $userRepository;

    public function user()
    {
        return $this->userRepository->user;
    }

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
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
        return $this->userRepository->getEmployeesByDepartmentIdandProjectId($sort, $departmentId, $projectId, $employeeId);
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
        return $this->userRepository->findById($id, $details);
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
        return $this->userRepository->findByEmail($email, $details);
    }

    /**
     * @param array $params
     * 
     * @return object
     */
    public function searchUser(array $params): object
    {
        return $this->userRepository->searchUser($params);
    }
    /**
     * @param array $data
     * 
     * @return object
     */
    public function createUser(array $data): object
    {
        return $this->userRepository->createUser($data);
    }
    /**
     * @param array $data
     * 
     * @return object
     */
    public function createUserAddress(array $data): object
    {
        return $this->userRepository->createUserAddress($data);
    }

    /**
     * @param array $data
     * 
     * @return object
     */
    public function createOfficeSchedule(array $data): object
    {
        return $this->userRepository->createOfficeSchedule($data);
    }
}
