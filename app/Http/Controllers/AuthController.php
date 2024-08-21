<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Helpers\APIHelper;
use App\Models\User;
use App\Models\Role;

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
            DB::beginTransaction();
            $data = $request->all();

            foreach ($data as $key => $value) {
                $user_id = explode(':', $key)[1];
                $existed_user = User::find($user_id);
                if (is_null($existed_user)) {
                    DB::rollBack();
                    return APIHelper::errorResponse(statusCode: 404, message: 'USER account not found');
                }

                if (is_array($value)) {
                    // Check Role Keys exist ?
                    $existing_roles = Role::whereIn('key', $value)->pluck('key')->toArray();

                    $missing_keys = array_diff($value, $existing_roles);

                    if (!empty($missing_keys)) {
                        // if existed Role Key does not existed in Role Table
                        DB::rollBack();
                        return APIHelper::errorResponse(statusCode: 400, message: "Role Keys '" . implode(", ", $missing_keys) . "' do not exist!");
                    }

                    $existed_user->roles()->sync($value);
                }
            }

            DB::commit();
            // All insert roles success
            return APIHelper::successResponse(message: 'Update Role successfully!');
        } catch (\Throwable $th) {
            DB::rollBack();
            return APIHelper::errorResponse(message: $th->getMessage());
        }
    }

    public function show(Request $request) {
        try {
            $data = User::with('roles')->get();

            return APIHelper::successResponse(statusCode: 200, message: 'Get all ' . self::MODEL .' successfully!', data: $data);
        } catch (\Throwable $th) {
            return APIHelper::errorResponse(message: $th->getMessage());
        }
    }
}