<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getUser(Request $request) {
        return $request->user();
    }
}
