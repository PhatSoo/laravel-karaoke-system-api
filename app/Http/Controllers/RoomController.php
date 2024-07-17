<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
            'status' => 200,
            'message' => 'Get all rooms successfully!',
            'pagination' => [
                'total' => $total,
                'current_page' => $current_page,
                'limit' => $limit
            ],
            'data' => $room
        ]);
    }

    public function create (Request $request) {
        $validated = $request->validate([
            'room_name' => 'required|unique:rooms|max:100',
            'capacity' => 'required|numeric|min:0|max:20',
            'price_per_hour' => 'required|decimal:2',
            'status',
        ]);
    }
}