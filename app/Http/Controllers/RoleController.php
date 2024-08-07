<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Helpers\APIHelper;
use App\Models\Role;
use App\Models\Permission;

class RoleController extends Controller
{
    private const LIMIT = 5;
    private const CURRENT_PAGE = 1;
    private const MODEL = 'ROLE';

    public function listAll (Request $request) {
        $limit = $request->input('limit', self::LIMIT);
        $current_page = $request->input('page', self::CURRENT_PAGE);

        $offset = ($current_page - 1) * $limit;

        $allItems = Role::orderBy('id', 'ASC');
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
            'name' => 'required|unique:roles,name|string',
        ]);

        if ($validated->fails()) {
            return APIHelper::errorResponse(statusCode: 400, message: $validated->messages());
        }

        $createdNew = new Role();
        $createdNew->fill($request->all());
        $createdNew->save();

        return APIHelper::successResponse(statusCode: 201, message: 'Create new ' . self::MODEL .' successfully!');
    }

    public function update (Request $request, $id) {
        $foundItem = Role::find($id);

        if (!$foundItem) {
            return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
        }

        $validated = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name|string'
        ]);

        // Check validate form required fields
        if ($validated->fails()) {
            return APIHelper::errorResponse(statusCode: 400, message: $validated->messages());
        }

        $foundItem->update($request->all());

        return APIHelper::successResponse(statusCode: 200, message: "Update " . self::MODEL . " with id::{$id} successfully!");
    }

    public function getDetails ($id) {
        $foundItem = Role::find($id);

        if ($foundItem) {
            return APIHelper::successResponse(statusCode: 200, message: "Get " . self::MODEL ." details with id::{$id} successfully!", data: $foundItem);
        }

        return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
    }

    public function destroy ($id) {
        $foundItem = Role::find($id);

        if ($foundItem) {
            Role::destroy($id);

            return APIHelper::successResponse(statusCode: 200, message: "Remove " . self::MODEL . " with id::{$id} successfully!");
        }

        return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
    }

    public function showPermissions() {
        $data = Role::with('permissions')->get();
        return APIHelper::successResponse(statusCode: 200, message: 'Get all ' . self::MODEL .' successfully!', data: $data);
    }

    public function decentralize(Request $request) {
        try {
            DB::beginTransaction();
            $data = $request->all();
            // example data
            /*
            {
                role_key_1: [
                    permission_key_1,
                    permission_key_2
                ],
                role_key_2: [...]
            }
            */

            foreach ($data as $role_key => $value) {
                $existed_role = Role::find($role_key);
                if (is_null($existed_role)) {
                    DB::rollBack();
                    return APIHelper::errorResponse(statusCode: 404, message: "ROLE::{$role_key} not found");
                }

                if (is_array($value)) {
                    $existing_permissions = Permission::whereIn('key', $value)->pluck('key')->toArray();
                    $missing_roles = array_diff($value, $existing_permissions);
                    if (!empty($missing_roles)) {
                        DB::rollBack();
                        return APIHelper::errorResponse(statusCode: 400, message: "Permission Key::". implode(',', $missing_roles) ." does not exist!");
                    }

                    $existed_role->permissions()->sync($value);
                }
            }
            DB::commit();
            // All insert roles success
            return APIHelper::successResponse(message: 'Update Permissions for Role successfully!');
        } catch (\Throwable $th) {
            DB::rollBack();
            return APIHelper::errorResponse(message: $th->getMessage());
        }
    }
}
