<?php

namespace App\Services;

use App\Models\Overtime;
use App\Repositories\OvertimeFilingRepository;
use Illuminate\Database\Eloquent\Collection;

class OvertimeFilingService
{
    /**
     * @var App\Repositories\OvertimeFilingRepository $overtimeFilingRepository
     */
    private OvertimeFilingRepository $overtimeFilingRepository;

    public function __construct(OvertimeFilingRepository $overtimeFilingRepository)
    {
        $this->overtimeFilingRepository = $overtimeFilingRepository;
    }

    /**
     * @param array $params
     * 
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function get(array $params): Collection
    {
        return $this->overtimeFilingRepository->get($params);
    }

    /**
     * @param int $id
     * 
     * @return App\Models\Overtime
     */
    public function findById(int $id): Overtime
    {
        return $this->overtimeFilingRepository->findById($id);
    }
}
