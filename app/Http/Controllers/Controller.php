<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

abstract class Controller
{
    protected function getAllTablesName() {
        return array_map('current',DB::select('SHOW TABLES'));
    }
}
