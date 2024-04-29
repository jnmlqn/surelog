<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Users\UsersCreateRequest;
use App\Http\Requests\Users\UsersIndexRequest;
use App\Http\Requests\Users\UsersUpdateRequest;
use App\Models\OauthAccessTokens;
use App\Services\UserService;
use App\Traits\ApiResponser;
use App\Traits\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;
use \Firebase\JWT\JWT;

class UsersController extends Controller
{
    use ApiResponser;

    use AuditTrail;

    /**
     * @var App\Services\UserService $userService
     */
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param App\Http\Requests\Users\UsersIndexRequest $request
     * 
     * @return Illuminate\Http\Response
     */
    public function index(UsersIndexRequest $request): Response
    {
        $users = $this->userService->searchUser([
            'keyword' => $request->keyword ?? null,
            'departmentId' => $request->department_id ?? null,
            'roleId' => $request->role_id ?? null,
            'sortBy' => $request->sort_by ?? 'created_at',
            'sorting' => $request->sorting ?? 'DESC',
            'limit' => $request->limit ?? 10,
            'get' => $request->boolean('get', false)
        ]);

        return $this->apiResponse(
            'Employees were successfully retrieved',
            $users,
            200
        );
    }

    /**
     * @param string $id
     * 
     * @return Illuminate\Http\Response
     */
    public function show(string $id): Response
    {
        $user = $this->userService->findById(
            $id,
            [
                'address.zipcode_id.city_id.province_id',
                'officeSchedule',
                'supervisor'
            ]
        );
        return $this->apiResponse(
            'Employee details were successfully retrieved',
            $user,
            200
        );
    }

    /**
     * @param App\Http\Requests\Users\UsersCreateRequest $request
     * 
     * @return Illuminate\Http\Response
     */
    public function store(UsersCreateRequest $request): Response
    {
        $data = $request->validated();

        $data['password'] = isset($data['password'])
            ? Hash::make($data['password'])
            : Hash::make($data['last_name']);

        $user = $this->userService->createUser($data);

        $data['user_id'] = $user->id;

        $this->userService->createUserAddress($data);

        if ($request->office_schedule) {
            $this->userService->createOfficeSchedule($data);
        }

        $this->saveLogs(
            'Employees',
            $data,
            "Created an employee - {$user->id}"
        );

        return $this->apiResponse(
            'Employee was stored successfully',
            $user,
            201
        );
    }

    /**
     * @param App\Http\Requests\Users\UsersUpdateRequest $request
     * @param string $id
     * 
     * @return Illuminate\Http\Response
     */
    public function update(
        UsersUpdateRequest $request,
        string $id
    ): Response {
        $data = $request->validated();
        $data['user_id'] = $id;

        $user = $this->userService->findById($id);

        $data['password'] = isset($data['password'])
            ? Hash::make($data['password'])
            : $user->password;

        $user->update($data);

        $address = $user->address;

        if ($address) {
            $address->fill($data)->save();
        } else {
            $this->userService->createUserAddress($data);
        }

        $schedule = $user->officeSchedule;

        if ($request->office_schedule) {
            if ($schedule) {
                $schedule->fill($data)->save();
            } else {
                $this->userService->createOfficeSchedule($data);
            }
        } else {
            if ($schedule) {
                $schedule->delete();
            }
        }

        $this->saveLogs(
            'Employees',
            $data,
            "Updated the employee details - $id"
        );

        return $this->apiResponse(
            'Employee details were updated successfully',
            null,
            200
        );
    }

    /**
     * @param string $id
     * 
     * @return Illuminate\Http\Response
     */
    public function destroy(string $id): Response
    {
        $user = $this->userService->findById($id);
        $user->address()->delete();
        $user->officeSchedule()->delete();
        $user->delete();

        $this->saveLogs(
            'Employees',
            $id,
            'Deleted an employee'
        );

        return $this->apiResponse(
            'Employee was deleted successfully',
            null,
            200
        );
    }

    /**
     * @param App\Http\Requests\Auth\LoginRequest $request
     * 
     * @return Illuminate\Http\Response
     */
    public function login(LoginRequest $request): Response
    {
        $user = $this->userService->findByEmail(
            $request->email,
            $this->userData()
        );

        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('Personal Access Token')->accessToken;
                $user['access_token'] = $token;

                $this->saveLogs('Login', $user, 'Login');
                
                return $this->apiResponse(
                    'Login Successful',
                    $user,
                    200
                );
            } else {
                return $this->apiResponse(
                    'Invalid Password',
                    null,
                    500
                );
            }
        } else {
            return $this->apiResponse(
                'Employee not found',
                null,
                404
            );
        }
    }

    /**
     * @param Illuminate\Http\Request $request
     * 
     * @return Illuminate\Http\Response
     */
    public function logout(Request $request): Response
    {
        $data = config('oauth');
        
        $oauth = OauthAccessTokens::where('id', $data->id)->update(['revoked' => 1]);

        return $this->apiResponse(
            'You are logged out',
            null,
            200
        );
    }

    /**
     * @param Illuminate\Http\Request $request
     * 
     * @return Illuminate\Http\Response
     */
    public function me(Request $request): Response
    {
        $user = config('user');

        $user = $user = $this->userService->findById(
            $user['id'],
            $this->userData()
        );

        return $this->apiResponse(
            'Authenticated',
            $user,
            200
        );
    }

    /**
     * @param Illuminate\Http\Request $request
     * 
     * @return Illuminate\Http\Response
     */
    public function changePassword(Request $request): Response
    {
        $user = config('user');

        $old = $request->old;
        $new = $request->new;

        $user = $this->userService->findById($user['id']);

        if (Hash::check($old, $user->password)) {
            if($old == $new) {
                return $this->apiResponse(
                    'New password cannot be the same as old password',
                    null,
                    500
                );
            } else {
                $user->password = Hash::make($new);
                $user->save();
                return $this->apiResponse(
                    'Password updated successfully',
                    null,
                    200
                );
            }
        } else {
            return $this->apiResponse(
                'Invalid old password',
                null,
                500
            );
        }
    }

    /**
     * @return array
     */
    public function userData(): array
    {
        return [
            'departmentId',
            'civilStatusId',
            'employmentTypeId:id,name',
            'projectMember',
            'projectAuthority',
            'address.zipcode_id.city_id.province_id',
            'officeSchedule',
            'supervisor:id,first_name,middle_name,last_name,extension'
        ];
    }
}
