<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SinhVienController extends Controller
{
    public function index(Request $request)
    {
        return view('student');
    }
}
