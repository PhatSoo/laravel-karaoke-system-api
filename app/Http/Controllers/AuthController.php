<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request) {
        // $token = $request->user()->createToken($request->username);
        return $request;
    }

    public function login(Request $request) {
        dd(Auth::attempt($request->all()));
        return $request;
    }
}