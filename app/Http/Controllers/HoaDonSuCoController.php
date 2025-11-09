<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HoaDonSuCo;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HoaDonSuCoController extends Controller
{
    // 📋 Danh sách hóa đơn sự cố
    public function index()
    {
        // Lấy tất cả hóa đơn sự cố, kèm quan hệ sinh viên, phòng, sự cố
        $hoaDons = HoaDonSuCo::with(['sinhVien', 'phong', 'suCo'])
            ->orderByDesc('id')
            ->get();

        return view('hoa_don_su_co.index', compact('hoaDons'));
    }

    // 💵 Xác nhận thanh toán hóa đơn sự cố
    public function thanhToan($id, Request $request)
    {
        $hoaDon = HoaDonSuCo::findOrFail($id);

        if ($hoaDon->status === 'Đã thanh toán') {
            // Nếu request gửi AJAX thì trả về JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hóa đơn này đã thanh toán rồi!'
                ]);
            }

            return redirect()->back()->with('info', 'Hóa đơn này đã thanh toán rồi!');
        }

        // Cập nhật trạng thái thanh toán
        $hoaDon->update([
            'status' => 'Đã thanh toán',
            'ngay_thanh_toan' => now(),
        ]);

        // Đồng bộ trạng thái thanh toán trong bảng sự cố
        if ($hoaDon->suCo) {
            $hoaDon->suCo->update(['is_paid' => true]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '✅ Thanh toán thành công!',
                'ngay_thanh_toan' => Carbon::parse($hoaDon->ngay_thanh_toan)->format('d/m/Y')
            ]);
        }

        return redirect()->back()->with('success', '✅ Xác nhận thanh toán thành công!');
    }
}
