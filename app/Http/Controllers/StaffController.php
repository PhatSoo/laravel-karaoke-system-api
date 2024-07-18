<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Staff;

class StaffController extends Controller
{
    private $LIMIT = 5;
    private $CURRENT_PAGE = 1;

    public function listAll (Request $request) {
        $limit = $request->input('limit', $this->LIMIT);
        $current_page = $request->input('page', $this->CURRENT_PAGE);

        $offset = ($current_page - 1) * $limit;

        $allRooms = Staff::orderBy('id', 'ASC');
        $total = $allRooms->count();

        $room = $allRooms->skip($offset)->take($limit)->get();

        return response()->json([
            'statusCode' => 200,
            'message' => 'Get all staffs successfully!',
            'pagination' => [
                'total' => $total,
                'current_page' => $current_page,
                'limit' => $limit
            ],
            'data' => $room
        ]);
    }

    public function create(Request $request) {
        $validated = Validator::make($request->all(), [
            'staff_name' => 'required|string',
            'role' => 'required|string|in:manager,receptionist,waiter',
            'phone' => 'required|unique:staffs,phone|string',
            'email' => 'required|unique:staffs,email|string|email',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'statusCode' => 400,
                'message' => $validated->messages()
            ]);
        }

        $createdNew = new Staff();
        $createdNew->fill($request->all());
        $createdNew->save();

        return response()->json([
            'statusCode' => 201,
            'message' => 'Create new staff successfully!'
        ]);
    }

    public function update (Request $request, $id) {
        $foundItem = Staff::find($id);

        if (!$foundItem) {
            return response()->json([
                'statusCode' => 404,
                'message' => "Staff with id::{$id} not found!"
            ]);
        }

        $validated = Validator::make($request->all(), [
            'staff_name' => 'string',
            'role' => 'string|in:manager,receptionist,waiter',
            'phone' => 'unique:staffs,phone|string',
            'email' => 'unique:staffs,email|string|email',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'statusCode' => 400,
                'message' => $validated->messages()
            ]);
        }

        $foundItem->update($request->all());

        return response()->json([
            'statusCode' => 204,
            'message' => "Update staff with id::{$id} successfully!"
        ]);
    }

    public function getDetails ($id) {
        $foundItem = Staff::find($id);

        if ($foundItem) {
            return response()->json([
                'statusCode' => 200,
                'message' => 'Get staff details successfully!',
                'data' => $foundItem
            ]);
        }

        return response()->json([
            'statusCode' => 404,
            'message' => "Staff details with id::{$id} not found!"
        ]);
    }

    public function destroy ($id) {
        $foundItem = Staff::find($id);

        if ($foundItem) {
            Staff::destroy($id);

            return response()->json([
                'statusCode' => 204,
                'message' => 'Delete staff successfully!',
            ]);
        }

        return response()->json([
            'statusCode' => 404,
            'message' => "Staff details with id::{$id} not found!"
        ]);
    }
}