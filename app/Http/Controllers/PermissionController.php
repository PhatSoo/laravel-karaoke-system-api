<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

use App\Helpers\APIHelper;
use App\Models\Permission;

/**
 * @OA\Tag(
 *     name="Permissions",
 *     description="API Endpoints of Permissions"
 * )
*/

class PermissionController extends Controller
{
    private const LIMIT = 5;
    private const CURRENT_PAGE = 1;
    private const MODEL = 'PERMISSION';

    /**
     * @OA\Get(
     *     path="/permissions",
     *     summary="Retrieve a list of permissions",
     *     description="Get all permissions with pagination",
     *     tags={"Permissions"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of records to return",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number to return",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved permissions",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request",
     *     )
     * )
     */
    public function listAll (Request $request) {
        $limit = $request->input('limit', self::LIMIT);
        $current_page = $request->input('page', self::CURRENT_PAGE);

        $offset = ($current_page - 1) * $limit;

        $allItems = Permission::orderBy('id', 'ASC');
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
     *     path="/permissions",
     *     summary="Create a new permission",
     *     description="Create a new permission",
     *     tags={"Permissions"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="manage_users")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully created permission",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request",
     *     )
     * )
     */
    public function create(Request $request) {
        try {
            $validated = Validator::make($request->all(), [
                'name' => 'required|unique:permissions,name|string',
            ]);

            if ($validated->fails()) {
                return APIHelper::errorResponse(statusCode: 400, message: $validated->messages());
            }

            $createdNew = new Permission();
            $createdNew->fill($request->all());
            $createdNew->save();

            return APIHelper::successResponse(statusCode: 201, message: 'Create new ' . self::MODEL .' successfully!');
        } catch (\Throwable $th) {
            return APIHelper::errorResponse(message: $th->getMessage() . ' in line: ' . $th->getLine());
        }

    }

    /**
     * @OA\Put(
     *     path="/permissions/{id}",
     *     summary="Update an existing permission",
     *     description="Update a permission by ID",
     *     tags={"Permissions"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Permission ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="manage_roles")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully updated permission",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Permission not found",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request",
     *     )
     * )
     */
    public function update (Request $request, $id) {
        $foundItem = Permission::find($id);

        if (!$foundItem) {
            return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
        }

        $validated = Validator::make($request->all(), [
            'name' => 'required|unique:permissions,name|string'
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
     *     path="/permissions/{id}",
     *     summary="Get permission details",
     *     description="Retrieve details of a specific permission by ID",
     *     tags={"Permissions"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Permission ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved permission details",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Permission not found",
     *     )
     * )
     */
    public function getDetails ($id) {
        try {
            $foundItem = Permission::find($id);

            if ($foundItem) {
                return APIHelper::successResponse(statusCode: 200, message: "Get " . self::MODEL ." details with id::{$id} successfully!", data: $foundItem);
            }

            return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
        } catch (\Throwable $th) {
            return APIHelper::errorResponse(message: $th->getMessage());
        }

    }

    /**
     * @OA\Delete(
     *     path="/permissions/{id}",
     *     summary="Delete a permission",
     *     description="Delete a permission by ID",
     *     tags={"Permissions"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Permission ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully deleted permission",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Permission not found",
     *     )
     * )
     */
    public function destroy ($id) {
        $foundItem = Permission::find($id);

        if ($foundItem) {
            Permission::destroy($id);

            return APIHelper::successResponse(statusCode: 200, message: "Remove " . self::MODEL . " with id::{$id} successfully!");
        }

        return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
    }
}