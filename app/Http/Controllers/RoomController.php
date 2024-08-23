<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Room;
use App\Helpers\APIHelper;

/**
 * @OA\Tag(
 *     name="Rooms",
 *     description="Manage Permissions"
 * )
*/

class RoomController extends Controller
{
    private const LIMIT = 5;
    private const CURRENT_PAGE = 1;
    private const MODEL = 'ROOM';

    /**
     * @OA\Get(
     *     path="/rooms",
     *     summary="Get all rooms",
     *     description="Retrieve a paginated list of all rooms",
     *     operationId="listAllRooms",
     *     tags={"Rooms"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of rooms to return per page",
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

        $allItems = Room::orderBy('id', 'ASC');
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
     *     path="/rooms",
     *     summary="Create a new room",
     *     description="Add a new room to the system",
     *     operationId="createRoom",
     *     tags={"Rooms"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="capacity", type="integer"),
     *             @OA\Property(property="price_per_hour", type="number", format="decimal"),
     *             @OA\Property(property="status", type="string", enum={"available", "occupied", "maintenance"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Room created successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     )
     * )
     */
    public function create(Request $request) {
        $validated = Validator::make($request->all(), [
            'name' => 'required|unique:rooms,name|max:100',
            'capacity' => 'required|numeric|min:0|max:20',
            'price_per_hour' => 'required|decimal:2|min:10',
            'status' => 'required|string|in:available,occupied,maintenance',
        ]);

        if ($validated->fails()) {
            return APIHelper::errorResponse(statusCode: 400, message: $validated->messages());
        }

        $createdNew = new Room();
        $createdNew->fill($request->all());
        $createdNew->save();

        return APIHelper::successResponse(statusCode: 201, message: 'Create new ' . self::MODEL .' successfully!');
    }

    /**
     * @OA\Put(
     *     path="/rooms/{id}",
     *     summary="Update an existing room",
     *     description="Update details of an existing room",
     *     operationId="updateRoom",
     *     tags={"Rooms"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the room to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="capacity", type="integer"),
     *             @OA\Property(property="price_per_hour", type="number", format="decimal"),
     *             @OA\Property(property="status", type="string", enum={"available", "occupied", "maintenance"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Room updated successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Room not found"
     *     )
     * )
     */
    public function update (Request $request, $id) {
        $foundItem = Room::find($id);

        if (!$foundItem) {
            return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
        }

        $validated = Validator::make($request->all(), [
            'name' => 'unique:rooms,name|max:100',
            'capacity' => 'numeric|min:0|max:20',
            'price_per_hour' => 'decimal:2|min:10',
            'status' => 'string|in:available,occupied,maintenance',
        ]);

        if ($validated->fails()) {
            return APIHelper::errorResponse(statusCode: 400, message: $validated->messages());
        }

        $foundItem->update($request->all());

        return APIHelper::successResponse(statusCode: 200, message: "Update " . self::MODEL . " with id::{$id} successfully!");
    }

    /**
     * @OA\Get(
     *     path="/rooms/{id}",
     *     summary="Get room details",
     *     description="Retrieve details of a specific room",
     *     operationId="getRoomDetails",
     *     tags={"Rooms"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the room to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Room not found"
     *     )
     * )
     */
    public function getDetails ($id) {
        $foundItem = Room::find($id);

        if ($foundItem) {
            return APIHelper::successResponse(statusCode: 200, message: "Get " . self::MODEL ." details with id::{$id} successfully!", data: $foundItem);
        }

        return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
    }

    /**
     * @OA\Delete(
     *     path="/rooms/{id}",
     *     summary="Delete a room",
     *     description="Remove a room from the system",
     *     operationId="deleteRoom",
     *     tags={"Rooms"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the room to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Room deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Room not found"
     *     )
     * )
     */
    public function destroy ($id) {
        $foundItem = Room::find($id);

        if ($foundItem) {
            Room::destroy($id);

            return APIHelper::successResponse(statusCode: 200, message: "Remove " . self::MODEL . " with id::{$id} successfully!");
        }

        return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
    }
}
