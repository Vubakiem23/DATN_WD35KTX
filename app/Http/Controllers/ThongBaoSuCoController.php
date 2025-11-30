<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ThongBaoSuCo;

class ThongBaoSuCoController extends Controller
{
    public function index()
    {
        // Lấy tất cả thông báo kèm quan hệ su_co, phân trang 10 bản ghi/trang
        $thongbaos = ThongBaoSuCo::with('su_co.sinhVien')
                        ->orderByDesc('ngay_tao')
                        ->paginate(10);

        return view('thongbao_su_co.index', compact('thongbaos'));
    }
}