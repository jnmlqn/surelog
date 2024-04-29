<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use \Firebase\JWT\JWT;
use App\Models\OauthAccessTokens;
use App\Services\UserService;
use Carbon\Carbon;

class JwtAuth
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * @var App\Services\UserService
     */
    private UserService $userService;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth, UserService $userService)
    {
        $this->auth = $auth;
        $this->userService = $userService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        JWT::$leeway = 10;
        $public_key = file_get_contents(storage_path('oauth-public.key'));
        $access_token = trim(str_replace('Bearer', '', $request->header('Authorization')));

        if ($access_token == 'null' || $access_token == null || $access_token == '') {
            return response('Unauthorized.', 401);
        }

        try {
            $data = JWT::decode($access_token, $public_key, ['RS256']);
        } catch (Exception $e) {
            return response('Unauthorized.', 401);
        }
        
        if (!$data) {
            return response('Unauthorized.', 401);
        }

        $oauth = OauthAccessTokens::find($data->jti);
        $user = $this->userService->findById($data->sub);

        if (!$user || !$oauth || $oauth->revoked == 1) {
            return response('Unauthorized.', 401);
        }

        config()->set('user', [
            "id" => $user->id,
            "name" => $user->name,
            "email" => $user->email,
            "position" => $user->position,
            "image" => $user->image,
            "role_id" => $user->roleId,
            "department_id" => $user->department_id,
            "employment_type_id" => $user->employment_type_id,
            "civil_status_id" => $user->civil_status_id,
        ]);
        
        config()->set('oauth', $oauth);

        return $next($request);
    }
}
