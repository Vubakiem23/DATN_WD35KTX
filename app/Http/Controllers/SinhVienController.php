<?php

namespace App\Http\Controllers;

use App\Mail\SinhVienApprovalMail;
use App\Models\SinhVien;
use App\Models\Phong;
use App\Models\ThongBaoPhongSv;
use App\Http\Controllers\AssignmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SinhVienController extends Controller
{
    // Danh sách sinh viên (hiển thị + tìm kiếm)
    public function index(Request $request)
    {
        // giữ tương thích ?search= cũ
        $q          = $request->input('q', $request->input('search'));
        $gender     = $request->input('gender');         // Nam/Nữ/Khác
        $status     = $request->input('status');         // Đã duyệt/Chờ duyệt
        $roomId     = $request->input('room_id');        // phong_id
        $khu        = $request->input('khu');            // khu ở bảng phong
        $classLike  = $request->input('class_id');       // map vào 'lop'
        $majorLike  = $request->input('major_id');       // map vào 'nganh'
        $intakeYear = $request->input('intake_year');    // map vào 'khoa_hoc'
        $month      = $request->input('month');          // Lọc theo tháng
        $year       = $request->input('year');           // Lọc theo năm

        // Query base để tính thống kê theo bộ lọc chung (chưa áp dụng giới tính)
        $baseQuery = SinhVien::query()
            ->search($q)
            ->hoSoStatus($status)
            ->inRoom($roomId)
            ->inKhu($khu)
            ->classLike($classLike)
            ->majorLike($majorLike)
            ->intakeYear($intakeYear);

        // Lọc theo tháng/năm nếu có
        if ($month) {
            $baseQuery->whereMonth('created_at', $month);
        }
        if ($year) {
            $baseQuery->whereYear('created_at', $year);
        }

        // Áp dụng filter giới tính cho danh sách hiện tại
        $filteredQuery = (clone $baseQuery)->gender($gender);

        // Thống kê trạng thái hồ sơ theo bộ lọc hiện tại (bao gồm giới tính)
        $tongHoSo = (clone $filteredQuery)->count();
        $daDuyet = (clone $filteredQuery)->where('trang_thai_ho_so', SinhVien::STATUS_APPROVED)->count();
        $choDuyet = (clone $filteredQuery)->where('trang_thai_ho_so', SinhVien::STATUS_PENDING_APPROVAL)->count();
        $choXacNhan = (clone $filteredQuery)->where('trang_thai_ho_so', SinhVien::STATUS_PENDING_CONFIRMATION)->count();
        $chuaDuyet = $tongHoSo - $daDuyet; // Tổng - đã duyệt (bao gồm cả chờ duyệt, chờ xác nhận và null)

        // Query để lấy danh sách (có pagination)
        $sinhviens = (clone $filteredQuery)
            ->with(['phong', 'slot.phong'])
            ->orderBy('id', 'desc')
            ->paginate(13)
            ->appends($request->query());

        // Thống kê theo giới tính (Tất cả/Nam/Nữ/Khác) dùng cho tab, chỉ áp dụng filter chung, không áp dụng giới tính
        $genderAll   = (clone $baseQuery)->count();
        $genderMale  = (clone $baseQuery)->where('gioi_tinh', 'Nam')->count();
        $genderFemale = (clone $baseQuery)->where('gioi_tinh', 'Nữ')->count();
        $genderOther = (clone $baseQuery)->whereNotIn('gioi_tinh', ['Nam', 'Nữ'])->count();

        // dữ liệu cho dropdown
        $phongs = \App\Models\Phong::select('id', 'ten_phong')->orderBy('ten_phong')->get();
        $dsKhu  = \App\Models\Khu::orderBy('ten_khu')->pluck('ten_khu');

        return view('sinhvien.index', [
            'sinhviens' => $sinhviens,
            'keyword'   => $q,
            'phongs'    => $phongs,
            'dsKhu'     => $dsKhu,
            'tongHoSo'  => $tongHoSo,
            'daDuyet'   => $daDuyet,
            'choDuyet'  => $choDuyet,
            'choXacNhan' => $choXacNhan,
            'chuaDuyet' => $chuaDuyet,
            'currentGender' => $gender ?: 'all',
            'genderStats' => [
                'all'   => $genderAll,
                'male'  => $genderMale,
                'female'=> $genderFemale,
                'other' => $genderOther,
            ],
        ]);
    }


    /* Show modal */
    public function show($id)
    {
        $sinhvien = \App\Models\SinhVien::with([
            'phong.khu',
            'slot.phong.khu',
            'violations.type'
        ])->findOrFail($id);

        return response()->json([
            'data' => view('sinhvien.show_modal', compact('sinhvien'))->render()
        ]);
    }

    // Form thêm mới
    public function create()
    {
        return view('sinhvien.create');
    }

    // Lưu sinh viên mới
    public function store(Request $request)
    {
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
            // 'phong_id' bỏ khỏi form tạo mới; sẽ gán qua chức năng khác
            'trang_thai_ho_so' => ['nullable', 'string', Rule::in(SinhVien::statusOptions())],

            // mới
            'citizen_id_number' => 'nullable|string',
            'citizen_issue_date' => 'nullable|date',
            'citizen_issue_place' => 'nullable|string',
            'guardian_name' => 'nullable|string',
            'guardian_phone' => 'nullable|string',
            'guardian_relationship' => 'nullable|string',

            // ảnh đã có migration riêng từ trước
            'anh_sinh_vien' => 'nullable|image|max:2048',
            'anh_giay_xac_nhan' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('anh_sinh_vien')) {
            $data['anh_sinh_vien'] = $request->file('anh_sinh_vien')->store('students', 'public'); // storage/public/students
        }

        if ($request->hasFile('anh_giay_xac_nhan')) {
            $data['anh_giay_xac_nhan'] = $request->file('anh_giay_xac_nhan')->store('students', 'public'); // storage/public/students
        }

        $data['trang_thai_ho_so'] = $data['trang_thai_ho_so'] ?? SinhVien::STATUS_PENDING_APPROVAL;

        $sv = \App\Models\SinhVien::create($data);

        // Ghi lịch sử phòng nếu có phòng được gán (hiện form tạo không gán phòng)
        if (!empty($sv->phong_id)) {
            \App\Models\RoomAssignment::create([
                'sinh_vien_id' => $sv->id,
                'phong_id' => $sv->phong_id,
                'start_date' => now()->toDateString(),
                'end_date' => null,
            ]);
        }

        return redirect()->route('sinhvien.index')->with('success', 'Đã thêm sinh viên');
    }

    // Form chỉnh sửa
    public function edit($id)
    {
        $sinhvien = \App\Models\SinhVien::findOrFail($id);
        return view('sinhvien.edit', compact('sinhvien'));
    }

    // Cập nhật thông tin
    public function update(Request $request, $id)
    {
        $sv = \App\Models\SinhVien::findOrFail($id);

        $data = $request->validate([
            'ma_sinh_vien' => 'required|string|unique:sinh_vien,ma_sinh_vien,' . $sv->id,
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
            'trang_thai_ho_so' => ['nullable', 'string', Rule::in(SinhVien::statusOptions())],

            // mới
            'citizen_id_number' => 'nullable|string',
            'citizen_issue_date' => 'nullable|date',
            'citizen_issue_place' => 'nullable|string',
            'guardian_name' => 'nullable|string',
            'guardian_phone' => 'nullable|string',
            'guardian_relationship' => 'nullable|string',

            'anh_sinh_vien' => 'nullable|image|max:2048',
            'anh_giay_xac_nhan' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('anh_sinh_vien')) {
            // Xóa ảnh cũ nếu có
            if ($sv->anh_sinh_vien) {
                Storage::disk('public')->delete($sv->anh_sinh_vien);
            }
            $data['anh_sinh_vien'] = $request->file('anh_sinh_vien')->store('students', 'public');
        }

        if ($request->hasFile('anh_giay_xac_nhan')) {
            // Xóa ảnh cũ nếu có
            if ($sv->anh_giay_xac_nhan) {
                Storage::disk('public')->delete($sv->anh_giay_xac_nhan);
            }
            $data['anh_giay_xac_nhan'] = $request->file('anh_giay_xac_nhan')->store('students', 'public');
        }

        $sv->update($data);

        return redirect()->route('sinhvien.index')->with('success', 'Đã cập nhật sinh viên');
    }


    // 🧹 Xóa sinh viên + dữ liệu liên quan
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Xóa các bản ghi đăng ký KTX liên quan
            DB::table('dang_ky_k_t_x')->where('sinh_vien_id', $id)->delete();

            // Xóa sinh viên
            $sinhvien = SinhVien::findOrFail($id);
            $sinhvien->delete();

            DB::commit();

            return redirect()->route('sinhvien.index')->with('success', 'Đã xóa sinh viên và dữ liệu liên quan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('sinhvien.index')
                ->with('error', 'Không thể xóa sinh viên vì đang liên kết với dữ liệu khác.');
        }
    }

    // Duyệt hồ sơ (thay đổi trạng thái)
    public function approve($id)
    {
        $sv = SinhVien::findOrFail($id);
        $sv->trang_thai_ho_so = SinhVien::STATUS_APPROVED;
        $sv->save();

        // Gửi email thông báo đã được duyệt
        if (!empty($sv->email)) {
            try {
                Mail::to($sv->email)->send(new SinhVienApprovalMail($sv));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with('success', 'Đã duyệt hồ sơ sinh viên và gửi email thông báo.');
    }
    public function capNhatPhong(Request $request, $id)
{
        // Chuyển logic gán phòng sang AssignmentController để đảm bảo sinh viên phải xác nhận
        $assignmentController = new AssignmentController();
        return $assignmentController->assign($request, $id);
}
}
