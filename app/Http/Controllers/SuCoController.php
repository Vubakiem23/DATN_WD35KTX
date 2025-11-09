<?php

namespace App\Http\Controllers;

use App\Models\SuCo;
use App\Models\SinhVien;
use App\Models\Phong;
use App\Models\HoaDonSuCo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class SuCoController extends Controller
{
    // 📋 Danh sách sự cố (có tìm kiếm + phân trang)
    public function index(Request $request)
    {
        $query = SuCo::with(['sinhVien', 'phong']);

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->whereHas('sinhVien', function ($q) use ($search) {
                $q->whereRaw('LOWER(ho_ten) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(ma_sinh_vien) LIKE ?', ["%{$search}%"]);
            });
        }

        $su_cos = $query->orderByDesc('id')->paginate(10);
        $su_cos->appends($request->all());

        return view('su_co.index', compact('su_cos'));
    }

    // 🆕 Form thêm mới
public function create()
{
    // 🔹 Chỉ lấy sinh viên đã có phòng
    $sinhviens = SinhVien::whereNotNull('phong_id')->get();

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
        $data['ngay_hoan_thanh'] = null;

        if ($request->hasFile('anh')) {
            $uploadPath = public_path('uploads/suco');
            if (!File::exists($uploadPath)) File::makeDirectory($uploadPath, 0755, true);

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
        $hoaDon = HoaDonSuCo::where('su_co_id', $suco->id)->first();
        return view('su_co.show', compact('suco', 'hoaDon'));
    }

    // ✏️ Form sửa (admin)
public function edit($id)
{
    $suco = SuCo::with(['sinhVien', 'phong'])->findOrFail($id);

    // 🔹 Chỉ lấy sinh viên đã có phòng
    $sinhviens = SinhVien::whereNotNull('phong_id')->get();

    $phongs = Phong::all();
    return view('su_co.edit', compact('suco', 'sinhviens', 'phongs'));
}

    // 🔄 Cập nhật sự cố (admin xử lý)
    public function update(Request $request, $id)
    {
        $request->validate([
            'trang_thai' => 'required|string',
            'mo_ta' => 'required|string|max:1000',
            'anh' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $suco = SuCo::findOrFail($id);

        $data = [
            'trang_thai' => $request->trang_thai,
            'mo_ta' => $request->mo_ta,
        ];

        if ($request->trang_thai === 'Hoàn thành' && $suco->ngay_hoan_thanh === null) {
            $data['ngay_hoan_thanh'] = now();
        } elseif ($request->trang_thai !== 'Hoàn thành') {
            $data['ngay_hoan_thanh'] = null;
        }

        if ($request->hasFile('anh')) {
            if (!empty($suco->anh) && File::exists(public_path($suco->anh))) File::delete(public_path($suco->anh));

            $uploadPath = public_path('uploads/suco');
            if (!File::exists($uploadPath)) File::makeDirectory($uploadPath, 0755, true);

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

    // 💵 Xác nhận thanh toán
    public function thanhToan($id)
    {
        $suco = SuCo::findOrFail($id);

        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện thao tác này!');
        }

        if ($suco->payment_amount > 0 && !$suco->is_paid) {
            $suco->update(['is_paid' => true]);

            $hoaDon = HoaDonSuCo::where('su_co_id', $suco->id)->first();
            if ($hoaDon) {
                $hoaDon->update([
                    'status' => 'Đã thanh toán',
                    'ngay_thanh_toan' => now(),
                ]);
            }

            return redirect()->route('suco.show', $id)->with('success', '✅ Xác nhận thanh toán thành công!');
        }

        return redirect()->route('suco.show', $id)
            ->with('info', 'Sự cố này không cần hoặc đã được thanh toán!');
    }

    // Nút hoàn thành sự cố
    public function hoanThanh(Request $request, $id)
    {
        $suco = SuCo::findOrFail($id);

        // Validate dữ liệu
        $validated = $request->validate([
            'trang_thai' => 'required|in:Tiếp nhận,Đang xử lý,Hoàn thành',
            'ngay_hoan_thanh' => 'nullable|date',
            'anh' => 'nullable|image|max:2048', // tối đa 2MB
        ]);

        // Cập nhật trạng thái và ngày hoàn thành
        $suco->trang_thai = $validated['trang_thai'];
        $suco->ngay_hoan_thanh = $validated['trang_thai'] === 'Hoàn thành'
                                  ? $validated['ngay_hoan_thanh'] ?? now()
                                  : null;

        // Xử lý upload ảnh nếu có
        if ($request->hasFile('anh')) {
            // Xóa ảnh cũ nếu tồn tại
            if ($suco->anh && File::exists(public_path($suco->anh))) {
                File::delete(public_path($suco->anh));
            }

            $file = $request->file('anh');
            $fileName = 'suco_' . $suco->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = 'uploads/suco/' . $fileName;
            $file->move(public_path('uploads/suco'), $fileName);

            $suco->anh = $filePath;
        }

        $suco->save();

        return redirect()->back()->with('success', 'Cập nhật sự cố thành công.');
    }


    // 🧾 Form nhập giá tiền & tạo hóa đơn cho 1 sự cố
    public function formTaoHoaDon($id)
    {
        $suco = SuCo::with(['sinhVien', 'phong'])->findOrFail($id);
        return view('su_co.form_tao_hoa_don', compact('suco'));
    }

    public function luuHoaDon(Request $request, $id)
    {
        $request->validate([
            'payment_amount' => 'required|numeric|min:0',
        ]);

        $suco = SuCo::findOrFail($id);
        $suco->update(['payment_amount' => $request->payment_amount]);

        if (!$suco->hoaDonSuCo) {
            HoaDonSuCo::create([
                'su_co_id' => $suco->id,
                'sinh_vien_id' => $suco->sinh_vien_id,
                'phong_id' => $suco->phong_id,
                'amount' => $request->payment_amount,
                'status' => 'Chưa thanh toán',
                'ngay_tao' => now(),
                'ngay_thanh_toan' => null,
            ]);
        }

        return redirect()->route('hoadonsuco.index')->with('success', '✅ Cập nhật giá tiền và tạo hóa đơn thành công!');
    }

    // Form thanh toán hàng loạt
    public function formThanhToan()
    {
        $sucos = SuCo::with(['sinhVien', 'phong'])
            ->doesntHave('hoaDonSuCo')
            ->get();

        return view('su_co.thanhtoan', compact('sucos'));
    }

    // Lưu giá tiền + tạo hóa đơn hàng loạt
    public function luuThanhToan(Request $request)
    {
        $data = $request->input('payment');

        foreach ($data as $suco_id => $so_tien) {
            $suco = SuCo::find($suco_id);
            if (!$suco) continue;

            $suco->update(['payment_amount' => $so_tien]);

            if (!$suco->hoaDonSuCo) {
                HoaDonSuCo::create([
                    'su_co_id' => $suco->id,
                    'sinh_vien_id' => $suco->sinh_vien_id,
                    'phong_id' => $suco->phong_id,
                    'amount' => $so_tien,
                    'status' => 'Chưa thanh toán',
                    'ngay_tao' => now(),
                    'ngay_thanh_toan' => null,
                ]);
            }
        }

        return redirect()->route('hoadonsuco.index')->with('success', '✅ Cập nhật giá tiền và tạo hóa đơn thành công!');
    }
}
