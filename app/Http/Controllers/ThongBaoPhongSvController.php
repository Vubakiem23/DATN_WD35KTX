<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ThongBaoPhongSv;

class ThongBaoPhongSvController extends Controller
{
    public function index()
    {
        $thongbaos = ThongBaoPhongSv::with(['sinhVien', 'phong'])
                ->latest()
                ->paginate(10); // số bản ghi mỗi trang

        return view('thongbao_phong_sv.index', compact('thongbaos'));
    }
}
