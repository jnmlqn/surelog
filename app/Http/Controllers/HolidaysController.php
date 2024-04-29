<?php

namespace App\Http\Controllers;

use App\Http\Requests\Holidays\HolidaysCreateRequest;
use App\Http\Requests\Holidays\HolidaysIndexRequest;
use App\Http\Requests\Holidays\HolidaysUpdateRequest;
use App\Services\HolidayService;
use App\Traits\ApiResponser;
use App\Traits\AuditTrail;
use Illuminate\Http\Response;

class HolidaysController extends Controller
{
    use ApiResponser;

    use AuditTrail;

    /**
     * @var App\Services\HolidayService $holidayService
     */
    private HolidayService $holidayService;

    public function __construct(HolidayService $holidayService)
    {
        $this->holidayService = $holidayService;
    }

    /**
     * Get index page data
     * 
     * @param App\Http\Requests\Holidays\HolidaysIndexRequest $request
     * 
     * @return Illuminate\Http\Response
     */
    public function index(HolidaysIndexRequest $request): Response
    {
        $params = [
            'keyword' => $request->keyword ?? null,
            'year' => $request->year ?? date('Y'),
            'month' => $request->month ?? null,
            'sortBy' => $request->sortBy ?? 'date',
            'sorting' => $request->sorting ?? 'ASC',
            'limit' => $request->limit ?? 10
        ];

        $holidays = $this->holidayService->get($params);

        return $this->apiResponse(
            'Holidays were successfully retrieved',
            $holidays,
            200
        );
    }

    /**
     * @param App\Http\Requests\Holidays\HolidaysCreateRequest $request
     * 
     * @return Illuminate\Http\Response
     */
    public function store(HolidaysCreateRequest $request): Response
    {
        $data = $request->validated();

        $holiday = $this->holidayService->create($data);

        $this->saveLogs(
            'Holidays',
            $data,
            'Added a holiday'
        );

        return $this->apiResponse(
            'Holiday was stored successfully',
            $holiday,
             201
         );
    }

    /**
     * @param App\Http\Requests\Holidays\HolidaysCreateRequest $request
     * 
     * @return Illuminate\Http\Response
     */
    public function update(HolidaysCreateRequest $request, $id): Response
    {
        $data = $request->validated();

        $holiday = $this->holidayService->findById($id); 

        $holiday->fill($data)->save();

        $this->saveLogs(
            'Holidays',
            $data,
            'Updated a holiday'
        );

        return $this->apiResponse(
            'Holiday was updated successfully',
            $holiday,
            200
        );
    }

    /**
     * @param int $id
     * 
     * @return Illuminate\Http\Response
     */
    public function destroy(int $id): Response
    {
        $holiday = $this->holidayService->deleteById($id);
        
        if ($holiday) {
            $this->saveLogs(
                'Holidays',
                $id,
                'Deleted a holiday'
            );
            return $this->apiResponse(
                'Holiday was deleted successfully',
                null,
                200
            );
        } else {
            return $this->apiResponse(
                'Holiday data not found',
                null,
                404
            );
        }
    }
}
