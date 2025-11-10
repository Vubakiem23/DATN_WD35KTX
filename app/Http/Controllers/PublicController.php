<?php

namespace App\Http\Controllers;

use App\Models\ThongBao;
use App\Models\SinhVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicController extends Controller
{
    /**
     * Trang chủ công khai
     */
    public function home()
    {
        // Cho phép truy cập trang công khai kể cả khi đã đăng nhập
        try {
            // Lấy thông báo mới nhất (5 thông báo)
            $thongBaos = ThongBao::with('tieuDe')
                ->orderBy('ngay_dang', 'desc')
                ->limit(5)
                ->get();
            
            // Lấy tin tức (tạm thời sử dụng ThongBao làm placeholder cho tin tức)
            $tinTuc = ThongBao::with('tieuDe')
                ->orderBy('ngay_dang', 'desc')
                ->limit(3)
                ->get();
            // Quyền hiển thị nút đăng ký
            $canRegister = true;
            $registerMessage = null;
            $hideRegisterButton = false;
            if (Auth::check()) {
                $existing = SinhVien::where('user_id', Auth::id())->first();
                if ($existing) {
                    $canRegister = false;
                    $isApproved = $existing->trang_thai_ho_so === 'Đã duyệt';
                    $registerMessage = $isApproved
                        ? 'Hồ sơ đã được duyệt. Bạn không thể đăng ký thêm.'
                        : 'Bạn đã gửi hồ sơ. Vui lòng chờ duyệt.';
                    // Nếu đã duyệt thì ẩn hoàn toàn nút
                    $hideRegisterButton = $isApproved;
                }
            }
        } catch (\Exception $e) {
            // Nếu có lỗi (ví dụ: chưa có dữ liệu), trả về collection rỗng
            $thongBaos = collect([]);
            $tinTuc = collect([]);
            $canRegister = true;
            $registerMessage = null;
            $hideRegisterButton = false;
        }
        
        return view('public.home', compact('thongBaos', 'tinTuc', 'canRegister', 'registerMessage', 'hideRegisterButton'));
    }

    /**
     * Form đăng ký ký túc xá (Public)
     */
    public function applyForm()
    {
        // Chặn đăng ký khi đã có hồ sơ
        if (Auth::check()) {
            $existing = SinhVien::where('user_id', Auth::id())->first();
            if ($existing) {
                if ($existing->trang_thai_ho_so === 'Đã duyệt') {
                    return redirect()->route('client.dashboard')
                        ->with('warning', 'Hồ sơ đã được duyệt. Bạn không thể đăng ký thêm.');
                }
                return redirect()->route('public.home')
                    ->with('warning', 'Bạn đã gửi hồ sơ. Vui lòng chờ duyệt.');
            }
        }
        return view('public.apply');
    }

    /**
     * Lưu hồ sơ sinh viên ở trạng thái 'Chờ duyệt'
     */
    public function applyStore(Request $request)
    {
        // Nếu đã đăng nhập và đã có hồ sơ thì chặn submit
        if (Auth::check()) {
            $existing = SinhVien::where('user_id', Auth::id())->first();
            if ($existing) {
                if ($existing->trang_thai_ho_so === 'Đã duyệt') {
                    return redirect()->route('client.dashboard')
                        ->with('warning', 'Hồ sơ đã được duyệt. Bạn không thể đăng ký thêm.');
                }
                return redirect()->route('public.home')
                    ->with('warning', 'Bạn đã gửi hồ sơ. Vui lòng chờ duyệt.');
            }
        }

        $data = $request->validate([
            'ma_sinh_vien' => 'required|string|unique:sinh_vien,ma_sinh_vien',
            'ho_ten' => 'required|string',
            'ngay_sinh' => 'required|date',
            'gioi_tinh' => 'required|string',
            'que_quan' => 'required|string',
            'noi_o_hien_tai' => 'required|string',
            'lop' => 'required|string',
            'nganh' => 'required|string',
            'khoa_hoc' => 'required|string',
            'so_dien_thoai' => 'required|string',
            'email' => 'required|email',
            'anh_sinh_vien' => 'nullable|image|max:2048',
            'citizen_id_number' => 'nullable|string',
            'citizen_issue_date' => 'nullable|date',
            'citizen_issue_place' => 'nullable|string',
            'guardian_name' => 'nullable|string',
            'guardian_phone' => 'nullable|string',
            'guardian_relationship' => 'nullable|string',
        ]);

        // Nếu đã đăng nhập, luôn dùng email tài khoản và gắn user_id
        if (Auth::check()) {
            $data['email'] = Auth::user()->email;
            $data['user_id'] = Auth::id();
        }

        if ($request->hasFile('anh_sinh_vien')) {
            $data['anh_sinh_vien'] = $request->file('anh_sinh_vien')->store('students', 'public');
        }

        $data['trang_thai_ho_so'] = 'Chờ duyệt';
        $data['phong_id'] = null;

        SinhVien::create($data);

        return redirect()->route('public.home')->with('success', 'Đã gửi hồ sơ. Vui lòng chờ duyệt.');
    }
}

