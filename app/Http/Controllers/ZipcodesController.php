<?php

namespace App\Http\Controllers;

use App\Services\ZipcodeService;
use App\Traits\ApiResponser;
use Illuminate\Http\Response;


class ZipcodesController extends Controller
{
    use ApiResponser;

    /**
     * @var App\Services\ZipcodeService $zipcodeService
     */
    private ZipcodeService $zipcodeService;

    public function __construct(ZipcodeService $zipcodeService)
    {
        $this->zipcodeService = $zipcodeService;
    }

    /**
     * @return Illuminate\Http\Response
     */
    public function index(): Response
    {
        $data = [
            'cities' => $this->zipcodeService->getCities(),
            'provinces' => $this->zipcodeService->getProvinces(),
            'zipcodes' => $this->zipcodeService->getZipcodes()
        ];

        return $this->apiResponse(
            'Zipcodes successfully retrieved',
            $data,
            200
        );
    }
     
}
