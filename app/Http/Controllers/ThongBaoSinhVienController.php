<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ThongBaoSinhVien;

class ThongBaoSinhVienController extends Controller
{
    public function index()
    {
        // 1️⃣ Lấy thông báo sinh viên, kèm sinh viên, phân trang 10 bản ghi / trang
        $thongbaos = ThongBaoSinhVien::with('sinhVien')->latest()->paginate(10);

        // 2️⃣ Trả về view, truyền biến $thongbaos
        return view('thongbao_sinh_vien.index', compact('thongbaos'));
    }
}
