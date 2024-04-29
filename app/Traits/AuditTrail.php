<?php

namespace App\Traits;

use App\Models\AuditTrail as Logs;

trait AuditTrail
{
    /**
     * @param string $module
     * @param array|string|int|null $data
     * @param string $message 
     */
	public function saveLogs(
        string $module,
        $data,
        string $message
    ): void {
		$user = config('user');

        Logs::create([
        	'user_id' => $user['id'] ?? $data->id, // Coalescing only on login
        	'module' => $module,
            'data' => json_encode($data),
        	'message' => $message
        ]);
	}
}