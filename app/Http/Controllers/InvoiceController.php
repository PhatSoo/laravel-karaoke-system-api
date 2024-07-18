<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Models\Invoice;

class InvoiceController extends Controller
{
    private $LIMIT = 5;
    private $CURRENT_PAGE = 1;

    public function listAll (Request $request) {
        $limit = $request->input('limit', $this->LIMIT);
        $current_page = $request->input('page', $this->CURRENT_PAGE);

        $offset = ($current_page - 1) * $limit;

        $allRooms = Invoice::orderBy('id', 'ASC');
        $total = $allRooms->count();

        $room = $allRooms->skip($offset)->take($limit)->get();

        return response()->json([
            'statusCode' => 200,
            'message' => 'Get all invoices successfully!',
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
            'booking_id' => ['required', 'numeric', Rule::exists('bookings', 'id')],
            'staff_id' => ['required', 'numeric', Rule::exists('staffs', 'id')],
            'total_amount' => 'decimal:2',
            'payment_status' => 'string|in:pending,paid,cancelled'
        ]);

        if ($validated->fails()) {
            return response()->json([
                'statusCode' => 400,
                'message' => $validated->messages()
            ]);
        }

        $createdNew = new Invoice();
        $createdNew->fill($request->all());
        $createdNew->save();

        return response()->json([
            'statusCode' => 201,
            'message' => 'Create new invoice successfully!'
        ]);
    }

    public function update (Request $request, $id) {
        $foundItem = Invoice::find($id);

        if (!$foundItem) {
            return response()->json([
                'statusCode' => 404,
                'message' => "Invoice with id::{$id} not found!"
            ]);
        }

        $validated = Validator::make($request->all(),[
            'booking_id' => ['numeric', Rule::exists('bookings', 'id')],
            'staff_id' => ['numeric', Rule::exists('staffs', 'id')],
            'total_amount' => 'decimal:2',
            'payment_status' => 'string|in:pending,paid,cancelled'
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
            'message' => "Update invoice with id::{$id} successfully!"
        ]);
    }

    public function getDetails ($id) {
        $foundItem = Invoice::find($id);

        if ($foundItem) {
            return response()->json([
                'statusCode' => 200,
                'message' => 'Get invoice details successfully!',
                'data' => $foundItem
            ]);
        }

        return response()->json([
            'statusCode' => 404,
            'message' => "Invoice details with id::{$id} not found!"
        ]);
    }

    public function destroy ($id) {
        $foundItem = Invoice::find($id);

        if ($foundItem) {
            Invoice::destroy($id);

            return response()->json([
                'statusCode' => 204,
                'message' => 'Delete invoice successfully!',
            ]);
        }

        return response()->json([
            'statusCode' => 404,
            'message' => "Invoice details with id::{$id} not found!"
        ]);
    }
}