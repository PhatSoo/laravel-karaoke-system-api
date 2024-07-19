<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Staff;
use App\Helpers\APIHelper;

class StaffController extends Controller
{
    private const LIMIT = 5;
    private const CURRENT_PAGE = 1;
    private const MODEL = 'CUSTOMER';

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

    public function create(Request $request) {
        $validated = Validator::make($request->all(), [
            'staff_name' => 'required|string',
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

    public function update (Request $request, $id) {
        $foundItem = Staff::find($id);

        if (!$foundItem) {
            return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
        }

        $validated = Validator::make($request->all(), [
            'staff_name' => 'string',
            'role' => 'string|in:manager,receptionist,waiter',
            'phone' => 'unique:staffs,phone|string',
            'email' => 'unique:staffs,email|string|email',
        ]);

        if ($validated->fails()) {
            return APIHelper::errorResponse(statusCode: 400, message: $validated->messages());
        }

        $foundItem->update($request->all());

        return APIHelper::successResponse(statusCode: 204, message: "Update " . self::MODEL . " with id::{$id} successfully!");
    }

    public function getDetails ($id) {
        $foundItem = Staff::find($id);

        if ($foundItem) {
            return APIHelper::successResponse(statusCode: 200, message: "Get " . self::MODEL ." details with id::{$id} successfully!", data: $foundItem);
        }

        return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
    }

    public function destroy ($id) {
        $foundItem = Staff::find($id);

        if ($foundItem) {
            Booking::destroy($id);

            return APIHelper::successResponse(statusCode: 204, message: "Update " . self::MODEL . " with id::{$id} successfully!");
        }

        return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
    }
}