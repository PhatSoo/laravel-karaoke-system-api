<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Models\Product;
use App\Helpers\APIHelper;

/**
 * @OA\Tag(
 *     name="Products",
 *     description="Manage Products"
 * )
*/

class ProductController extends Controller
{
    private const LIMIT = 5;
    private const CURRENT_PAGE = 1;
    private const MODEL = 'PRODUCT';

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Get all products with pagination",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Current page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Get all products successfully!",
     *         @OA\JsonContent(
     *             @OA\Property(property="paginate", type="object",
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="limit", type="integer")
     *             ),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="statusCode", type="integer")
     *         )
     *     )
     * )
     */
    public function listAll (Request $request) {
        $limit = $request->input('limit', self::LIMIT);
        $current_page = $request->input('page', self::CURRENT_PAGE);

        $offset = ($current_page - 1) * $limit;

        $allItems = Product::orderBy('id', 'ASC');
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
     *     path="/api/products",
     *     summary="Create a new product",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Coca Cola"),
     *             @OA\Property(property="price", type="number", format="decimal", example="10.99"),
     *             @OA\Property(property="inventory", type="integer", example="100"),
     *             @OA\Property(property="type", type="string", example="drinks")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Create new product successfully!",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="statusCode", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="statusCode", type="integer")
     *         )
     *     )
     * )
     */
    public function create(Request $request) {
        $validated = Validator::make($request->all(), [
            'name' => 'required|string|unique:products,name|max:100',
            'price' => 'required|decimal:2|min:0',
            'inventory' => 'numeric|min:0',
            'type' => 'required|string|in:drinks,foods,other'
        ]);

        if ($validated->fails()) {
            return APIHelper::errorResponse(statusCode: 400, message: $validated->messages());
        }

        $createdNew = new Product();
        $createdNew->fill($request->all());
        $createdNew->save();

        return APIHelper::successResponse(statusCode: 201, message: 'Create new ' . self::MODEL .' successfully!');
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Update an existing product",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Coca Cola"),
     *             @OA\Property(property="price", type="number", format="decimal", example="12.99"),
     *             @OA\Property(property="inventory", type="integer", example="200"),
     *             @OA\Property(property="type", type="string", example="drinks")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Update product successfully!",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="statusCode", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="statusCode", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="statusCode", type="integer")
     *         )
     *     )
     * )
     */
    public function update (Request $request, $id) {
        $foundItem = Product::find($id);

        if (!$foundItem) {
            return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
        }

        $request->only(['name', 'price', 'type']);

        $validated = Validator::make($request->all(), [
            'name' => 'string|unique:products,name|max:100',
            'price' => 'decimal:2|min:0',
            'type' => 'string|in:drinks,foods,other'
        ]);

        if ($validated->fails()) {
            return APIHelper::errorResponse(statusCode: 400, message: $validated->messages());
        }

        $foundItem->update($request->all());

        return APIHelper::successResponse(statusCode: 200, message: "Update " . self::MODEL . " with id::{$id} successfully!");
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Get product details",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Get product details successfully!",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="statusCode", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="statusCode", type="integer")
     *         )
     *     )
     * )
     */
    public function getDetails ($id) {
        $foundItem = Product::find($id);

        if ($foundItem) {
            return APIHelper::successResponse(statusCode: 200, message: "Get " . self::MODEL ." details with id::{$id} successfully!", data: $foundItem);
        }

        return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Delete a product",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Remove product successfully!",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="statusCode", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="statusCode", type="integer")
     *         )
     *     )
     * )
     */
    public function destroy ($id) {
        $foundItem = Product::find($id);

        if ($foundItem) {
            Product::destroy($id);

            return APIHelper::successResponse(statusCode: 200, message: "Remove " . self::MODEL . " with id::{$id} successfully!");
        }

        return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
    }

    /**
     * @OA\Get(
     *     path="/api/products/alerts",
     *     summary="Get products with inventory alerts",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="alert",
     *         in="query",
     *         description="Inventory alert threshold",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Get all out of stock alert products successfully!",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="statusCode", type="integer")
     *         )
     *     )
     * )
     */
    public function getProductsAlert(Request $request) {
        try {
            $limit_alert = $request->input('alert', 10);
            $data = Product::where('inventory', '<', $limit_alert)->get();

            return APIHelper::successResponse(statusCode: 200, data: $data, message: "Get all OUT OF STOCK ALERT PRODUCTS successfully!");
        } catch (\Throwable $th) {
            return APIHelper::errorResponse(message: $th->getMessage() . ' in line: ' . $th->getLine());
        }
    }

     /**
     * @OA\Post(
     *     path="/api/products/import",
     *     summary="Import product inventory",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="inventory", type="integer", example=50)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Update product inventory successfully!",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="statusCode", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="statusCode", type="integer")
     *         )
     *     )
     * )
     */
    public function importProducts(Request $request) {
        try {
            DB::beginTransaction();
            $data = $request->all();

            if (count($data) === 0) {
                DB::rollBack();
                return APIHelper::errorResponse(statusCode: 400, message: "Error occurred!");
            }

            foreach ($data as $product) {
                $validated = Validator::make($product, [
                    'id' => 'required|exists:products,id',
                    'inventory' => 'required|numeric|min:1'
                ]);

                if ($validated->fails()) {
                    DB::rollBack();
                    return APIHelper::errorResponse(statusCode: 404, message: $validated->messages());
                }

                $id = $product['id'];
                $foundProduct = Product::find($id);

                $foundProduct->inventory += $product['inventory'];
                $foundProduct->save();
            }

            DB::commit();
            return APIHelper::successResponse(statusCode: 200, message: "Update PRODUCT inventory successfully!");
        } catch (\Throwable $th) {
            DB::rollBack();
            return APIHelper::errorResponse(message: $th->getMessage() . ' in line: ' . $th->getLine());
        }
    }
}
