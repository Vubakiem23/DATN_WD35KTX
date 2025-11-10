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
        $query = SuCo::with([
            'sinhVien' => function($q) {
                $q->with(['phong.khu', 'slot.phong.khu']);
            },
            'phong.khu'
        ]);

        // 🔍 Tìm kiếm theo MSSV hoặc Họ tên (không phân biệt chữ hoa/thường)
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->whereHas('sinhVien', function ($q) use ($search) {
                $q->whereRaw('LOWER(ho_ten) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(ma_sinh_vien) LIKE ?', ["%{$search}%"]);
            });
        }

        // 🔍 Lọc theo trạng thái
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        // 🔍 Lọc theo khoảng thời gian (ngày gửi)
        if ($request->filled('date_from')) {
            $query->whereDate('ngay_gui', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('ngay_gui', '<=', $request->date_to);
        }

        $su_cos = $query->orderByDesc('id')->paginate(10);
        $su_cos->appends($request->all());

        return view('su_co.index', compact('su_cos'));
    }


    // 🆕 Form thêm mới
    public function create()
    {
        $sinhviens = SinhVien::with(['slot.phong', 'phong'])->get();
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

        // Ưu tiên lấy phong_id từ slot nếu có, nếu không thì dùng phong_id từ request
        $sinhVien = SinhVien::with('slot')->findOrFail($request->sinh_vien_id);
        $phongId = $request->phong_id;
        
        // Nếu sinh viên có slot và slot có phòng, ưu tiên dùng phòng từ slot
        if ($sinhVien->slot && $sinhVien->slot->phong_id) {
            $phongId = $sinhVien->slot->phong_id;
        }

        $data = [
            'sinh_vien_id' => $request->sinh_vien_id,
            'phong_id' => $phongId,
            'mo_ta' => $request->mo_ta,
            'ngay_gui' => now(),
            'trang_thai' => 'Tiếp nhận',
            'payment_amount' => 0,
            'is_paid' => false,
            'nguoi_tao' => 'sinh_vien',
            'ngay_hoan_thanh' => null,
        ];

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
        $suco = SuCo::with([
            'sinhVien' => function($q) {
                $q->with(['phong.khu', 'slot.phong.khu']);
            },
            'phong.khu'
        ])->findOrFail($id);
        return view('su_co.show', compact('suco'));
    }

    // ✏️ Form sửa (admin)
    public function edit($id)
    {
        $suco = SuCo::with([
            'sinhVien' => function($q) {
                $q->with(['phong.khu', 'slot.phong.khu']);
            },
            'phong.khu'
        ])->findOrFail($id);
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

    // 💵 Admin/Nhân viên xác nhận thanh toán
    public function thanhToan($id)
    {
        $suco = SuCo::findOrFail($id);

        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'nhanvien'])) {
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện thao tác này!');
        }

        if ($suco->payment_amount > 0 && !$suco->is_paid) {
            $suco->update(['is_paid' => true]);
            return redirect()->route('suco.show', $id)->with('success', '✅ Xác nhận thanh toán thành công!');
        }

        return redirect()->route('suco.show', $id)
            ->with('info', 'Sự cố này không cần hoặc đã được thanh toán!');
    }

    // Nút hoàn thành sự cố - CHỈ sửa trạng thái và ảnh, KHÔNG sửa thanh toán
    public function hoanThanh(Request $request, SuCo $suco)
    {
        $request->validate([
            'ngay_hoan_thanh' => 'required|date',
            'anh' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // CHỈ cập nhật trạng thái và ngày hoàn thành
        $suco->trang_thai = 'Hoàn thành';
        $suco->ngay_hoan_thanh = $request->ngay_hoan_thanh;

        // Xử lý upload ảnh nếu có
        if ($request->hasFile('anh')) {
            // Xóa ảnh cũ nếu tồn tại
            if ($suco->anh && File::exists(public_path($suco->anh))) {
                File::delete(public_path($suco->anh));
            }

            $uploadPath = public_path('uploads/suco');
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            $file = $request->file('anh');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);
            $suco->anh = 'uploads/suco/' . $filename;
        }

        $suco->save();

        return redirect()->back()->with('success', 'Cập nhật hoàn thành thành công!');
    }

    // 💰 Tạo hóa đơn cho sự cố (chỉ khi payment_amount = 0)
    public function taoHoaDon(Request $request, $id)
    {
        $suco = SuCo::findOrFail($id);

        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'nhanvien'])) {
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện thao tác này!');
        }

        // Chỉ cho phép tạo hóa đơn khi chưa có payment_amount
        if ($suco->payment_amount > 0) {
            return redirect()->back()->with('info', 'Sự cố này đã có hóa đơn rồi!');
        }

        $request->validate([
            'payment_amount' => 'required|numeric|min:0',
        ]);

        // Nếu payment_amount = 0 thì không cần thanh toán (sự cố do ký túc xá)
        // Nếu payment_amount > 0 thì cần thanh toán (sự cố do sinh viên gây ra)
        $suco->payment_amount = $request->payment_amount;
        $suco->is_paid = false; // Mặc định chưa thanh toán

        $suco->save();

        return redirect()->back()->with('success', '✅ Tạo hóa đơn thành công! ' . 
            ($request->payment_amount > 0 ? 'Sinh viên cần thanh toán ' . number_format($request->payment_amount, 0, ',', '.') . ' VNĐ' : 'Sự cố này không cần thanh toán'));
    }





}
