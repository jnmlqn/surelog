<?php

namespace App\Services;

use App\Repositories\HrDataRepository;
use Illuminate\Database\Eloquent\Collection;

class HrDataService
{
    /**
     * @var App\Repositories\HrDataRepository $hrDataRepository
     */
    private HrDataRepository $hrDataRepository;

    public function __construct(HrDataRepository $hrDataRepository)
    {
        $this->hrDataRepository = $hrDataRepository;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        return [
            'departments' => $this->hrDataRepository->getData('departments'),
            'employment_types' => $this->hrDataRepository->getData('employmentTypes'),
            'civil_statuses' => $this->hrDataRepository->getData('civilStatuses'),
            'sss_contributions' => $this->hrDataRepository->getData('sssContributions'),
            'pagibig_contributions' => $this->hrDataRepository->getData('pagibigContributions'),
            'philhealth_contributions' => $this->hrDataRepository->getData('philhealthContributions'),
            'taxes' => $this->hrDataRepository->getData('taxes')
        ];
    }

    /**
     * @param string $model
     * @param array $condition
     * @param array $data
     */
    public function update(
        string $model,
        array $condition,
        array $data
    ): object {
        return $this->hrDataRepository
            ->update(
                $model,
                $condition,
                $data
            );
    }

    /**
     * @param string $model
     * @param array $data
     * 
     * @return void
     */
    public function delete(
        string $model,
        array $data
    ): void {
        $this->hrDataRepository
            ->delete(
                $model,
                $data
            );
    }

    /**
     * @param string $model
     * 
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getData(string $model): Collection
    {
        return $this->hrDataRepository->getData($model);
    }
}
