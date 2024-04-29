<?php

namespace App\Http\Controllers;

use App\Helpers\PayrollComputation;
use App\Http\Requests\Accounting\AccountingIndexRequest;
use App\Http\Requests\Accounting\AccountingBulkUpdateRequest;
use App\Http\Requests\Accounting\AccountingUpdateRequest;
use App\Services\UserService;
use App\Traits\ApiResponser;
use App\Traits\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AccountingController extends Controller
{
    use ApiResponser;

    use AuditTrail;

    /**
     * @var App\Services\UserService $userService
     */
    private UserService $userService;

    /**
     * @var App\Helpers\PayrollComputation $payrollComputation
     */
    private PayrollComputation $payrollComputation;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->payrollComputation = new PayrollComputation();
    }

    /**
     * Return attendance computation based on given parameters
     * 
     * @param App\Http\Requests\Accounting\AccountingIndexRequest $request
     * 
     * @return Illuminate\Http\Response
     */
    public function index(AccountingIndexRequest $request): Response
    {
        $data = $request->validated();

        $dateFrom = $data['dateFrom'];
        $dateTo = $data['dateTo'];
        $sortBy = $data['sortBy'] ?? 'first_name';
        $sorting = $data['sorting'] ?? 'asc';
        $departmentId = !empty($data['departmentId'])
            ? $data['departmentId']
            : null;
        $computationType = !empty($data['computationType'])
            ? $data['computationType']
            : '2_cutoffs';
        $projectId = !empty($data['projectId'])
            ? $data['projectId']
            : null;
        $employeeId = !empty($data['employeeId'])
            ? $data['employeeId']
            : null;
        
        $employees = $this->userService->getEmployeesByDepartmentIdandProjectId(
            [
                'sortBy' => $sortBy,
                'sorting' => $sorting
            ],
            $departmentId,
            $projectId,
            $employeeId
        );

        foreach ($employees as $key => $value) {
            $this->payrollComputation
                ->computeByEmployeeId(
                    $value,
                    $dateFrom,
                    $dateTo,
                    $computationType,
                    $projectId
                );
        }

        $this->saveLogs(
            'Accounting',
            $data,
            "Payroll computation ($dateFrom - $dateTo)"
        );

        return $this->apiResponse(
            'Payroll successfully computed',
            $employees,
            200
        );
    }

    /**
     * Update selected payroll computation
     * 
     * @param App\Http\Requests\Accounting\AccountingUpdateRequest $request
     * 
     * @return Illuminate\Http\Response
     */
    public function updatePayroll(AccountingUpdateRequest $request): Response
    {
        $data = $request->validated();

        $userId = $data['userId'];
        $dateFrom = $data['dateFrom'];
        $dateTo = $data['dateTo'];
        $computationType = $data['computationType'];
        $projectId = $data['projectId'];
        $inputs = $data['inputs'];

        $employee = $this->userService->findById($userId);

        $employee = $this->payrollComputation
            ->computeByEmployeeId(
                $employee,
                $dateFrom,
                $dateTo,
                $computationType,
                $projectId,
                $inputs
            );

        $this->saveLogs(
            'Accounting',
            $data,
            "Payroll computation updated for $userId ($dateFrom - $dateTo)"
        );

        return $this->apiResponse(
            'Payroll successfully updated',
            $employee,
            200
        );
    }

    /**
     * Bulk update selected payroll computation
     * 
     * @param App\Http\Requests\Accounting\AccountingBulkUpdateRequest $request
     * 
     * @return Illuminate\Http\Response
     */
    public function bulkUpdatePayroll(AccountingBulkUpdateRequest $request): Response
    {
        $data = $request->validated();

        $employees = $data['employees'];
        $dateFrom = $data['dateFrom'];
        $dateTo = $data['dateTo'];
        $computationType = $data['computationType'];
        $projectId = $data['projectId'];

        foreach ($employees as $key => $value) {
            $employee = $this->userService->findById($value['userId']);

            $employee = $this->payrollComputation
                ->computeByEmployeeId(
                    $employee,
                    $dateFrom,
                    $dateTo,
                    $computationType,
                    $projectId,
                    $value['inputs']
                );

            $response[] = $employee;
        }

        $this->saveLogs(
            'Accounting',
            $data,
            "Payroll computation bulk update ($dateFrom - $dateTo)"
        );

        return $this->apiResponse(
            'Payroll successfully updated',
            $response ?? [],
            200
        );
    }

    public function updateAttendance() {
        
    }
}
