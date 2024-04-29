<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuditTrails\AuditTrailsIndexRequest;
use App\Services\AuditTrailService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuditTrailsController extends Controller
{
    use ApiResponser;

    /**
     * @var App\Services\AuditTrailService $auditTrailService
     */
    private AuditTrailService $auditTrailService;

    public function __construct(AuditTrailService $auditTrailService)
    {
        $this->auditTrailService = $auditTrailService;
    }

    /**
     * Get audit trails
     * 
     * @param App\Http\Requests\AuditTrails\AuditTrailsIndexRequest $request
     * @return Illuminate\Http\Response
     */
    public function index(AuditTrailsIndexRequest $request): Response
    {
        $data = $request->validated();

        $params = [
            'keyword' => $data['keyword'] ?? null,
            'date' => $data['date'] ?? null,
            'sortBy' => $data['sortBy'] ?? 'created_at',
            'sorting' => $data['sorting'] ?? 'DESC',
            'limit' => $data['limit'] ?? 10
        ];

        $audit_trails = $this->auditTrailService->get($params);

        return $this->apiResponse(
            'Audit trails were successfully retrieved',
            $audit_trails,
            200
        );
    }
}
