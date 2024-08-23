<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Helpers\APIHelper;
use App\Models\User;
use App\Models\Role;

/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="Manage Authentication & Authorization"
 * )
*/

class AuthController extends Controller
{
    private const MODEL = 'USER';

    /**
     * @OA\Get(
     *      path="/api/info",
     *      tags={"Auth"},
     *      summary="Get current user info",
     *      description="Retrieve the information of the currently logged-in user.",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="data", type="object")
     *          )
     *      )
     * )
     */
    public function info(Request $request) {
        return APIHelper::successResponse(message: 'Get User Login Info success!', data: $request->user());
    }

    /**
     * @OA\Post(
     *      path="/api/register",
     *      tags={"Auth"},
     *      summary="Register a new user",
     *      description="Create a new user account.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"username","password"},
     *              @OA\Property(property="username", type="string", example="john_doe"),
     *              @OA\Property(property="password", type="string", example="password123")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="User created successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="data", type="object")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="errors", type="object")
     *          )
     *      )
     * )
     */
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

    /**
     * @OA\Post(
     *      path="/api/login",
     *      tags={"Auth"},
     *      summary="Login a user",
     *      description="Authenticate a user and return a token.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"username","password"},
     *              @OA\Property(property="username", type="string", example="john_doe"),
     *              @OA\Property(property="password", type="string", example="password123")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Login successful",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="data", type="string", example="token")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="errors", type="object")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
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

    /**
     * @OA\Post(
     *      path="/api/logout",
     *      tags={"Auth"},
     *      summary="Logout a user",
     *      description="Revoke the current user's token.",
     *      @OA\Response(
     *          response=200,
     *          description="Logout successful",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return APIHelper::successResponse(message: 'Logout success!');
    }

    /**
     * @OA\Post(
     *      path="/api/decentralize",
     *      tags={"Auth"},
     *      summary="Decentralize user roles",
     *      description="Update roles for users.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "user:1": {"role1", "role2"},
     *                  "user:2": {"role3"}
     *              }
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Roles updated successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Role keys do not exist",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
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

    /**
     * @OA\Get(
     *      path="/api/users",
     *      tags={"Auth"},
     *      summary="Get all users",
     *      description="Retrieve a list of all users with their roles.",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="username", type="string"),
     *                      @OA\Property(property="roles", type="array",
     *                          @OA\Items(
     *                              type="object",
     *                              @OA\Property(property="id", type="integer"),
     *                              @OA\Property(property="name", type="string"),
     *                              @OA\Property(property="key", type="string")
     *                          )
     *                      )
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string")
     *          )
     *      )
     * )
    */
    public function show(Request $request) {
        try {
            $data = User::with('roles')->get();

            return APIHelper::successResponse(statusCode: 200, message: 'Get all ' . self::MODEL .' successfully!', data: $data);
        } catch (\Throwable $th) {
            return APIHelper::errorResponse(message: $th->getMessage());
        }
    }
}