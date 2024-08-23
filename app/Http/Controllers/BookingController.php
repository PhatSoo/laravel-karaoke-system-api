<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Booking;
use App\Helpers\APIHelper;

/**
 * @OA\Tag(
 *     name="Bookings",
 *     description="Manage Bookings"
 * )
*/

class BookingController extends Controller
{
    private const LIMIT = 5;
    private const CURRENT_PAGE = 1;
    private const MODEL = 'BOOKING';

    /**
     * @OA\Get(
     *      path="/api/booking",
     *      tags={"Booking"},
     *      summary="List all bookings",
     *      description="Retrieve a paginated list of all bookings.",
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="integer", default=10)
     *      ),
     *      @OA\Parameter(
     *          name="page",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="integer", default=1)
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="paginate", type="object",
     *                  @OA\Property(property="total", type="integer"),
     *                  @OA\Property(property="current_page", type="integer"),
     *                  @OA\Property(property="limit", type="integer")
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
    public function listAll(Request $request) {
        try {
            $limit = $request->input('limit', self::LIMIT);
            $current_page = $request->input('page', self::CURRENT_PAGE);

            $offset = ($current_page - 1) * $limit;

            $allItems = Booking::orderBy('id', 'ASC');
            $total = $allItems->count();

            $data = $allItems->skip($offset)->take($limit)->get();

            $paginate = [
                'total' => $total,
                'current_page' => $current_page,
                'limit' => $limit
            ];

            return APIHelper::successResponse(statusCode: 200, message: 'Get all ' . self::MODEL .' successfully!', data: $data, paginate: $paginate);
        } catch (\Throwable $th) {
            return APIHelper::errorResponse(message: $th->getMessage());
        }
    }

    /**
     * @OA\Post(
     *      path="/api/booking",
     *      tags={"Booking"},
     *      summary="Create a new booking",
     *      description="Create a new booking record.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"room_id","customer_id","start_time","status"},
     *              @OA\Property(property="room_id", type="integer", example=1),
     *              @OA\Property(property="customer_id", type="integer", example=1),
     *              @OA\Property(property="start_time", type="string", format="date-time", example="2024-08-21T15:00:00Z"),
     *              @OA\Property(property="end_time", type="string", format="date-time", example="2024-08-21T17:00:00Z"),
     *              @OA\Property(property="status", type="string", example="booked")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Booking created successfully",
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
    public function create(Request $request) {
        try {
            $validated = Validator::make($request->all(),[
                'room_id' => 'required|numeric|exists:rooms,id',
                'customer_id' => 'required|numeric|exists:customers,id',
                'start_time' => 'required|date',
                'end_time' => 'date',
                'status' => 'required|string|in:booked,completed,cancelled'
            ]);

            if ($validated->fails()) {
                return APIHelper::errorResponse(statusCode: 400, message: $validated->messages());
            }

            $createdNew = new Booking();
            $createdNew->fill($request->all());
            $createdNew->save();

            return APIHelper::successResponse(statusCode: 201, message: 'Create new ' . self::MODEL .' successfully!');
        } catch (\Throwable $th) {
            return APIHelper::errorResponse(message: $th->getMessage());
        }
    }

    /**
     * @OA\Put(
     *      path="/api/booking/{id}",
     *      tags={"Booking"},
     *      summary="Update a booking",
     *      description="Update an existing booking record.",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="room_id", type="integer", example=1),
     *              @OA\Property(property="customer_id", type="integer", example=1),
     *              @OA\Property(property="start_time", type="string", format="date-time", example="2024-08-21T15:00:00Z"),
     *              @OA\Property(property="end_time", type="string", format="date-time", example="2024-08-21T17:00:00Z"),
     *              @OA\Property(property="status", type="string", example="booked")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Booking updated successfully",
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
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Booking not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
    public function update(Request $request, $id) {
        $foundItem = Booking::find($id);

        if (!$foundItem) {
            return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
        }

        $validated = Validator::make($request->all(),[
            'room_id' => 'numeric|exists:rooms,id',
            'customer_id' => 'numeric|exists:customers,id',
            'start_time' => 'date',
            'end_time' => 'date',
            'status' => 'string|in:booked,completed,cancelled'
        ]);

        if ($validated->fails()) {
            return APIHelper::errorResponse(statusCode: 400, message: $validated->messages());
        }

        $foundItem->update($request->all());

        return APIHelper::successResponse(statusCode: 200, message: "Update " . self::MODEL . " with id::{$id} successfully!");
    }

    /**
     * @OA\Get(
     *      path="/api/booking/{id}",
     *      tags={"Booking"},
     *      summary="Get booking details",
     *      description="Retrieve the details of a specific booking.",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="data", type="object")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Booking not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
    public function getDetails($id) {
        $foundItem = Booking::find($id);

        if ($foundItem) {
            return APIHelper::successResponse(statusCode: 200, message: "Get " . self::MODEL ." details with id::{$id} successfully!", data: $foundItem);
        }

        return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
    }

    /**
     * @OA\Delete(
     *      path="/api/booking/{id}",
     *      tags={"Booking"},
     *      summary="Delete a booking",
     *      description="Remove a specific booking by its ID.",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Booking removed successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Booking not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
    public function destroy ($id) {
        $foundItem = Booking::find($id);

        if ($foundItem) {
            Booking::destroy($id);

            return APIHelper::successResponse(statusCode: 200, message: "Remove " . self::MODEL . " with id::{$id} successfully!");
        }

        return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
    }
}
