<?php

namespace App\Services;

use App\Repositories\AppVersionRepository;

class AppVersionService
{
    /**
     * @var App\Repositories\AppVersionRepository $appVersionRepository
     */
    private AppVersionRepository $appVersionRepository;

    public function __construct(AppVersionRepository $appVersionRepository)
    {
        $this->appVersionRepository = $appVersionRepository;
    }

    /**
     * @param string $platform
     * 
     * @return object|null
     */
    public function get(string $platform): ?object
    {
        return $this->appVersionRepository->get($platform);
    }
}
