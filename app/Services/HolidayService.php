<?php

namespace App\Services;

use App\Repositories\HolidayRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class HolidayService
{
    /**
     * @var App\Repositories\HolidayRepository $holidayRepository
     */
    public HolidayRepository $holidayRepository;

    public function __construct(HolidayRepository $holidayRepository)
    {
        $this->holidayRepository = $holidayRepository;
    }

    /**
     * @param array $params
     * 
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    public function get(array $params): LengthAwarePaginator
    {
        return $this->holidayRepository->get($params);
    }

    /**
     * @param array $data
     * 
     * @return object
     */
    public function create(array $data): object
    {
        return $this->holidayRepository->create($data);
    }

    /**
     * @param int $id
     * 
     * @return object|null
     */
    public function findById(int $id): ?object
    {
        return $this->holidayRepository->findById($id);
    }

    /**
     * @param int $id
     * 
     * @return bool
     */
    public function deleteById(int $id): bool
    {
        return $this->holidayRepository->deleteById($id);
    }
}
