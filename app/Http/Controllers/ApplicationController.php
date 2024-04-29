<?php

namespace App\Http\Controllers;

use App\Services\AppVersionService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{
    use ApiResponser;

    /**
     * @var App\Services\AppVersionService $appVersionService
     */
    private AppVersionService $appVersionService;

    public function __construct(AppVersionService $appVersionService)
    {
        $this->appVersionService = $appVersionService;
    }

    /**
     * Store image to a specific folder
     * 
     * @param Illuminate\Http\Request $request
     * @param string $folder
     * 
     * @return Illuminate\Http\Response
     */
    public function storeImage(Request $request, string $folder): Response
    {
        if (!empty($request->image) && $request->image !== '') {
            $name = Str::random(10).date('Ymdhis').'.png';
            Storage::disk('local')->put("/{$folder}/{$name}", base64_decode($request->image));
            $image = "/storage/{$folder}/{$name}";

            return $this->apiResponse(
                'Image Successfully Uploaded',
                $image,
                201
            );
        }

        return $this->apiResponse(
            'Invalid Image Type',
            $image,
            422
        );
    }

    /**
     * Check version of specific platform
     * 
     * @param string $platform
     * @return Illuminate\Http\Response
     */
    public function version(string $platform): Response
    {
        $version = $this->appVersionService->get($platform);

        $response = $version
            ? $this->apiResponse(
                'App version retrieved successfully',
                $version,
                200
            )
            : $this->apiResponse(
                'Platfrom does not exist',
                null,
                500
            );
            
        return $response;
    }
}
