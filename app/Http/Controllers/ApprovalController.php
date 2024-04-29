<?php

namespace App\Http\Controllers;

use App\Http\Requests\Approval\ApprovalIndexRequest;
use App\Services\OvertimeFilingService;
use App\Traits\ApiResponser;
use App\Traits\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApprovalController extends Controller
{
    use ApiResponser;

    use AuditTrail;

    /**
     * @var App\Services\OvertimeFilingService $overtimeFilingService
     */
    private OvertimeFilingService $overtimeFilingService;

    public function __construct(OvertimeFilingService $overtimeFilingService)
    {
        $this->overtimeFilingService = $overtimeFilingService;
    }

    /**
     * Get OT filings
     * 
     * @param App\Http\Requests\Approval\ApprovalIndexRequest $request
     * 
     * @return Illuminate\Http\Response
     */
    public function index(ApprovalIndexRequest $request): Response
    {
        $user = config('user');

        $params = [
            'userId' => $user['id'],
            'dateFrom' => $request->dateFrom,
            'dateTo' => $request->dateTo,
            'departmentId' => $request->departmentId ?? null,
            'projectId' => $request->projectId ?? null,
            'sortBy' => $request->sortBy ?? 'date',
            'sorting' => $request->sorting ?? 'asc'
        ];
        
        $otFilings = $this->overtimeFilingService->get($params);

        return $this->apiResponse(
            'OT filings successfully retrieved',
            $otFilings,
             200
         );
    }

    /**
     * Apprve/Decline an OT filing
     * 
     * @param Illuminate\Http\Request $request
     * @param int $id
     * @param string $status
     * 
     * @return Illuminate\Http\Response
     */
    public function status(
        Request $request,
        int $id,
        string $status
    ): Response {
        $user = config('user');
        $ot = $this->overtimeFilingService->findById($id);

        if ($status == 'approved') {
            $holidayHours = ((float)$request->holidayHours) ?? 0;
            $otHours = ((float)$request->otHours) ?? 0;
            $nightOtHours = ((float)$request->nightOtHours) ?? 0;
            $data = [
                'approved_holiday_hours' => $holidayHours,
                'approved_ot_hours' => $otHours,
                'approved_night_ot_hours' => $nightOtHours,
                'approved_by' => $user['id'],
                'declined_by' => null
            ];
        } else {
            $data = [
                'approved_holiday_hours' => 0,
                'approved_ot_hours' => 0,
                'approved_night_ot_hours' => 0,
                'declined_by' => $user['id'],
                'approved_by' => null
            ];
        }

        $ot->update($data);

        $this->saveLogs(
            'Approval',
            $data,
            "Approved an OT filing - $id"
        );

        return $this->apiResponse(
            'OT filing successfully updated',
            $ot,
            200
        );
    }
}
