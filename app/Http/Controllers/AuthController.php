<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Helpers\APIHelper;
use App\Models\User;

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
        if(Auth::attempt($request->all())) {
            $token = $request->user()->createToken($request->username, ['*'], now()->addMinutes(5));
            return APIHelper::successResponse(message: 'Login success!', data: $token);
        }

        return APIHelper::errorResponse(statusCode: 401, message: "Login info is wrong!");
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return APIHelper::successResponse(message: 'Logout success!');
    }
}
