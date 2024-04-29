<?php

namespace App\Repositories;

use App\Models\Overtime;
use Illuminate\Database\Eloquent\Collection;

class OvertimeFilingRepository
{
    /**
     * @var App\Models\Overtime $overtime
     */
	private Overtime $overtime;

	public function __construct(Overtime $overtime)
	{
		$this->overtime = $overtime;
	}

    /**
     * @param array $params
     * 
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function get(array $params): Collection
    {
        return $this->overtime
            ->whereBetween('date', [$params['dateFrom'], $params['dateTo']])
            ->whereHas('user_id', function ($q) use ($params) {
                $q->where('supervisor', $params['userId']);
            })
            ->when(!empty($params['projectId']) && $params['projectId'] !== '', function ($q) use ($params) {
                $q->where('project_id', $params['projectId']);
            })
            ->when(!empty($params['departmentId']) && $params['departmentId'] !== '', function ($q) use ($params) {
                $q->whereHas('user_id', function ($q) use ($params) {
                    $q->where('department_id', $params['departmentId']);
                });
            })
            ->with([
                'user_id:id,first_name,middle_name,last_name,extension',
                'approved_by:id,first_name,middle_name,last_name,extension',
                'declined_by:id,first_name,middle_name,last_name,extension',
                'project_id:id,name'
            ])
            ->orderBy($params['sortBy'], $params['sorting'])
            ->get();
    }

    /**
     * @param int $id
     * 
     * @return App\Models\Overtime
     */
    public function findById(int $id): Overtime
    {
        return $this->overtime->findOrFail($id);
    }
}
