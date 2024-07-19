<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Song;

class SongController extends Controller
{
    private $LIMIT = 5;
    private $CURRENT_PAGE = 1;

    public function listAll (Request $request) {
        $limit = $request->input('limit', $this->LIMIT);
        $current_page = $request->input('page', $this->CURRENT_PAGE);

        $offset = ($current_page - 1) * $limit;

        $allItems = Song::orderBy('id', 'ASC');
        $total = $allItems->count();

        $data = $allItems->skip($offset)->take($limit)->get();

        return response()->json([
            'statusCode' => 200,
            'message' => 'Get all songs successfully!',
            'pagination' => [
                'total' => $total,
                'current_page' => $current_page,
                'limit' => $limit
            ],
            'data' => $data
        ]);
    }

    public function create(Request $request) {
        $validated = Validator::make($request->all(), [
            'title' => 'required|string',
            'artist' => 'required|string',
            'duration' => 'required|decimal:2|min:1|max:10',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'statusCode' => 400,
                'message' => $validated->messages()
            ]);
        }

        $createdNew = new Song();
        $createdNew->fill($request->all());
        $createdNew->save();

        return response()->json([
            'statusCode' => 201,
            'message' => 'Create new song successfully!'
        ]);
    }

    public function update (Request $request, $id) {
        $foundItem = Song::find($id);

        if (!$foundItem) {
            return response()->json([
                'statusCode' => 404,
                'message' => "Song with id::{$id} not found!"
            ]);
        }

        $validated = Validator::make($request->all(), [
            'title' => 'string',
            'artist' => 'string',
            'duration' => 'decimal:2|min:1|max:10',
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
            'message' => "Update song with id::{$id} successfully!"
        ]);
    }

    public function getDetails ($id) {
        $foundItem = Song::find($id);

        if ($foundItem) {
            return response()->json([
                'statusCode' => 200,
                'message' => 'Get song details successfully!',
                'data' => $foundItem
            ]);
        }

        return response()->json([
            'statusCode' => 404,
            'message' => "Song details with id::{$id} not found!"
        ]);
    }

    public function destroy ($id) {
        $foundItem = Song::find($id);

        if ($foundItem) {
            Song::destroy($id);

            return response()->json([
                'statusCode' => 204,
                'message' => 'Delete song successfully!',
            ]);
        }

        return response()->json([
            'statusCode' => 404,
            'message' => "Song details with id::{$id} not found!"
        ]);
    }
}