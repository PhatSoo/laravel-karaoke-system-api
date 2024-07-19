<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Product;

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

        return response()->json([
            'statusCode' => 200,
            'message' => 'Get all '. self::MODEL . ' successfully!',
            'pagination' => [
                'total' => $total,
                'current_page' => $current_page,
                'limit' => $limit
            ],
            'data' => $data
        ]);
    }

    public function getDetails ($id) {
        $foundItem = Product::find($id);

        if ($foundItem) {
            return response()->json([
                'statusCode' => 200,
                'message' => 'Get ' . self::MODEL . ' details successfully!',
                'data' => $foundItem
            ]);
        }

        return response()->json([
            'statusCode' => 404,
            'message' => self::MODEL . " details with id::{$id} not found!"
        ]);
    }

    public function create(Request $request) {
        $validated = Validator::make($request->all(), [
            'name' => 'required|string|unique:products,name|max:100',
            'price' => 'required|decimal:2|min:0',
            'quantity' => 'required|numeric|min:0',
            'type' => 'required|string|in:drinks,foods,other'
        ]);

        if ($validated->fails()) {
            return response()->json([
                'statusCode' => 400,
                'message' => $validated->messages()
            ]);
        }

        $createdNew = new Product();
        $createdNew->fill($request->all());
        $createdNew->save();

        return response()->json([
            'statusCode' => 201,
            'message' => 'Create new ' . self::MODEL .' successfully!'
        ]);
    }

    public function update (Request $request, $id) {
        $foundItem = Product::find($id);

        if (!$foundItem) {
            return response()->json([
                'statusCode' => 404,
                'message' => self::MODEL . " with id::{$id} not found!"
            ]);
        }

        $validated = Validator::make($request->all(), [
            'name' => 'string|unique:products,name|max:100',
            'price' => 'decimal:2|min:0',
            'quantity' => 'numeric|min:0',
            'type' => 'string|in:drinks,foods,other'
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
            'message' => "Update " . self::MODEL ." with id::{$id} successfully!"
        ]);
    }

    public function destroy ($id) {
        $foundItem = Product::find($id);

        if ($foundItem) {
            Product::destroy($id);

            return response()->json([
                'statusCode' => 204,
                'message' => 'Delete ' . self::MODEL . ' successfully!',
            ]);
        }

        return response()->json([
            'statusCode' => 404,
            'message' => self::MODEL . " details with id::{$id} not found!"
        ]);
    }
}