<?php

namespace App\Repositories;

use App\Models\AppVersion;

class AppVersionRepository
{
    /**
     * @var App\Models\AppVersion $appVersion
     */
	private AppVersion $appVersion;

	public function __construct(AppVersion $appVersion)
	{
		$this->appVersion = $appVersion;
	}

    /**
     * @param string $platform
     * 
     * @return object|null
     */
    public function get(string $platform): ?object
    {
        return $this->appVersion->where('platform', $platform)->first();
    }
}
