<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Models\Product;
use App\Helpers\APIHelper;

class ProductController extends Controller
{
    private const LIMIT = 5;
    private const CURRENT_PAGE = 1;
    private const MODEL = 'PRODUCT';

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

    public function getDetails ($id) {
        $foundItem = Product::find($id);

        if ($foundItem) {
            return APIHelper::successResponse(statusCode: 200, message: "Get " . self::MODEL ." details with id::{$id} successfully!", data: $foundItem);
        }

        return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
    }

    public function destroy ($id) {
        $foundItem = Product::find($id);

        if ($foundItem) {
            Product::destroy($id);

            return APIHelper::successResponse(statusCode: 200, message: "Remove " . self::MODEL . " with id::{$id} successfully!");
        }

        return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
    }

    public function getProductsAlert(Request $request) {
        try {
            $limit_alert = $request->input('alert', 10);
            $data = Product::where('inventory', '<', $limit_alert)->get();

            return APIHelper::successResponse(statusCode: 200, data: $data, message: "Get all OUT OF STOCK ALERT PRODUCTS successfully!");
        } catch (\Throwable $th) {
            return APIHelper::errorResponse(message: $th->getMessage() . ' in line: ' . $th->getLine());
        }
    }

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