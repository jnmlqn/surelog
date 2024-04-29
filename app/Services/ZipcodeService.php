<?php

namespace App\Services;

use App\Repositories\ZipcodeRepository;

class ZipcodeService
{
    /**
     * @var App\Repositories\ZipcodeRepository $zipcodeRepository
     */
    private ZipcodeRepository $zipcodeRepository;

    public function __construct(ZipcodeRepository $zipcodeRepository)
    {
        $this->zipcodeRepository = $zipcodeRepository;
    }

    /**
     * @return object
     */
    public function getCities(): object
    {
        return $this->zipcodeRepository->getCities();
    }

    /**
     * @return object
     */
    public function getProvinces(): object
    {
        return $this->zipcodeRepository->getProvinces();
    }

    /**
     * @return object
     */
    public function getZipcodes(): object
    {
        return $this->zipcodeRepository->getZipcodes();
    }
}
