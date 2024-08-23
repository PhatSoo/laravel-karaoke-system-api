<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Helpers\APIHelper;
use App\Models\Role;
use App\Models\Permission;

/**
 * @OA\Tag(
 *     name="Roles",
 *     description="Manage Roles"
 * )
*/

class RoleController extends Controller
{
    private const LIMIT = 5;
    private const CURRENT_PAGE = 1;
    private const MODEL = 'ROLE';

    /**
     * @OA\Get(
     *     path="/api/roles",
     *     summary="Get all roles with pagination",
     *     tags={"Roles"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Current page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Get all roles successfully!",
     *         @OA\JsonContent(
     *             @OA\Property(property="paginate", type="object",
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="limit", type="integer")
     *             ),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="statusCode", type="integer")
     *         )
     *     )
     * )
     */
    public function listAll (Request $request) {
        $limit = $request->input('limit', self::LIMIT);
        $current_page = $request->input('page', self::CURRENT_PAGE);

        $offset = ($current_page - 1) * $limit;

        $allItems = Role::orderBy('id', 'ASC');
        $total = $allItems->count();

        $data = $allItems->skip($offset)->take($limit)->get();

        $paginate = [
            'total' => $total,
            'current_page' => $current_page,
            'limit' => $limit
        ];

        return APIHelper::successResponse(statusCode: 200, message: 'Get all ' . self::MODEL .' successfully!', data: $data, paginate: $paginate);
    }

    /**
     * @OA\Post(
     *     path="/api/roles",
     *     summary="Create a new role",
     *     tags={"Roles"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Admin")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Create new role successfully!",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="statusCode", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="statusCode", type="integer")
     *         )
     *     )
     * )
     */
    public function create(Request $request) {
        $validated = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name|string',
        ]);

        if ($validated->fails()) {
            return APIHelper::errorResponse(statusCode: 400, message: $validated->messages());
        }

        $createdNew = new Role();
        $createdNew->fill($request->all());
        $createdNew->save();

        return APIHelper::successResponse(statusCode: 201, message: 'Create new ' . self::MODEL .' successfully!');
    }

    /**
     * @OA\Put(
     *     path="/api/roles/{id}",
     *     summary="Update an existing role",
     *     tags={"Roles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Role ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Super Admin")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Update role successfully!",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="statusCode", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Role not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="statusCode", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="statusCode", type="integer")
     *         )
     *     )
     * )
     */
    public function update (Request $request, $id) {
        $foundItem = Role::find($id);

        if (!$foundItem) {
            return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
        }

        $validated = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name|string'
        ]);

        // Check validate form required fields
        if ($validated->fails()) {
            return APIHelper::errorResponse(statusCode: 400, message: $validated->messages());
        }

        $foundItem->update($request->all());

        return APIHelper::successResponse(statusCode: 200, message: "Update " . self::MODEL . " with id::{$id} successfully!");
    }

    /**
     * @OA\Get(
     *     path="/api/roles/{id}",
     *     summary="Get role details",
     *     tags={"Roles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Role ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Get role details successfully!",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="statusCode", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Role not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="statusCode", type="integer")
     *         )
     *     )
     * )
     */
    public function getDetails ($id) {
        $foundItem = Role::find($id);

        if ($foundItem) {
            return APIHelper::successResponse(statusCode: 200, message: "Get " . self::MODEL ." details with id::{$id} successfully!", data: $foundItem);
        }

        return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
    }

    /**
     * @OA\Delete(
     *     path="/api/roles/{id}",
     *     summary="Delete a role",
     *     tags={"Roles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Role ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Remove role successfully!",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="statusCode", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Role not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="statusCode", type="integer")
     *         )
     *     )
     * )
     */
    public function destroy ($id) {
        $foundItem = Role::find($id);

        if ($foundItem) {
            Role::destroy($id);

            return APIHelper::successResponse(statusCode: 200, message: "Remove " . self::MODEL . " with id::{$id} successfully!");
        }

        return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
    }

    /**
     * @OA\Get(
     *     path="/api/roles/permissions",
     *     summary="Get all roles with their permissions",
     *     tags={"Roles"},
     *     @OA\Response(
     *         response=200,
     *         description="Get all roles with permissions successfully!",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="statusCode", type="integer")
     *         )
     *     )
     * )
     */
    public function showPermissions() {
        $data = Role::with('permissions')->get();
        return APIHelper::successResponse(statusCode: 200, message: 'Get all ' . self::MODEL .' successfully!', data: $data);
    }

    /**
     * @OA\Post(
     *     path="/api/roles/decentralize",
     *     summary="Decentralize permissions to roles",
     *     tags={"Roles"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\AdditionalProperties(
     *                 type="array",
     *                 @OA\Items(type="string")
     *             ),
     *             example={
     *                 "role_key_1": {"permission_key_1", "permission_key_2"},
     *                 "role_key_2": {"permission_key_3"}
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Update permissions for roles successfully!",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="statusCode", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="statusCode", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Role or Permission not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="statusCode", type="integer")
     *         )
     *     )
     * )
     */
    public function decentralize(Request $request) {
        try {
            DB::beginTransaction();
            $data = $request->all();
            // example data
            /*
            {
                role_key_1: [
                    permission_key_1,
                    permission_key_2
                ],
                role_key_2: [...]
            }
            */

            foreach ($data as $role_key => $value) {
                $existed_role = Role::find($role_key);
                if (is_null($existed_role)) {
                    DB::rollBack();
                    return APIHelper::errorResponse(statusCode: 404, message: "ROLE::{$role_key} not found");
                }

                if (is_array($value)) {
                    $existing_permissions = Permission::whereIn('key', $value)->pluck('key')->toArray();
                    $missing_roles = array_diff($value, $existing_permissions);
                    if (!empty($missing_roles)) {
                        DB::rollBack();
                        return APIHelper::errorResponse(statusCode: 400, message: "Permission Key::". implode(',', $missing_roles) ." does not exist!");
                    }

                    $existed_role->permissions()->sync($value);
                }
            }
            DB::commit();
            // All insert roles success
            return APIHelper::successResponse(message: 'Update Permissions for Role successfully!');
        } catch (\Throwable $th) {
            DB::rollBack();
            return APIHelper::errorResponse(message: $th->getMessage());
        }
    }
}
