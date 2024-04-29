<?php

namespace App\Repositories;

use App\Models\CivilStatus;
use App\Models\Department;
use App\Models\EmploymentType;
use App\Models\PagibigContribution;
use App\Models\PhilhealthContribution;
use App\Models\Role;
use App\Models\SssContribution;
use App\Models\Tax;
use Illuminate\Database\Eloquent\Collection;

class HrDataRepository
{
    /**
     * @var App\Models\CivilStatus $civilStatus
     */
	private CivilStatus $civilStatus;

    /**
     * @var App\Models\Department $department
     */
    private Department $department;

    /**
     * @var App\Models\EmploymentType $employmentType
     */
    private EmploymentType $employmentType;

    /**
     * @var App\Models\PagibigContribution $pagibigContribution
     */
    private PagibigContribution $pagibigContribution;

    /**
     * @var App\Models\PhilhealthContribution $philhealthContribution
     */
    private PhilhealthContribution $philhealthContribution;

    /**
     * @var App\Models\Role $role
     */
    private Role $role;

    /**
     * @var App\Models\SssContribution $sssContribution
     */
    private SssContribution $sssContribution;

    /**
     * @var App\Models\Tax $tax
     */
    private Tax $tax;

	public function __construct(
        CivilStatus $civilStatus,
        Department $department,
        EmploymentType $employmentType,
        PagibigContribution $pagibigContribution,
        PhilhealthContribution $philhealthContribution,
        Role $role,
        SssContribution $sssContribution,
        Tax $tax
    )
	{
        $this->civilStatus = $civilStatus;
        $this->department = $department;
        $this->employmentType = $employmentType;
        $this->pagibigContribution = $pagibigContribution;
        $this->philhealthContribution = $philhealthContribution;
        $this->role = $role;
        $this->sssContribution = $sssContribution;
        $this->tax = $tax;
	}

    /**
     * @param string $model
     * 
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getData(string $model): Collection
    {
        $class = $this->getClass($model);
        return $class->orderBy('created_at', 'ASC')->get();
    }

    /**
     * @param string $model
     * @param array $condition
     * @param array $data
     * 
     * @return object
     */
    public function update(
        string $model,
        array $condition,
        array $data
    ): object {
        $class = $this->getClass($model);

        return $class->updateOrCreate(
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
        $class = $this->getClass($model);

        $class->whereNotIn(
                'id',
                array_map(
                    function ($a) {
                        return $a['id'];
                    },
                    $data
                )
            )
            ->delete();
    }

    /**
     * @return object
     */
    private function getClass(string $model): object
    {        
        switch ($model) {
            case 'civilStatuses':
                return $this->civilStatus;
                break;

            case 'departments':
                return $this->department;
                break;

            case 'employmentTypes':
                return $this->employmentType;
                break;

            case 'pagibigContributions':
                return $this->pagibigContribution;
                break;

            case 'philhealthContributions':
                return $this->philhealthContribution;
                break;

            case 'roles':
                return $this->role;
                break;

            case 'sssContributions':
                return $this->sssContribution;
                break;

            case 'taxes':
                return $this->tax;
                break;
        }
    }
}
