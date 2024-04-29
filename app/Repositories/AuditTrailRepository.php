<?php

namespace App\Repositories;

use App\Models\AuditTrail;
use Illuminate\Pagination\LengthAwarePaginator;

class AuditTrailRepository
{
    /**
     * @var App\Models\AuditTrail $auditTrail
     */
	private AuditTrail $auditTrail;

	public function __construct(AuditTrail $auditTrail)
	{
		$this->auditTrail = $auditTrail;
	}

    /**
     * @param array $params
     * 
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    public function get(array $params): LengthAwarePaginator
    {
        return $this->auditTrail
            ->with('user_id')
            ->where(function ($q) use ($params) {
                $q->when($params['keyword'] !== null && $params['keyword'] !== '', function ($q) use ($params) {
                    $q->whereHas('user_id', function ($q) use ($params) {
                        $q->where('first_name', 'LIKE', "%{$params['keyword']}%")
                        ->orWhere('middle_name', 'LIKE', "%{$params['keyword']}%")
                        ->orWhere('last_name', 'LIKE', "%{$params['keyword']}%");
                    })
                    ->orWhere('module', 'LIKE', "%{$params['keyword']}%")
                    ->orWhere('data', 'LIKE', "%{$params['keyword']}%");
                })
                ->when($params['date'] !== null && $params['date'] !== '', function ($q) use ($params) {
                    $q->whereDate('created_at', $params['date']);
                });
            })
            ->orderBy($params['sortBy'], $params['sorting'])
            ->paginate($params['limit']);
    }
}
