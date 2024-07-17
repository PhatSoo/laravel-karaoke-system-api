<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Invoice;

class InvoiceController extends Controller
{
    private $LIMIT = 5;
    private $CURRENT_PAGE = 1;

    public function listAll (Request $request) {
        $limit = $request->input('limit', $this->LIMIT);
        $current_page = $request->input('page', $this->CURRENT_PAGE);

        $offset = ($current_page - 1) * $limit;

        $allRooms = Invoice::orderBy('id', 'ASC')->with(['staff', 'booking']);
        $total = $allRooms->count();

        $room = $allRooms->skip($offset)->take($limit)->get();

        return response()->json([
            'status' => 200,
            'message' => 'Get all invoices successfully!',
            'pagination' => [
                'total' => $total,
                'current_page' => $current_page,
                'limit' => $limit
            ],
            'data' => $room
        ]);
    }
}