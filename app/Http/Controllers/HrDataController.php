<?php

namespace App\Http\Controllers;

use App\Http\Requests\HrData\HrDataUpdateRequest;
use App\Traits\ApiResponser;
use App\Traits\AuditTrail;
use App\Services\HrDataService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HrDataController extends Controller
{
    use ApiResponser;

    use AuditTrail;

    /**
     * @var App\Services\HrDataService $hrDataService
     */
    private HrDataService $hrDataService;

    public function __construct(HrDataService $hrDataService)
    {
        $this->hrDataService = $hrDataService;
    }

    /**
     * Get index page data
     * 
     * @return Illuminate\Http\Response
     */
    public function index(): Response
    {
        $data = $this->hrDataService->get();

        return $this->apiResponse(
            'HR data were successfully retrieved',
            $data,
            200
        );
    }

    /**
     * @param App\Http\Requests\HrData\HrDataUpdateRequest $request
     * 
     * @return Illuminate\Http\Response
     */
    public function update(HrDataUpdateRequest $request): Response
    {
        $input = $request->validated();

        foreach ($input['civil_statuses'] as $key => $value) {
            $data['civil_statuses'][] = $this->hrDataService
                ->update(
                    'civilStatuses',
                    [
                        'id' => $value['id']
                    ],
                    $value
                );
        }

        foreach ($input['departments'] as $key => $value) {
            $data['departments'][] = $this->hrDataService
                ->update(
                    'departments',
                    [
                        'id' => $value['id']
                    ],
                    $value
                );
        }

        foreach ($input['employment_types'] as $key => $value) {
            $data['employment_types'][] = $this->hrDataService
                ->update(
                    'employmentTypes',
                    [
                        'id' => $value['id']
                    ],
                    $value
                );
        }

        foreach ($input['sss_contributions'] as $key => $value) {
            $data['sss_contributions'][] = $this->hrDataService
                ->update(
                    'sssContributions',
                    [
                        'id' => $value['id']
                    ],
                    $value
                );
        }

        foreach ($input['pagibig_contributions'] as $key => $value) {
            $data['pagibig_contributions'][] = $this->hrDataService
                ->update(
                    'pagibigContributions',
                    [
                        'id' => $value['id']
                    ],
                    $value
                );
        }

        foreach ($input['philhealth_contributions'] as $key => $value) {
            $data['philhealth_contributions'][] = $this->hrDataService
                ->update(
                    'philhealthContributions',
                    [
                        'id' => $value['id']
                    ],
                    $value
                );
        }

        foreach ($input['taxes'] as $key => $value) {
            $data['taxes'][] = $this->hrDataService
                ->update(
                    'taxes',
                    [
                        'id' => $value['id']
                    ],
                    $value
                );
        }

        $this->hrDataService
        ->delete(
            'civilStatuses',
            $data['civil_statuses']
        );

        $this->hrDataService
        ->delete(
            'departments',
            $data['departments']
        );

        $this->hrDataService
        ->delete(
            'employmentTypes',
            $data['employment_types']
        );

        $this->hrDataService
        ->delete(
            'sssContributions',
            $data['sss_contributions']
        );

        $this->hrDataService
        ->delete(
            'pagibigContributions',
            $data['pagibig_contributions']
        );

        $this->hrDataService
        ->delete(
            'philhealthContributions',
            $data['philhealth_contributions']
        );

        $this->hrDataService
        ->delete(
            'taxes',
            $data['taxes']
        );

        $this->saveLogs(
            'HR Data',
            $data,
            'Updated the HR data'
        );

        return $this->apiResponse(
            'HR data were successfully updated',
            $data,
            200
        );
    }

    /**
     * Get HR data for forms
     * 
     * @return Illuminate\Http\Response
     */
    public function getHrData(): Response
    {
        $data = [
            'departments' => $this->hrDataService->getData('departments'),
            'employment_types' => $this->hrDataService->getData('employmentTypes'),
            'civil_statuses' => $this->hrDataService->getData('civilStatuses'),
            'roles' => $this->hrDataService->getData('roles'),
        ];

        return $this->apiResponse(
            'HR data successfully retrieved',
            $data,
            200
        );
    }
}
