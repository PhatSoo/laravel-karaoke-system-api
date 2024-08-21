<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Helpers\APIHelper;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\InvoiceProduct;

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
            'booking_id' => 'required|numeric|exists:bookings,id',
            'staff_id' => 'required|numeric|exists:staffs,id',
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
            'booking_id' => 'numeric|exists:bookings,id',
            'staff_id' => 'numeric|exists:staffs,id',
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

    // Order Products
    public function orderDetails($id) {
        try {
            $foundItem = InvoiceProduct::where('invoice_id', $id)->get();

            if ($foundItem) {
                $data = [
                    "invoice_id" => $id,
                    "products" => $foundItem,
                ];
                return APIHelper::successResponse(statusCode: 200, message: "Get " . self::MODEL ." Order details with id::{$id} successfully!", data: $data);
            }

            return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
        } catch (\Throwable $th) {
            return APIHelper::errorResponse(message: $th->getMessage());
        }
    }

    public function order(Request $request, $id) {
        try {
            DB::beginTransaction();

            // Check exist invoice
            $foundItem = Invoice::find($id);
            if (is_null($foundItem)) {
                return APIHelper::errorResponse(statusCode: 404, message: 'INVOICE not found');
            }

            $data = $request->all();

            foreach($data as $order) {
                $product_id = $order['product_id'];
                $order_operator = $order['operator'] ?? null;

                $foundProduct = Product::find($product_id);
                if (is_null($foundProduct)) {
                    // Product not found
                    DB::rollBack();
                    return APIHelper::errorResponse(statusCode: 404, message: "PRODUCT with id::{$product_id} not found!");
                }
                // Check require quantity > inventory
                if (!$order_operator && $order['quantity'] > $foundProduct['inventory']) {
                    DB::rollBack();
                    return APIHelper::errorResponse(statusCode: 400, message: "PRODUCT with name::{$foundProduct->name} not enough to order!");
                }

                $row_existed = InvoiceProduct::where([['invoice_id', '=', $id], ['product_id', '=', $product_id]])->first();

                if ($row_existed) {
                    $result = $row_existed['quantity'];

                    if (!$order_operator) {
                        $result += $order['quantity'];
                        $foundProduct->inventory -= $order['quantity'];
                    }
                    else {
                        if ($order['quantity'] > $result) {
                            DB::rollBack();
                            return APIHelper::errorResponse(statusCode: 400, message: "PRODUCT with name::{$foundProduct->name} return too much!! Order Quantity: {$row_existed['quantity']}; Return Quantity: {$order['quantity']}");
                        }
                        $result -= $order['quantity'];
                        $foundProduct->inventory += $order['quantity'];
                    }

                    $row_existed->update(['quantity' => $result]);
                } else {
                    InvoiceProduct::create([
                        'invoice_id' => $id,
                        'product_id' => $product_id,
                        'quantity' => $order['quantity']
                    ]);

                    $foundProduct->inventory -= $order['quantity'];
                }
                // Change inventory in Product tables...
                $foundProduct->save();
            }

            DB::commit();

            return APIHelper::successResponse(message: 'Order Products successfully!');

        } catch (\Throwable $th) {
            DB::rollBack();
            return APIHelper::errorResponse(message: $th->getMessage() . ' in line: ' . $th->getLine());
        }
    }
}
