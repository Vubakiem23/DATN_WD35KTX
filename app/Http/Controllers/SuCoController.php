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

        $role = Auth::check() ? trim(strtolower((string) Auth::user()->getRole())) : null;
        if (!$role || !in_array($role, ['admin', 'nhanvien'])) {
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện thao tác này!');
        }

        if ($suco->payment_amount > 0 && !$suco->is_paid) {
            $suco->update(['is_paid' => true]);
            return redirect()->route('suco.show', $id)->with('success', '✅ Xác nhận thanh toán thành công!');
        }

        return redirect()->route('suco.show', $id)
            ->with('info', 'Sự cố này không cần hoặc đã được thanh toán!');
    }

    // Nút hoàn thành sự cố - cập nhật trạng thái, ngày hoàn thành, ảnh sau sửa, % hoàn thiện
    public function hoanThanh(Request $request, SuCo $suco)
    {
        // Chỉ admin hoặc nhân viên mới được hoàn thành sự cố
        $role = Auth::check() ? trim(strtolower((string) Auth::user()->getRole())) : null;
        if (!$role || !in_array($role, ['admin', 'nhanvien'])) {
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $request->validate([
            'ngay_hoan_thanh' => 'required|date',
            'completion_percent' => 'required|integer|min:0|max:100',
            'anh_sau' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'co_thanh_toan' => 'nullable|boolean',
            'payment_amount' => 'nullable|numeric|min:0',
            'ktx_thanh_toan' => 'nullable|boolean',
        ]);

        // Cập nhật trạng thái và ngày hoàn thành
        $suco->trang_thai = 'Hoàn thành';
        $suco->ngay_hoan_thanh = $request->ngay_hoan_thanh;
        if ($request->filled('completion_percent')) {
            $suco->completion_percent = (int) $request->completion_percent;
        }

        // Ảnh sau xử lý (không ghi đè ảnh gốc)
        if ($request->hasFile('anh_sau')) {
            $uploadPath = public_path('uploads/suco');
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }
            // Xóa ảnh sau cũ nếu có
            if ($suco->anh_sau && File::exists(public_path($suco->anh_sau))) {
                File::delete(public_path($suco->anh_sau));
            }
            $file = $request->file('anh_sau');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);
            $suco->anh_sau = 'uploads/suco/' . $filename;
        }

        // Có thanh toán?
        if ($request->boolean('co_thanh_toan')) {
            $amount = (float) ($request->payment_amount ?? 0);
            $suco->payment_amount = max(0, $amount);
            $suco->chi_phi_thuc_te = max(0, $amount); // Ghi nhận chi phí thực tế
            
            // Kiểm tra KTX thanh toán
            $ktxThanhToan = $request->has('ktx_thanh_toan') && $request->ktx_thanh_toan;
            
            if ($ktxThanhToan) {
                // KTX thanh toán → Hoàn thành luôn, đánh dấu đã thanh toán
                $suco->is_paid = true;
                $suco->nguoi_thanh_toan = 'ktx'; // KTX thanh toán
                $suco->ngay_thanh_toan = now();
                $suco->save();
                
                return redirect()->back()->with('success', 'Cập nhật hoàn thành thành công! Chi phí do KTX thanh toán.');
            } else {
                // Sinh viên thanh toán → Chưa thanh toán, chuyển sang hóa đơn
                $suco->is_paid = false;
                $suco->nguoi_thanh_toan = 'client'; // Sinh viên sẽ thanh toán
                $suco->save();
                
                // Nếu có số tiền cần thanh toán, điều hướng sang danh sách hóa đơn sự cố
                if ($suco->payment_amount > 0) {
                    return redirect()->route('hoadonsuco.index')
                        ->with('success', 'Đã cập nhật hoàn thành. Vui lòng tiến hành thanh toán hóa đơn sự cố.');
                }
            }
        } else {
            $suco->payment_amount = 0;
            $suco->is_paid = false;
        }

        $suco->save();

        return redirect()->back()->with('success', 'Cập nhật hoàn thành thành công!');
    }

    // 💰 Tạo hóa đơn cho sự cố (chỉ khi payment_amount = 0)
    public function taoHoaDon(Request $request, $id)
    {
        $suco = SuCo::findOrFail($id);

        $role = Auth::check() ? trim(strtolower((string) Auth::user()->getRole())) : null;
        if (!$role || !in_array($role, ['admin', 'nhanvien'])) {
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

    // ⭐ Sinh viên đánh giá chất lượng xử lý sau khi thanh toán
    public function danhGia(Request $request, $id)
    {
        $suco = SuCo::findOrFail($id);

        // Chỉ cho phép đánh giá khi đã thanh toán
        if (!$suco->is_paid) {
            return redirect()->back()->with('error', 'Bạn chỉ có thể đánh giá sau khi đã thanh toán!');
        }

        // Nếu có đăng nhập sinh viên, yêu cầu là chủ sự cố
        if (Auth::check() && trim(strtolower((string) Auth::user()->getRole())) === 'sinhvien') {
            if ($suco->sinh_vien_id != Auth::user()->id) {
                return redirect()->back()->with('error', 'Bạn không có quyền đánh giá sự cố này!');
            }
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:2000',
        ]);

        $suco->rating = (int) $request->rating;
        $suco->feedback = $request->feedback;
        $suco->rated_at = now();
        $suco->save();

        return redirect()->back()->with('success', 'Cảm ơn bạn đã đánh giá!');
    }


    public function dangXuLy($id) {
    $suco = SuCo::findOrFail($id);
    if ($suco->trang_thai === 'Tiếp nhận') {
        $suco->trang_thai = 'Đang xử lý';
        $suco->save();
        return redirect()->back()->with('success', 'Sự cố đã chuyển sang trạng thái "Đang xử lý".');
    }
    return redirect()->back()->with('info', 'Sự cố không thể chuyển trạng thái.');
}



}
