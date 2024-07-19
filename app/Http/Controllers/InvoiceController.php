<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Models\Invoice;
use App\Helpers\APIHelper;

class InvoiceController extends Controller
{
    private const LIMIT = 5;
    private const CURRENT_PAGE = 1;
    private const MODEL = 'INVOICE';

    public function listAll (Request $request) {
        $limit = $request->input('limit', self::LIMIT);
        $current_page = $request->input('page', self::CURRENT_PAGE);

        $offset = ($current_page - 1) * $limit;

        $allItems = Invoice::orderBy('id', 'ASC');
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
        $validated = Validator::make($request->all(),[
            'booking_id' => ['required', 'numeric', Rule::exists('bookings', 'id')],
            'staff_id' => ['required', 'numeric', Rule::exists('staffs', 'id')],
            'total_amount' => 'decimal:2',
            'payment_status' => 'string|in:pending,paid,cancelled'
        ]);

        if ($validated->fails()) {
            return APIHelper::errorResponse(statusCode: 400, message: $validated->messages());
        }

        $createdNew = new Invoice();
        $createdNew->fill($request->all());
        $createdNew->save();

        return APIHelper::successResponse(statusCode: 201, message: 'Create new ' . self::MODEL .' successfully!');
    }

    public function update (Request $request, $id) {
        $foundItem = Invoice::find($id);

        if (!$foundItem) {
            return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
        }

        $validated = Validator::make($request->all(),[
            'booking_id' => ['numeric', Rule::exists('bookings', 'id')],
            'staff_id' => ['numeric', Rule::exists('staffs', 'id')],
            'total_amount' => 'decimal:2',
            'payment_status' => 'string|in:pending,paid,cancelled'
        ]);

        if ($validated->fails()) {
            return APIHelper::errorResponse(statusCode: 400, message: $validated->messages());
        }

        $foundItem->update($request->all());

        return APIHelper::successResponse(statusCode: 200, message: "Update " . self::MODEL . " with id::{$id} successfully!");
    }

    public function getDetails ($id) {
        $foundItem = Invoice::find($id);

        if ($foundItem) {
            return APIHelper::successResponse(statusCode: 200, message: "Get " . self::MODEL ." details with id::{$id} successfully!", data: $foundItem);
        }

        return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
    }

    public function destroy ($id) {
        $foundItem = Invoice::find($id);

        if ($foundItem) {
            Invoice::destroy($id);

            return APIHelper::successResponse(statusCode: 200, message: "Remove " . self::MODEL . " with id::{$id} successfully!");
        }

        return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
    }
}
