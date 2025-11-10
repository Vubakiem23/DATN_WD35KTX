<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ThongBaoKhuPhong;

class ThongBaoKhuPhongController extends Controller
{
    public function index()
{
    $thongbaos = ThongBaoKhuPhong::orderBy('created_at', 'desc')->paginate(10);

    return view('thongbao_khu_phong.index', compact('thongbaos'));
}
}

