<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Booking;
use App\Helpers\APIHelper;

class BookingController extends Controller
{
    private const LIMIT = 5;
    private const CURRENT_PAGE = 1;
    private const MODEL = 'BOOKING';

    public function listAll (Request $request) {
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

    public function update (Request $request, $id) {
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

    public function getDetails ($id) {
        $foundItem = Booking::find($id);

        if ($foundItem) {
            return APIHelper::successResponse(statusCode: 200, message: "Get " . self::MODEL ." details with id::{$id} successfully!", data: $foundItem);
        }

        return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
    }

    public function destroy ($id) {
        $foundItem = Booking::find($id);

        if ($foundItem) {
            Booking::destroy($id);

            return APIHelper::successResponse(statusCode: 200, message: "Remove " . self::MODEL . " with id::{$id} successfully!");
        }

        return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
    }
}
