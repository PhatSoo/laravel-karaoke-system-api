<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Models\Staff;
use App\Helpers\APIHelper;

/**
 * @OA\Tag(
 *     name="Staffs",
 *     description="Manage Staffs"
 * )
*/

class StaffController extends Controller
{
    private const LIMIT = 5;
    private const CURRENT_PAGE = 1;
    private const MODEL = 'STAFF';

    /**
     * @OA\Get(
     *     path="/staffs",
     *     summary="Get all staff members",
     *     description="Retrieve a paginated list of all staff members",
     *     operationId="listAllStaff",
     *     tags={"Staff"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of staff members to return per page",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number to retrieve",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="total", type="integer"),
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="limit", type="integer"),
     *         )
     *     )
     * )
     */
    public function listAll (Request $request) {
        $limit = $request->input('limit', self::LIMIT);
        $current_page = $request->input('page', self::CURRENT_PAGE);

        $offset = ($current_page - 1) * $limit;

        $allItems = Staff::orderBy('id', 'ASC');
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
     *     path="/staffs",
     *     summary="Create a new staff member",
     *     description="Add a new staff member to the system",
     *     operationId="createStaff",
     *     tags={"Staff"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="role", type="string", enum={"manager", "receptionist", "waiter"}),
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="email", type="string", format="email")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Staff member created successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     )
     * )
     */
    public function create(Request $request) {
        $validated = Validator::make($request->all(), [
            'name' => 'required|string',
            'role' => 'required|string|in:manager,receptionist,waiter',
            'phone' => 'required|unique:staffs,phone|string',
            'email' => 'required|unique:staffs,email|string|email',
        ]);

        if ($validated->fails()) {
            return APIHelper::errorResponse(statusCode: 400, message: $validated->messages());
        }

        $createdNew = new Staff();
        $createdNew->fill($request->all());
        $createdNew->save();

        return APIHelper::successResponse(statusCode: 201, message: 'Create new ' . self::MODEL .' successfully!');
    }

    /**
     * @OA\Put(
     *     path="/staffs/{id}",
     *     summary="Update an existing staff member",
     *     description="Update details of an existing staff member",
     *     operationId="updateStaff",
     *     tags={"Staff"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the staff member to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="role", type="string", enum={"manager", "receptionist", "waiter"}),
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="email", type="string", format="email")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Staff member updated successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Staff member not found"
     *     )
     * )
     */
    public function update (Request $request, $id) {
        $foundItem = Staff::find($id);

        if (!$foundItem) {
            return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
        }

        $validated = Validator::make($request->all(), [
            'name' => 'string',
            'role' => 'string|in:manager,receptionist,waiter',
            'phone' => 'unique:staffs,phone|string',
            'email' => 'unique:staffs,email|string|email',
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
     *     path="/staffs/{id}",
     *     summary="Get staff member details",
     *     description="Retrieve details of a specific staff member",
     *     operationId="getStaffDetails",
     *     tags={"Staff"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the staff member to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Staff member not found"
     *     )
     * )
     */
    public function getDetails ($id) {
        $foundItem = Staff::find($id);

        if ($foundItem) {
            return APIHelper::successResponse(statusCode: 200, message: "Get " . self::MODEL ." details with id::{$id} successfully!", data: $foundItem);
        }

        return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
    }

    /**
     * @OA\Delete(
     *     path="/staffs/{id}",
     *     summary="Delete a staff member",
     *     description="Remove a staff member from the system",
     *     operationId="deleteStaff",
     *     tags={"Staff"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the staff member to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Staff member deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Staff member not found"
     *     )
     * )
     */
    public function destroy ($id) {
        $foundItem = Staff::find($id);

        if ($foundItem) {
            Staff::destroy($id);

            return APIHelper::successResponse(statusCode: 200, message: "Remove " . self::MODEL . " with id::{$id} successfully!");
        }

        return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
    }
}