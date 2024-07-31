<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Helpers\APIHelper;
use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;

class AuthController extends Controller
{
    private const MODEL = 'USER';

    public function info(Request $request) {
        return APIHelper::successResponse(message: 'Get User Login Info success!', data: $request->user());
    }

    public function register(Request $request) {
        $validated = Validator::make($request->all(),[
            'username' => 'required|min:6|max:20|string|unique:users,username',
            'password' => 'required|string|min:6'
        ]);

        if ($validated->fails()) {
            return APIHelper::errorResponse(statusCode: 400, message: $validated->messages());
        }

        $createdNew = new User();
        $createdNew->fill($request->all());
        $createdNew->save();

        return APIHelper::successResponse(statusCode: 201, message: 'Create new ' . self::MODEL .' successfully!');
    }

    public function login(Request $request) {
        $validated = Validator::make($request->all(),[
            'username' => 'required',
            'password' => 'required'
        ]);

        if ($validated->fails()) {
            return APIHelper::errorResponse(statusCode: 400, message: $validated->messages());
        }

        if(Auth::attempt($request->all())) {
            $token = $request->user()->createToken($request->username, ['*']/*, now()->addMinutes(5)*/)->plainTextToken;
            return APIHelper::successResponse(message: 'Login success!', data: $token);
        }

        return APIHelper::errorResponse(statusCode: 401, message: "Login info is wrong!");
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return APIHelper::successResponse(message: 'Logout success!');
    }

    public function decentralize(Request $request) {
        try {
            $data = $request->all();

            foreach ($data as $key => $value) {
                $user_id = explode(':', $key)[1];
                $existed_user = User::find($user_id);
                if (is_null($existed_user)) {
                    return APIHelper::errorResponse(statusCode: 404, message: 'USER account not found');
                }

                if (is_array($value)) {
                    // Get all current roles of user by user_id
                    $user_roles = UserRole::where('user_id', '=', $user_id)->pluck('role_key')->toArray();

                    // Get new roles in request
                    $new_roles = array_diff($value, $user_roles);
                    if (count($new_roles) === 0) {
                        // If have no new roles => return success
                        return APIHelper::successResponse(message: 'Update Role successfully!');
                    }

                    // Get roles existed in DB but not in request to remove roles
                    $removing_roles = array_diff($user_roles, $value);

                    $insert_data = [];
                    // Create new roles
                    foreach ($new_roles as $role) {
                        // Check existed roles
                        $is_existed_role = Role::where('key', $role)->exists();
                        if (!$is_existed_role) {
                            return APIHelper::errorResponse(statusCode: 400, message: "Role Key '{$role}' does not exist!");
                        }

                        $insert_data[] = [
                            'user_id' => $user_id,
                            'role_key' => $role
                        ];
                    }

                    // Insert all new roles if exist
                    if (!empty($insert_data)) {
                        UserRole::insert($insert_data);
                    }

                    // Remove roles that not in request
                    UserRole::where('user_id', $user_id)->whereIn('role_key', $removing_roles)->delete();
                }
            }
            // All insert roles success
            return APIHelper::successResponse(message: 'Update Role successfully!');
        } catch (\Throwable $th) {
            return APIHelper::errorResponse(message: $th->getMessage());
        }
    }

    public function show(Request $request) {
        try {
            $data = UserRole::all()->groupBy('user_id')->map(function ($roles) {
                $user = $roles->first()->user;
                $user->roles = $roles->pluck('role');
                return $user;
            });

            return APIHelper::successResponse(statusCode: 200, message: 'Get all ' . self::MODEL .' successfully!', data: $data);
        } catch (\Throwable $th) {
            return APIHelper::errorResponse(message: $th->getMessage());
        }
    }
}