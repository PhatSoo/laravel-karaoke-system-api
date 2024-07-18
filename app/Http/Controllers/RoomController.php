<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Room;

class RoomController extends Controller
{
    private $LIMIT = 5;
    private $CURRENT_PAGE = 1;

    public function listAll (Request $request) {
        $limit = $request->input('limit', $this->LIMIT);
        $current_page = $request->input('page', $this->CURRENT_PAGE);

        $offset = ($current_page - 1) * $limit;

        $allRooms = Room::orderBy('id', 'ASC');
        $total = $allRooms->count();

        $room = $allRooms->skip($offset)->take($limit)->get();

        return response()->json([
            'statusCode' => 200,
            'message' => 'Get all rooms successfully!',
            'pagination' => [
                'total' => $total,
                'current_page' => $current_page,
                'limit' => $limit
            ],
            'data' => $room
        ]);
    }

    public function getDetails ($id) {
        $foundItem = Room::find($id);

        if ($foundItem) {
            return response()->json([
                'statusCode' => 200,
                'message' => 'Get room details successfully!',
                'data' => $foundItem
            ]);
        }

        return response()->json([
            'statusCode' => 404,
            'message' => "Room details with id::{$id} not found!"
        ]);
    }

    public function create(Request $request) {
        $validated = Validator::make($request->all(), [
            'room_name' => 'required|unique:rooms,room_name|max:100',
            'capacity' => 'required|numeric|min:0|max:20',
            'price_per_hour' => 'required|decimal:2|min:10',
            'status' => 'required|string|in:available,occupied,maintenance',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'statusCode' => 400,
                'message' => $validated->messages()
            ]);
        }

        $createdNew = new Room();
        $createdNew->fill($request->all());
        $createdNew->save();

        return response()->json([
            'statusCode' => 201,
            'message' => 'Create new room successfully!'
        ]);
    }
}