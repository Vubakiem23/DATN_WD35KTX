<?php

namespace App\Http\Controllers;

use App\Models\SinhVien;
use App\Models\Phong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


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

        $sinhviens = SinhVien::query()
            ->with('phong')
            ->search($q)
            ->gender($gender)
            ->hoSoStatus($status)
            ->inRoom($roomId)
            ->inKhu($khu)
            ->classLike($classLike)
            ->majorLike($majorLike)
            ->intakeYear($intakeYear)
            ->orderBy('id', 'desc')
            ->paginate(13)
            ->appends($request->query());

        // dữ liệu cho dropdown
        $phongs = \App\Models\Phong::select('id', 'ten_phong')->orderBy('ten_phong')->get();
        $dsKhu  = \App\Models\Khu::orderBy('ten_khu')->pluck('ten_khu');

        return view('sinhvien.index', [
            'sinhviens' => $sinhviens,
            'keyword'   => $q,
            'phongs'    => $phongs,
            'dsKhu'     => $dsKhu,
        ]);
    }


    /* Show modal */
    public function show($id)
    {
        $sinhvien = SinhVien::with('phong')->find($id);
        if (!$sinhvien) {
            return redirect()->route('sinhvien.index');
        }
        $sinhvien = \App\Models\SinhVien::with(['violations.type'])->findOrFail($id);
        return response()->json([
            'data' => view('sinhvien.show_modal', compact('sinhvien'))->render()
        ]);

        // $html = view('sinhvien.show_modal', compact('sinhvien'))->render();

        // return response()->json(['data' => $html]);
    }

    // Form thêm mới
    public function create()
    {
        $phongs = Phong::all();
        return view('sinhvien.create', compact('phongs'));
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
            'phong_id' => 'required|exists:phong,id',
            'trang_thai_ho_so' => 'nullable|string',

            // mới
            'citizen_id_number' => 'nullable|string',
            'citizen_issue_date' => 'nullable|date',
            'citizen_issue_place' => 'nullable|string',
            'guardian_name' => 'nullable|string',
            'guardian_phone' => 'nullable|string',
            'guardian_relationship' => 'nullable|string',

            // ảnh đã có migration riêng từ trước
            'anh_sinh_vien' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('anh_sinh_vien')) {
            $data['anh_sinh_vien'] = $request->file('anh_sinh_vien')->store('students', 'public'); // storage/public/students
        }

        $sv = \App\Models\SinhVien::create($data);

        // Ghi lịch sử phòng lần đầu
        \App\Models\RoomAssignment::create([
            'sinh_vien_id' => $sv->id,
            'phong_id' => $sv->phong_id,
            'start_date' => now()->toDateString(),
            'end_date' => null,
        ]);

        return redirect()->route('sinhvien.index')->with('success', 'Đã thêm sinh viên');
    }

    // Form chỉnh sửa
    public function edit($id)
    {
        $sinhvien = SinhVien::findOrFail($id);
        $phongs = Phong::all();

        return view('sinhvien.edit', compact('sinhvien', 'phongs'));
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
            'phong_id' => 'required|exists:phong,id',
            'trang_thai_ho_so' => 'nullable|string',

            // mới
            'citizen_id_number' => 'nullable|string',
            'citizen_issue_date' => 'nullable|date',
            'citizen_issue_place' => 'nullable|string',
            'guardian_name' => 'nullable|string',
            'guardian_phone' => 'nullable|string',
            'guardian_relationship' => 'nullable|string',

            'anh_sinh_vien' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('anh_sinh_vien')) {
            // Storage::disk('public')->delete($sv->anh_sinh_vien); // nếu muốn dọn ảnh cũ
            $data['anh_sinh_vien'] = $request->file('anh_sinh_vien')->store('students', 'public');
        }

        $oldPhong = $sv->phong_id;
        $sv->update($data);

        // Nếu đổi phòng, đóng lịch sử cũ và mở lịch sử mới
        if ((int)$oldPhong !== (int)$sv->phong_id) {
            \App\Models\RoomAssignment::where('sinh_vien_id', $sv->id)
                ->whereNull('end_date')
                ->update(['end_date' => now()->toDateString()]);

            \App\Models\RoomAssignment::create([
                'sinh_vien_id' => $sv->id,
                'phong_id' => $sv->phong_id,
                'start_date' => now()->toDateString(),
                'end_date' => null,
            ]);
        }

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
        $sv->trang_thai_ho_so = 'Đã duyệt';
        $sv->save();

        return back()->with('success', 'Đã duyệt hồ sơ sinh viên.');
    }
}
