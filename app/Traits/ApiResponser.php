<?php

namespace App\Traits;


trait ApiResponser
{
    public function apiResponse($message, $data, $status)
    {
        return response([
            'message' => $message,
            'data' => $data,
            'status' => $status
        ], $status);
    }
}
