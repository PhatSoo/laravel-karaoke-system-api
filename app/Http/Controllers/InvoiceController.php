<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Helpers\APIHelper;
use App\Models\Invoice;
use App\Models\Product;

/**
 * @OA\Tag(
 *     name="Invoices",
 *     description="API Endpoints of Invoices"
 * )
*/

class InvoiceController extends Controller
{
    private const LIMIT = 5;
    private const CURRENT_PAGE = 1;
    private const MODEL = 'INVOICE';

    /**
     * @OA\Get(
     *     path="/invoices",
     *     summary="Retrieve a list of invoices",
     *     description="Get all invoices with pagination",
     *     tags={"Invoices"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of records to return",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number to return",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved invoices",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request",
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/invoices",
     *     summary="Create a new invoice",
     *     description="Create a new invoice",
     *     tags={"Invoices"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="booking_id", type="integer", example=1),
     *             @OA\Property(property="staff_id", type="integer", example=1),
     *             @OA\Property(property="total_amount", type="number", format="decimal", example=100.00),
     *             @OA\Property(property="payment_status", type="string", enum={"pending", "paid", "cancelled"}, example="pending")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully created invoice",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request",
     *     )
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/invoices/{id}",
     *     summary="Update an existing invoice",
     *     description="Update an invoice by ID",
     *     tags={"Invoices"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Invoice ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="booking_id", type="integer", example=1),
     *             @OA\Property(property="staff_id", type="integer", example=1),
     *             @OA\Property(property="total_amount", type="number", format="decimal", example=150.00),
     *             @OA\Property(property="payment_status", type="string", enum={"pending", "paid", "cancelled"}, example="paid")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully updated invoice",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Invoice not found",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request",
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/invoices/{id}",
     *     summary="Get invoice details",
     *     description="Retrieve details of a specific invoice by ID",
     *     tags={"Invoices"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Invoice ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved invoice details",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Invoice not found",
     *     )
     * )
     */
    public function getDetails ($id) {
        $foundItem = Invoice::find($id);

        if ($foundItem) {
            return APIHelper::successResponse(statusCode: 200, message: "Get " . self::MODEL ." details with id::{$id} successfully!", data: $foundItem);
        }

        return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
    }

    /**
     * @OA\Delete(
     *     path="/invoices/{id}",
     *     summary="Delete an invoice",
     *     description="Delete an invoice by ID",
     *     tags={"Invoices"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Invoice ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully deleted invoice",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Invoice not found",
     *     )
     * )
     */
    public function destroy ($id) {
        $foundItem = Invoice::find($id);

        if ($foundItem) {
            Invoice::destroy($id);

            return APIHelper::successResponse(statusCode: 200, message: "Remove " . self::MODEL . " with id::{$id} successfully!");
        }

        return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
    }

    /**
     * @OA\Get(
     *     path="/invoices/{id}/order-details",
     *     summary="Get order details for an invoice",
     *     description="Retrieve product order details for a specific invoice by ID",
     *     tags={"Invoices"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Invoice ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved order details",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Invoice or products not found",
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/invoices/{id}/order",
     *     summary="Order products for an invoice",
     *     description="Order products by invoice ID",
     *     tags={"Invoices"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Invoice ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="product_id", type="integer", example=1),
     *                 @OA\Property(property="quantity", type="integer", example=2),
     *                 @OA\Property(property="operator", type="string", enum={"add", "subtract"}, example="add")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully ordered products",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Invoice or product not found",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request",
     *     )
     * )
     */
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