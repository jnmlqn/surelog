<?php

namespace App\Services;

use App\Repositories\AuditTrailRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class AuditTrailService
{
    /**
     * @var App\Repositories\AuditTrailRepository $auditTrailRepository
     */
    private AuditTrailRepository $auditTrailRepository;

    public function __construct(AuditTrailRepository $auditTrailRepository)
    {
        $this->auditTrailRepository = $auditTrailRepository;
    }

    /**
     * @param array $params
     * 
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    public function get(array $params): LengthAwarePaginator
    {
        return $this->auditTrailRepository->get($params);
    }
}
