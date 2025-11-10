<?php

namespace App\Http\Controllers;

use App\Models\SuCo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HoaDonSuCoController extends Controller
{
    /**
     * Danh sách hóa đơn sự cố (chỉ hiển thị các sự cố có payment_amount > 0)
     */
    public function index(Request $request)
    {
        // Chỉ lấy các sự cố có hóa đơn (payment_amount > 0)
        $query = SuCo::with([
            'sinhVien' => function($q) {
                $q->with(['phong.khu', 'slot.phong.khu']);
            },
            'phong.khu'
        ])->where('payment_amount', '>', 0);

        // 🔍 Tìm kiếm theo MSSV hoặc Họ tên
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->whereHas('sinhVien', function ($q) use ($search) {
                $q->whereRaw('LOWER(ho_ten) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(ma_sinh_vien) LIKE ?', ["%{$search}%"]);
            });
        }

        // 🔍 Lọc theo trạng thái thanh toán
        if ($request->filled('trang_thai_thanh_toan')) {
            if ($request->trang_thai_thanh_toan === 'da_thanh_toan') {
                $query->where('is_paid', true);
            } elseif ($request->trang_thai_thanh_toan === 'chua_thanh_toan') {
                $query->where('is_paid', false);
            }
        }

        // 🔍 Lọc theo khoảng thời gian (ngày gửi)
        if ($request->filled('date_from')) {
            $query->whereDate('ngay_gui', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('ngay_gui', '<=', $request->date_to);
        }

        // Sắp xếp: chưa thanh toán trước, sau đó mới đến đã thanh toán
        $query->orderByRaw('is_paid ASC, id DESC');

        $hoa_dons = $query->paginate(15);
        $hoa_dons->appends($request->all());

        // 📊 Thống kê
        $tong_hoa_don = SuCo::where('payment_amount', '>', 0)->count();
        $tong_tien = SuCo::where('payment_amount', '>', 0)->sum('payment_amount');
        $da_thanh_toan = SuCo::where('payment_amount', '>', 0)->where('is_paid', true)->count();
        $chua_thanh_toan = SuCo::where('payment_amount', '>', 0)->where('is_paid', false)->count();
        $tong_tien_da_thu = SuCo::where('payment_amount', '>', 0)->where('is_paid', true)->sum('payment_amount');
        $tong_tien_chua_thu = SuCo::where('payment_amount', '>', 0)->where('is_paid', false)->sum('payment_amount');

        return view('hoa_don_su_co.index', compact('hoa_dons', 'tong_hoa_don', 'tong_tien', 'da_thanh_toan', 'chua_thanh_toan', 'tong_tien_da_thu', 'tong_tien_chua_thu'));
    }

    /**
     * Xác nhận thanh toán hóa đơn sự cố
     */
    public function xacNhanThanhToan($id)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'nhanvien'])) {
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $suco = SuCo::findOrFail($id);

        if ($suco->payment_amount <= 0) {
            return redirect()->back()->with('error', 'Sự cố này không có hóa đơn!');
        }

        if ($suco->is_paid) {
            return redirect()->back()->with('info', 'Hóa đơn này đã được thanh toán rồi!');
        }

        $suco->update(['is_paid' => true]);

        return redirect()->route('hoadonsuco.index')
            ->with('success', '✅ Xác nhận thanh toán thành công! Số tiền: ' . number_format($suco->payment_amount, 0, ',', '.') . ' VNĐ');
    }

    /**
     * Hủy xác nhận thanh toán (nếu cần)
     */
    public function huyThanhToan($id)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin'])) {
            return redirect()->back()->with('error', 'Chỉ admin mới có quyền hủy thanh toán!');
        }

        $suco = SuCo::findOrFail($id);

        if (!$suco->is_paid) {
            return redirect()->back()->with('info', 'Hóa đơn này chưa được thanh toán!');
        }

        $suco->update(['is_paid' => false]);

        return redirect()->route('hoadonsuco.index')
            ->with('success', 'Đã hủy xác nhận thanh toán!');
    }

    /**
     * Thanh toán hóa đơn sự cố (cho sinh viên hoặc admin)
     */
    public function thanhToan(Request $request, $id)
    {
        $suco = SuCo::findOrFail($id);

        // Kiểm tra quyền: sinh viên chỉ được thanh toán hóa đơn của chính mình
        if (Auth::user()->role === 'sinhvien') {
            if ($suco->sinh_vien_id != Auth::user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền thanh toán hóa đơn này!'
                ], 403);
            }
        }

        if ($suco->payment_amount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Hóa đơn này không có số tiền cần thanh toán!'
            ], 400);
        }

        if ($suco->is_paid) {
            return response()->json([
                'success' => false,
                'message' => 'Hóa đơn này đã được thanh toán rồi!'
            ], 400);
        }

        // Validate dữ liệu
        $request->validate([
            'hinh_thuc_thanh_toan' => 'required|in:tien_mat,chuyen_khoan',
            'ghi_chu_thanh_toan' => 'nullable|string|max:500'
        ]);

        // Cập nhật thông tin thanh toán
        $suco->is_paid = true;
        $suco->ngay_thanh_toan = now();
        
        // Lưu thông tin thanh toán nếu có cột (có thể thêm migration sau)
        // $suco->hinh_thuc_thanh_toan = $request->hinh_thuc_thanh_toan;
        // $suco->ghi_chu_thanh_toan = $request->ghi_chu_thanh_toan;
        
        $suco->save();

        return response()->json([
            'success' => true,
            'message' => 'Thanh toán thành công! Số tiền: ' . number_format($suco->payment_amount, 0, ',', '.') . ' VNĐ'
        ]);
    }
}

