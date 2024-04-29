<?php

namespace App\Repositories;

use App\Models\City;
use App\Models\Province;
use App\Models\Zipcode;

class ZipcodeRepository
{
    /**
     * @var App\Models\City $city
     */
    private City $city;

    /**
     * @var App\Models\Province $province
     */
    private Province $province;

    /**
     * @var App\Models\Zipcode $zipcode
     */
    private Zipcode $zipcode;

	public function __construct(
        City $city,
        Province $province,
        Zipcode $zipcode
    ) {
        $this->city = $city;
        $this->province = $province;
		$this->zipcode = $zipcode;
	}

    /**
     * @return object
     */
    public function getCities(): object
    {
        return $this->city->orderBy('city', 'ASC')->get();
    }

    /**
     * @return object
     */
    public function getProvinces(): object
    {
        return $this->province->orderBy('province', 'ASC')->get();
    }

    /**
     * @return object
     */
    public function getZipcodes(): object
    {
        return $this->zipcode->orderBy('area', 'ASC')->get();
    }
}
