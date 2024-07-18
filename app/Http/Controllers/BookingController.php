<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Models\Booking;

class BookingController extends Controller
{
    private $LIMIT = 5;
    private $CURRENT_PAGE = 1;

    public function listAll (Request $request) {
        $limit = $request->input('limit', $this->LIMIT);
        $current_page = $request->input('page', $this->CURRENT_PAGE);

        $offset = ($current_page - 1) * $limit;

        $allRooms = Booking::orderBy('id', 'ASC');
        $total = $allRooms->count();

        $room = $allRooms->skip($offset)->take($limit)->get();

        return response()->json([
            'statusCode' => 200,
            'message' => 'Get all bookings successfully!',
            'pagination' => [
                'total' => $total,
                'current_page' => $current_page,
                'limit' => $limit
            ],
            'data' => $room
        ]);
    }

    public function create(Request $request) {
        $validated = Validator::make($request->all(),[
            'room_id' => ['required', 'numeric', Rule::exists('rooms', 'id')],
            'customer_id' => ['required', 'numeric', Rule::exists('customers', 'id')],
            'start_time' => 'required|date',
            'end_time' => 'date',
            'status' => 'required|string|in:booked,completed,cancelled'
        ]);

        if ($validated->fails()) {
            return response()->json([
                'statusCode' => 400,
                'message' => $validated->messages()
            ]);
        }

        $createdNew = new Booking();
        $createdNew->fill($request->all());
        $createdNew->save();

        return response()->json([
            'statusCode' => 201,
            'message' => 'Create new booking successfully!'
        ]);
    }

    public function getDetails ($id) {
        $foundItem = Booking::find($id);

        if ($foundItem) {
            return response()->json([
                'statusCode' => 200,
                'message' => 'Get booking details successfully!',
                'data' => $foundItem
            ]);
        }

        return response()->json([
            'statusCode' => 404,
            'message' => "Booking details with id::{$id} not found!"
        ]);
    }
}
