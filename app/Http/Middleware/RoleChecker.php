<?php

namespace App\Http\Middleware;

use App\Models\User;

use App\Traits\ApiResponser;

use Closure;

class RoleChecker
{

    use ApiResponser;

    public function handle($request, Closure $next, $module)
    {
        $user = config('user');
        $action = request()->route()[1]['as'];
        $permission = collect($user['role_id']['access'])->where('slug', $module)->first();
        
        if ($permission['permissions'][$action] ?? null) {
            return $next($request);
        } else {
            return $this->apiResponse(
                "Unauthorized", 
                [
                    'title' => ucwords(str_replace('-', ' ', $module))." - $action",
                    'message' => 'You are not allowed to do this action'
                ], 
                400
            );
        }
    }
}
