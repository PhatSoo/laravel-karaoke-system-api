<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

use App\Helpers\APIHelper;
use App\Models\Permission;

class PermissionController extends Controller
{
    private const LIMIT = 5;
    private const CURRENT_PAGE = 1;
    private const MODEL = 'PERMISSION';

    public function listAll (Request $request) {
        $limit = $request->input('limit', self::LIMIT);
        $current_page = $request->input('page', self::CURRENT_PAGE);

        $offset = ($current_page - 1) * $limit;

        $allItems = Permission::orderBy('id', 'ASC');
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
        try {
            $validated = Validator::make($request->all(), [
                'name' => 'required|unique:permissions,name|string',
            ]);

            if ($validated->fails()) {
                return APIHelper::errorResponse(statusCode: 400, message: $validated->messages());
            }

            $createdNew = new Permission();
            $createdNew->fill($request->all());
            $createdNew->save();

            return APIHelper::successResponse(statusCode: 201, message: 'Create new ' . self::MODEL .' successfully!');
        } catch (\Throwable $th) {
            return APIHelper::errorResponse(message: $th->getMessage() . ' in line: ' . $th->getLine());
        }

    }

    public function update (Request $request, $id) {
        $foundItem = Permission::find($id);

        if (!$foundItem) {
            return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
        }

        $validated = Validator::make($request->all(), [
            'name' => 'required|unique:permissions,name|string'
        ]);

        // Check validate form required fields
        if ($validated->fails()) {
            return APIHelper::errorResponse(statusCode: 400, message: $validated->messages());
        }

        $foundItem->update($request->all());

        return APIHelper::successResponse(statusCode: 200, message: "Update " . self::MODEL . " with id::{$id} successfully!");
    }

    public function getDetails ($id) {
        try {
            $foundItem = Permission::find($id);

            if ($foundItem) {
                return APIHelper::successResponse(statusCode: 200, message: "Get " . self::MODEL ." details with id::{$id} successfully!", data: $foundItem);
            }

            return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
        } catch (\Throwable $th) {
            return APIHelper::errorResponse(message: $th->getMessage());
        }

    }

    public function destroy ($id) {
        $foundItem = Permission::find($id);

        if ($foundItem) {
            Permission::destroy($id);

            return APIHelper::successResponse(statusCode: 200, message: "Remove " . self::MODEL . " with id::{$id} successfully!");
        }

        return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
    }
}