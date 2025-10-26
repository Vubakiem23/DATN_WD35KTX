<?php

namespace App\Http\Controllers;

use App\Models\SuCo;
use App\Models\SinhVien;
use App\Models\Phong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class SuCoController extends Controller
{
    // 📋 Danh sách sự cố (có tìm kiếm + phân trang)
    public function index(Request $request)
    {
        $query = SuCo::with(['sinhVien', 'phong']);

        // 🔍 Tìm kiếm theo MSSV hoặc họ tên sinh viên
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('sinhVien', function ($q) use ($search) {
                $q->where('ho_ten', 'like', "%$search%")
                  ->orWhere('ma_sinh_vien', 'like', "%$search%");
            });
        }

        $su_cos = $query->orderByDesc('id')->paginate(10);
        $su_cos->appends($request->all());

        return view('su_co.index', compact('su_cos'));
    }

    // 🆕 Form thêm mới
    public function create()
    {
        $sinhviens = SinhVien::all();
        $phongs = Phong::all();
        return view('su_co.create', compact('sinhviens', 'phongs'));
    }

    // 💾 Lưu sự cố mới (sinh viên tạo)
    public function store(Request $request)
    {
        $request->validate([
            'sinh_vien_id' => 'required|exists:sinh_vien,id',
            'phong_id' => 'required|exists:phong,id',
            'mo_ta' => 'required|string|max:1000',
            'anh' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['sinh_vien_id', 'phong_id', 'mo_ta']);
        $data['ngay_gui'] = now();
        $data['trang_thai'] = 'Tiếp nhận';
        $data['payment_amount'] = 0;
        $data['is_paid'] = false;
        $data['nguoi_tao'] = 'sinh_vien';
        $data['ngay_hoan_thanh'] = null; // 🔹 thêm mặc định null khi sinh viên tạo

        // ✅ Upload ảnh nếu có
        if ($request->hasFile('anh')) {
            $uploadPath = public_path('uploads/suco');
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            $file = $request->file('anh');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $fileName);
            $data['anh'] = 'uploads/suco/' . $fileName;
        }

        SuCo::create($data);

        return redirect()->route('suco.index')->with('success', 'Đã báo cáo sự cố thành công!');
    }

    // 👁️ Xem chi tiết
    public function show($id)
    {
        $suco = SuCo::with(['sinhVien', 'phong'])->findOrFail($id);
        return view('su_co.show', compact('suco'));
    }

    // ✏️ Form sửa (admin)
    public function edit($id)
    {
        $suco = SuCo::with(['sinhVien', 'phong'])->findOrFail($id);
        $sinhviens = SinhVien::all();
        $phongs = Phong::all();
        return view('su_co.edit', compact('suco', 'sinhviens', 'phongs'));
    }

    // 🔄 Cập nhật sự cố (admin xử lý)
    public function update(Request $request, $id)
    {
        $request->validate([
            'trang_thai' => 'required|string',
            'payment_amount' => 'nullable|numeric|min:0',
            'is_paid' => 'nullable|boolean',
            'mo_ta' => 'required|string|max:1000',
            'anh' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $suco = SuCo::findOrFail($id);

        $data = [
            'trang_thai' => $request->trang_thai,
            'mo_ta' => $request->mo_ta,
        ];

        // 🕓 Thêm logic ngày hoàn thành
        if ($request->trang_thai === 'Hoàn thành' && $suco->ngay_hoan_thanh === null) {
            $data['ngay_hoan_thanh'] = now(); // 🔹 lưu ngày hoàn thành khi chuyển sang Hoàn thành
        } elseif ($request->trang_thai !== 'Hoàn thành') {
            $data['ngay_hoan_thanh'] = null; // 🔹 reset lại nếu chuyển về trạng thái khác
        }

        // 💰 Thanh toán
        $paymentAmount = $request->payment_amount ?? 0;
        $isPaid = $request->is_paid ?? false;

        $data['payment_amount'] = $paymentAmount;
        $data['is_paid'] = ($paymentAmount == 0) ? false : $isPaid;

        // 🖼️ Cập nhật ảnh
        if ($request->hasFile('anh')) {
            if (!empty($suco->anh) && File::exists(public_path($suco->anh))) {
                File::delete(public_path($suco->anh));
            }

            $uploadPath = public_path('uploads/suco');
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            $file = $request->file('anh');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $fileName);
            $data['anh'] = 'uploads/suco/' . $fileName;
        }

        $suco->update($data);

        return redirect()->route('suco.index')->with('success', 'Cập nhật sự cố thành công!');
    }

    // 🗑️ Xóa sự cố
    public function destroy($id)
    {
        $suco = SuCo::findOrFail($id);

        if (!empty($suco->anh) && File::exists(public_path($suco->anh))) {
            File::delete(public_path($suco->anh));
        }

        $suco->delete();

        return redirect()->route('suco.index')->with('success', 'Xóa sự cố thành công!');
    }

    // 💵 Admin xác nhận thanh toán
    public function thanhToan($id)
    {
        $suco = SuCo::findOrFail($id);

        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện thao tác này!');
        }

        if ($suco->payment_amount > 0 && !$suco->is_paid) {
            $suco->update(['is_paid' => true]);
            return redirect()->route('suco.show', $id)->with('success', '✅ Xác nhận thanh toán thành công!');
        }

        return redirect()->route('suco.show', $id)
            ->with('info', 'Sự cố này không cần hoặc đã được thanh toán!');
    }
}
