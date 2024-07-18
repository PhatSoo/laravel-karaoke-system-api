<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Customer;

class CustomerController extends Controller
{
    private $LIMIT = 5;
    private $CURRENT_PAGE = 1;

    public function listAll (Request $request) {
        $limit = $request->input('limit', $this->LIMIT);
        $current_page = $request->input('page', $this->CURRENT_PAGE);

        $offset = ($current_page - 1) * $limit;

        $allRooms = Customer::orderBy('id', 'ASC');
        $total = $allRooms->count();

        $room = $allRooms->skip($offset)->take($limit)->get();

        return response()->json([
            'statusCode' => 200,
            'message' => 'Get all customers successfully!',
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
            'customer_name' => 'required',
            'customer_phone' => 'required|unique:customers,customer_phone|string',
            'customer_email' => 'required|unique:customers|string|email',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'statusCode' => 400,
                'message' => $validated->messages()
            ]);
        }

        $createdNew = new Customer();
        $createdNew->fill($request->all());
        $createdNew->save();

        return response()->json([
            'statusCode' => 201,
            'message' => 'Create new customer successfully!'
        ]);
    }

    public function getDetails ($id) {
        $foundItem = Customer::find($id);

        if ($foundItem) {
            return response()->json([
                'statusCode' => 200,
                'message' => 'Get customer details successfully!',
                'data' => $foundItem
            ]);
        }

        return response()->json([
            'statusCode' => 404,
            'message' => "Customer details with id::{$id} not found!"
        ]);
    }

    public function update (Request $request, $id) {
        $foundItem = Customer::find($id);

        if (!$foundItem) {
            return response()->json([
                'statusCode' => 404,
                'message' => "Customer with id::{$id} not found!"
            ]);
        }

        $validated = Validator::make($request->all(), [
            'customer_name' => 'string',
            'customer_phone' => 'unique:customers,customer_phone|string',
            'customer_email' => 'unique:customers|string|email',
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
            'message' => "Update customer with id::{$id} successfully!"
        ]);
    }

    public function destroy ($id) {
        $foundItem = Customer::find($id);

        if ($foundItem) {
            Customer::destroy($id);

            return response()->json([
                'statusCode' => 204,
                'message' => 'Delete customer successfully!',
            ]);
        }

        return response()->json([
            'statusCode' => 404,
            'message' => "Customer details with id::{$id} not found!"
        ]);
    }
}
