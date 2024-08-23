<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Song;
use App\Helpers\APIHelper;

/**
 * @OA\Tag(
 *     name="Songs",
 *     description="Manage Songs"
 * )
*/

class SongController extends Controller
{
    private const LIMIT = 5;
    private const CURRENT_PAGE = 1;
    private const MODEL = 'SONG';

    /**
     * @OA\Get(
     *     path="/songs",
     *     summary="Get all songs",
     *     description="Retrieve a paginated list of all songs",
     *     operationId="listAllSongs",
     *     tags={"Songs"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of songs to return per page",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number to retrieve",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="total", type="integer"),
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="limit", type="integer"),
     *         )
     *     )
     * )
     */
    public function listAll (Request $request) {
        $limit = $request->input('limit', self::LIMIT);
        $current_page = $request->input('page', self::CURRENT_PAGE);

        $offset = ($current_page - 1) * $limit;

        $allItems = Song::orderBy('id', 'ASC');
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
     *     path="/songs",
     *     summary="Create a new song",
     *     description="Add a new song to the system",
     *     operationId="createSong",
     *     tags={"Songs"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="artist", type="string"),
     *             @OA\Property(property="duration", type="number", format="decimal")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Song created successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     )
     * )
     */
    public function create(Request $request) {
        $validated = Validator::make($request->all(), [
            'title' => 'required|string',
            'artist' => 'required|string',
            'duration' => 'required|decimal:2|min:1|max:10',
        ]);

        if ($validated->fails()) {
            return APIHelper::errorResponse(statusCode: 400, message: $validated->messages());
        }

        $createdNew = new Song();
        $createdNew->fill($request->all());
        $createdNew->save();

        return APIHelper::successResponse(statusCode: 201, message: 'Create new ' . self::MODEL .' successfully!');
    }

    /**
     * @OA\Put(
     *     path="/songs/{id}",
     *     summary="Update an existing song",
     *     description="Update details of an existing song",
     *     operationId="updateSong",
     *     tags={"Songs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the song to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="artist", type="string"),
     *             @OA\Property(property="duration", type="number", format="decimal")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Song updated successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Song not found"
     *     )
     * )
     */
    public function update (Request $request, $id) {
        $foundItem = Song::find($id);

        if (!$foundItem) {
            return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
        }

        $validated = Validator::make($request->all(), [
            'title' => 'string',
            'artist' => 'string',
            'duration' => 'decimal:2|min:1|max:10',
        ]);

        if ($validated->fails()) {
            return APIHelper::errorResponse(statusCode: 400, message: $validated->messages());
        }

        $foundItem->update($request->all());

        return APIHelper::successResponse(statusCode: 200, message: "Update " . self::MODEL . " with id::{$id} successfully!");
    }

    /**
     * @OA\Get(
     *     path="/songs/{id}",
     *     summary="Get song details",
     *     description="Retrieve details of a specific song",
     *     operationId="getSongDetails",
     *     tags={"Songs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the song to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Song not found"
     *     )
     * )
     */
    public function getDetails ($id) {
        $foundItem = Song::find($id);

        if ($foundItem) {
            return APIHelper::successResponse(statusCode: 200, message: "Get " . self::MODEL ." details with id::{$id} successfully!", data: $foundItem);
        }

        return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
    }

    /**
     * @OA\Delete(
     *     path="/songs/{id}",
     *     summary="Delete a song",
     *     description="Remove a song from the system",
     *     operationId="deleteSong",
     *     tags={"Songs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the song to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Song deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Song not found"
     *     )
     * )
     */
    public function destroy ($id) {
        $foundItem = Song::find($id);

        if ($foundItem) {
            Song::destroy($id);

            return APIHelper::successResponse(statusCode: 200, message: "Remove " . self::MODEL . " with id::{$id} successfully!");
        }

        return APIHelper::errorResponse(statusCode: 404, message: self::MODEL . " with id::{$id} not found!");
    }
}
