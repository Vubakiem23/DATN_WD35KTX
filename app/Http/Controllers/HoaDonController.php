<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\HoaDon;
use App\Exports\HoaDonExport;
use App\Models\Phong;
use App\Models\SinhVien;
use App\Models\HoaDonSlotPayment;
use App\Models\HoaDonUtilitiesPayment;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\HoaDonDienNuocImport;
use App\Imports\HoaDonTienPhongImport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Traits\HoaDonCalculations;

use Illuminate\Http\Request;




class HoaDonController extends Controller
{
    use HoaDonCalculations;

    public function importHoaDon(Request $request)
    {
        try {
            $data = $request->validate([
                'file' => 'required|mimes:xlsx,xls',
                'invoice_type' => 'required|in:' . HoaDon::LOAI_TIEN_PHONG . ',' . HoaDon::LOAI_DIEN_NUOC,
            ]);

            $importer = $data['invoice_type'] === HoaDon::LOAI_DIEN_NUOC
                ? new HoaDonDienNuocImport
                : new HoaDonTienPhongImport;

            Excel::import($importer, $data['file']);

            return back()->with('success', 'Nhập hóa đơn thành công!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Dòng {$failure->row()}: " . implode(', ', $failure->errors());
            }
            return back()->with('error', 'Lỗi nhập dữ liệu: ' . implode(' | ', $errors));
        } catch (\Exception $e) {
            \Log::error('Lỗi import hóa đơn: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Lỗi khi nhập hóa đơn: ' . $e->getMessage());
        }
    }

    public function index(Request $request)
    {
        [$hoaDons, $dsPhongs] = $this->prepareHoaDonListing($request);

        return view('hoadon.index', compact('hoaDons', 'dsPhongs'));
    }

    public function dienNuoc(Request $request)
    {
        [$hoaDons, $dsPhongs] = $this->prepareHoaDonListing($request);

        return view('hoadon.diennuoc', compact('hoaDons', 'dsPhongs'));
    }



    public function destroy($id)
    {
        $hoaDon = HoaDon::find($id);

        if (!$hoaDon) {
            return redirect()->back()->with('error', 'Không tìm thấy hóa đơn!');
        }

        $hoaDon->delete();

        return redirect()->back()->with('success', 'Xóa hóa đơn thành công!');
    }
    public function exportPDF($id)
    {
        $hoaDon = HoaDon::with('phong.khu', 'phong.slots.sinhVien')->findOrFail($id);

        $so_dien = $hoaDon->so_dien_moi - $hoaDon->so_dien_cu;
        $so_nuoc = $hoaDon->so_nuoc_moi - $hoaDon->so_nuoc_cu;

        $hoaDon->tien_dien = $so_dien * $hoaDon->don_gia_dien;
        $hoaDon->tien_nuoc = $so_nuoc * $hoaDon->don_gia_nuoc;
        $this->enrichHoaDonWithPhongPricing($hoaDon);
        $hoaDon->thanh_tien = $hoaDon->tien_dien + $hoaDon->tien_nuoc + $hoaDon->tien_phong_slot;
        $this->attachSlotBreakdown($hoaDon);

        $pdf = Pdf::loadView('hoadon.pdf', compact('hoaDon'));
        return $pdf->stream('hoa-don-' . $hoaDon->id . '.pdf');
    }
    public function export(Request $request)
    {
        $trangThai = $request->get('trang_thai'); // lọc theo trạng thái nếu có
        $fileName = 'DanhSach_HoaDon.xlsx';

        return Excel::download(new HoaDonExport($trangThai), $fileName);
    }


    public function thanhToan($id, Request $request)
{
    $data = $request->validate([
        'type' => 'required|in:tien-phong,dien-nuoc',
        'hinh_thuc_thanh_toan' => 'required|in:tien_mat,chuyen_khoan',
        'ghi_chu_thanh_toan' => 'required|string|max:255',
    ]);

    $hoaDon = HoaDon::with('utilitiesPayments')->findOrFail($id);
    $type = $data['type'];

    if ($type === 'dien-nuoc') {
        // Nếu hóa đơn đã được đánh dấu là đã thanh toán tổng
        if ($hoaDon->da_thanh_toan_dien_nuoc) {
            return response()->json([
                'success' => true,
                'message' => 'Hóa đơn điện · nước đã được xác nhận trước đó.',
                'type' => $type,
            ]);
        }

        // Cập nhật từng slot chưa thanh toán
        foreach ($hoaDon->utilitiesPayments as $slot) {
            if (!$slot->da_thanh_toan) {
                $slot->update([
                    'da_thanh_toan' => true,
                    'trang_thai' => HoaDonUtilitiesPayment::TRANG_THAI_DA_THANH_TOAN,
                    'ngay_thanh_toan' => now(),
                    'hinh_thuc_thanh_toan' => $data['hinh_thuc_thanh_toan'],
                    'ghi_chu' => $data['ghi_chu_thanh_toan'],
                    'xac_nhan_boi' => Auth::id(),
                ]);
            }
        }

        // Kiểm tra lại tổng số slot đã thanh toán
        $totalSlots = $hoaDon->utilitiesPayments->count();
        $paidSlots = $hoaDon->utilitiesPayments->where('da_thanh_toan', true)->count();

        if ($paidSlots >= $totalSlots && $totalSlots > 0) {
            $hoaDon->da_thanh_toan_dien_nuoc = true;
            $hoaDon->ngay_thanh_toan_dien_nuoc = now();
            $hoaDon->hinh_thuc_thanh_toan_dien_nuoc = $data['hinh_thuc_thanh_toan'];
            $hoaDon->ghi_chu_thanh_toan_dien_nuoc = $data['ghi_chu_thanh_toan'];
            $hoaDon->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật thanh toán điện · nước.',
            'type' => $type,
        ]);
    }

    // Xử lý tiền phòng như cũ
    if ($hoaDon->da_thanh_toan) {
        return response()->json([
            'success' => true,
            'message' => 'Hóa đơn tiền phòng đã được xác nhận trước đó.',
            'type' => $type,
        ]);
    }

    $hoaDon->trang_thai = 'Đã thanh toán';
    $hoaDon->da_thanh_toan = true;
    $hoaDon->ngay_thanh_toan = now();
    $hoaDon->hinh_thuc_thanh_toan = $data['hinh_thuc_thanh_toan'];
    $hoaDon->ghi_chu_thanh_toan = $data['ghi_chu_thanh_toan'];
    $hoaDon->save();

    $bienLaiHtml = $this->hienThiBienLai($hoaDon);

    return response()->json([
        'success' => true,
        'bien_lai' => $bienLaiHtml,
        'type' => $type,
    ]);
}


    public function show($id, Request $request)
    {
        $hoaDon = HoaDon::with('phong.khu', 'phong.slots.sinhVien', 'slotPayments', 'utilitiesPayments')->findOrFail($id);

        // Tính toán lại nếu cần
        $so_dien = $hoaDon->so_dien_moi - $hoaDon->so_dien_cu;
        $so_nuoc = $hoaDon->so_nuoc_moi - $hoaDon->so_nuoc_cu;

        $hoaDon->tien_dien = $so_dien * $hoaDon->don_gia_dien;
        $hoaDon->tien_nuoc = $so_nuoc * $hoaDon->don_gia_nuoc;
        $this->enrichHoaDonWithPhongPricing($hoaDon);
        
        // Tính thành tiền theo view mode
        $viewMode = $request->get('view');
        if ($viewMode === 'phong') {
            // Chỉ tính tiền phòng
            $hoaDon->thanh_tien = $hoaDon->tien_phong_slot ?? 0;
        } elseif ($viewMode === 'dien-nuoc') {
            // Chỉ tính điện nước
            $hoaDon->thanh_tien = ($hoaDon->tien_dien ?? 0) + ($hoaDon->tien_nuoc ?? 0);
        } else {
            // Mặc định: tính tất cả
            $hoaDon->thanh_tien = ($hoaDon->tien_dien ?? 0) + ($hoaDon->tien_nuoc ?? 0) + ($hoaDon->tien_phong_slot ?? 0);
        }
        
        $this->attachSlotBreakdown($hoaDon);
        // Khởi tạo slot payments & utilities payments nếu chưa có
        $this->initializeSlotPayments($hoaDon);
        $this->initializeUtilitiesPayments($hoaDon);
        $hoaDon->load('utilitiesPayments');

        return view('hoadon.show', compact('hoaDon'));
    }
    public function quickUpdate(Request $request, $id)
{
    try {
        $hoaDon = HoaDon::findOrFail($id);

        // Cập nhật đơn giá
        $hoaDon->don_gia_dien = $request->don_gia_dien;
        $hoaDon->don_gia_nuoc = $request->don_gia_nuoc;

        // Tính lại tiền
        $so_dien = $hoaDon->so_dien_moi - $hoaDon->so_dien_cu;
        $so_nuoc = $hoaDon->so_nuoc_moi - $hoaDon->so_nuoc_cu;
        $this->enrichHoaDonWithPhongPricing($hoaDon);
        $gia_phong = $hoaDon->tien_phong_slot ?? 0;

        $hoaDon->thanh_tien = ($so_dien * $hoaDon->don_gia_dien)
                            + ($so_nuoc * $hoaDon->don_gia_nuoc)
                            + $gia_phong;

        $hoaDon->save();

        return response()->json(['success' => true], 200);
    } catch (\Exception $e) {
        \Log::error('Lỗi cập nhật nhanh: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Đã xảy ra lỗi khi cập nhật hóa đơn.'
        ], 500);
    }
}



    public function update(Request $request, $id)
{
    $hoaDon = HoaDon::findOrFail($id);

    // Cập nhật đơn giá
    $hoaDon->don_gia_dien = $request->don_gia_dien;
    $hoaDon->don_gia_nuoc = $request->don_gia_nuoc;
    // Tính lại tiền
    $so_dien = $hoaDon->so_dien_moi - $hoaDon->so_dien_cu;
$so_nuoc = $hoaDon->so_nuoc_moi - $hoaDon->so_nuoc_cu;
$this->enrichHoaDonWithPhongPricing($hoaDon);
$gia_phong = $hoaDon->tien_phong_slot ?? 0;

$hoaDon->thanh_tien = ($so_dien * $hoaDon->don_gia_dien) + ($so_nuoc * $hoaDon->don_gia_nuoc) + $gia_phong;


    $hoaDon->save();

    return redirect()->route('hoadon.index')->with('success', 'Hóa đơn đã được cập nhật!');
}


// lịch sử hóa đơn tiền phòng và điẹn nước
   public function lichSuTienPhong(Request $request)
{
    $query = HoaDon::where('invoice_type', 'tien_phong')
                   ->where('da_thanh_toan', true);

    if ($request->filled('ngay')) {
        $query->whereDate('ngay_thanh_toan', $request->ngay);
    }

    $hoaDons = $query->orderBy('ngay_thanh_toan', 'desc')->paginate(10);

    return view('hoadon.lichsu_tienphong', compact('hoaDons'));
}

public function lichSuDienNuoc(Request $request)
{
    $query = HoaDon::where('invoice_type', 'dien_nuoc')
                   ->where('da_thanh_toan_dien_nuoc', true);

    if ($request->filled('ngay')) {
        $query->whereDate('ngay_thanh_toan_dien_nuoc', $request->ngay);
    }

    $hoaDons = $query->orderBy('ngay_thanh_toan_dien_nuoc', 'desc')->paginate(10);

    return view('hoadon.lichsu_diennuoc', compact('hoaDons'));
}




    public function xemBienLai($id)
{
        $hoaDon = HoaDon::with('phong.khu', 'phong.slots.sinhVien')->findOrFail($id);
        $so_dien = $hoaDon->so_dien_moi - $hoaDon->so_dien_cu;
        $so_nuoc = $hoaDon->so_nuoc_moi - $hoaDon->so_nuoc_cu;

        $hoaDon->tien_dien = $so_dien * $hoaDon->don_gia_dien;
        $hoaDon->tien_nuoc = $so_nuoc * $hoaDon->don_gia_nuoc;
        $this->enrichHoaDonWithPhongPricing($hoaDon);
        $hoaDon->thanh_tien = $hoaDon->tien_dien + $hoaDon->tien_nuoc + $hoaDon->tien_phong_slot;
        $this->attachSlotBreakdown($hoaDon);

    if (!$hoaDon->da_thanh_toan) {
        return redirect()->route('hoadon.index')->with('error', 'Hóa đơn chưa thanh toán.');
    }

    return view('hoadon.receipt', compact('hoaDon'));
}
public function hienThiBienLai(HoaDon $hoaDon)
{
    $hoaDon->loadMissing('phong.khu', 'phong.slots.sinhVien');
    $so_dien = $hoaDon->so_dien_moi - $hoaDon->so_dien_cu;
    $so_nuoc = $hoaDon->so_nuoc_moi - $hoaDon->so_nuoc_cu;

    $hoaDon->tien_dien = $so_dien * $hoaDon->don_gia_dien;
    $hoaDon->tien_nuoc = $so_nuoc * $hoaDon->don_gia_nuoc;
    $this->enrichHoaDonWithPhongPricing($hoaDon);
    $hoaDon->thanh_tien = $hoaDon->tien_dien + $hoaDon->tien_nuoc + $hoaDon->tien_phong_slot;
    $this->attachSlotBreakdown($hoaDon);

    return view('hoadon.receipt', compact('hoaDon'))->render();
}
// gửi email hàng loạt 

public function guiEmailHangLoat()
{
    $hoaDons = HoaDon::with('phong.sinhViens')
        ->where('da_thanh_toan', false)
        ->get();

    $dem = 0;

    foreach ($hoaDons as $hoaDon) {
        if ($hoaDon->phong && $hoaDon->phong->sinhViens) {
            foreach ($hoaDon->phong->sinhViens as $sinhVien) {
                if ($sinhVien->email) {
                    Mail::send('emails.hoa_don', [
                        'hoaDon' => $hoaDon,
                        'sinhVien' => $sinhVien
                    ], function ($message) use ($sinhVien, $hoaDon) {
                        $message->to($sinhVien->email)
                                ->subject('Hóa đơn tiền phòng tháng ' . $hoaDon->thang);
                    });
                    $dem++;
                }
            }
        }
    }

    return back()->with('success', 'Đã gửi ' . $dem . ' email hóa đơn thành công.');
}



// gửi email cho taats cả sinh viên trong phòng chưa thanh toán 
public function guiEmailTheoPhong($phong_id)
    {
        $sinhViens = SinhVien::where('phong_id', $phong_id)->get();

        foreach ($sinhViens as $sv) {
            if (!$sv->email) continue;

            Mail::raw('Thông báo gửi tới sinh viên trong phòng ' . $phong_id, function ($message) use ($sv) {
                $message->to($sv->email)
                        ->subject('Thông báo từ KTX');
            });
        }

        return 'Đã gửi email tới ' . $sinhViens->count() . ' sinh viên trong phòng ' . $phong_id;
    }
     // tìm kiếm hóa đơn trong lịch sử thanh toán 
    public function timKiem(Request $request)
{
    $keyword = $request->input('keyword');

    $hoaDons = HoaDon::with('phong')
        ->where('da_thanh_toan', true) // 👉 chỉ lấy hóa đơn đã thanh toán
        ->where(function ($query) use ($keyword) {
            $query->whereHas('phong', function ($q) use ($keyword) {
                $q->where('ten_phong', 'like', "%$keyword%")
                  ->orWhereHas('khu', function ($k) use ($keyword) {
                      $k->where('ten_khu', 'like', "%$keyword%");
                  });
            })
            ->orWhere('created_at', 'like', "%$keyword%");
        })
        ->orderByDesc('ngay_thanh_toan')
        ->paginate(10);

    return view('hoadon.lichsu', compact('hoaDons'));
}

    /**
     * Thanh toán cho một slot cụ thể
     */
    public function thanhToanSlot(Request $request, $hoaDonId, $slotPaymentId)
    {
        $request->validate([
            'hinh_thuc_thanh_toan' => 'required|in:tien_mat,chuyen_khoan',
            'ghi_chu' => 'nullable|string|max:500',
            'anh_chuyen_khoan' => 'nullable|image|max:4096',
            'action' => 'nullable|in:student_submit,admin_confirm',
        ]);

        $action = $request->input('action', 'student_submit');

        $hoaDon = HoaDon::findOrFail($hoaDonId);
        $slotPayment = HoaDonSlotPayment::where('hoa_don_id', $hoaDonId)
            ->findOrFail($slotPaymentId);

        if ($action === 'admin_confirm') {
            if ($slotPayment->da_thanh_toan) {
                return response()->json([
                    'success' => true,
                    'message' => 'Slot đã được xác nhận trước đó.',
                ]);
            }

            $slotPayment->da_thanh_toan = true;
            $slotPayment->trang_thai = HoaDonSlotPayment::TRANG_THAI_DA_THANH_TOAN;
            $slotPayment->ngay_thanh_toan = now();
            $slotPayment->hinh_thuc_thanh_toan = $request->hinh_thuc_thanh_toan;
            $slotPayment->ghi_chu = $request->ghi_chu;
            $slotPayment->xac_nhan_boi = Auth::id();
            $slotPayment->save();
        } else {
            if ($slotPayment->da_thanh_toan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Slot đã được thanh toán.',
                ], 409);
            }

            if ($slotPayment->trang_thai === HoaDonSlotPayment::TRANG_THAI_CHO_XAC_NHAN) {
                return response()->json([
                    'success' => false,
                    'message' => 'Slot đang chờ xác nhận từ ban quản lý.',
                ], 409);
            }

            $slotPayment->trang_thai = HoaDonSlotPayment::TRANG_THAI_CHO_XAC_NHAN;
            $slotPayment->client_requested_at = now();
            $slotPayment->client_ghi_chu = $request->ghi_chu;
            $slotPayment->hinh_thuc_thanh_toan = $request->hinh_thuc_thanh_toan;
            if ($request->hasFile('anh_chuyen_khoan')) {
                $storedPath = $request->file('anh_chuyen_khoan')->store('slot-payments', 'public');
                $slotPayment->client_transfer_image_path = $storedPath;
            }
            $slotPayment->save();

            return response()->json([
                'success' => true,
                'message' => 'Đã gửi yêu cầu thanh toán, vui lòng chờ xác nhận.',
                'status' => $slotPayment->trang_thai,
            ]);
        }

        // Kiểm tra xem tất cả slot đã thanh toán chưa
        $totalSlots = $hoaDon->slotPayments()->count();
        $paidSlots = $hoaDon->slotPayments()->where('da_thanh_toan', true)->count();

        // Nếu tất cả slot đã thanh toán, cập nhật trạng thái hóa đơn
        if ($paidSlots >= $totalSlots && $totalSlots > 0) {
            $hoaDon->trang_thai = 'Đã thanh toán';
            $hoaDon->da_thanh_toan = true;
            if (!$hoaDon->ngay_thanh_toan) {
                $hoaDon->ngay_thanh_toan = now();
            }
            $hoaDon->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Xác nhận thanh toán slot thành công!',
            'paid_slots' => $paidSlots,
            'total_slots' => $totalSlots,
            'is_completed' => $paidSlots >= $totalSlots,
            'status' => $slotPayment->trang_thai,
        ]);
    }

    /**
     * Thanh toán điện nước theo slot
     */
    public function thanhToanUtilities(Request $request, $hoaDonId, $utilitiesPaymentId)
{
    $request->validate([
        'hinh_thuc_thanh_toan' => 'required|in:tien_mat,chuyen_khoan',
        'ghi_chu' => 'nullable|string|max:500',
        'anh_chuyen_khoan' => 'nullable|image|max:4096',
        'action' => 'nullable|in:student_submit,admin_confirm',
    ]);

    $action = $request->input('action', 'student_submit');

    $hoaDon = HoaDon::findOrFail($hoaDonId);
    $utilitiesPayment = HoaDonUtilitiesPayment::where('hoa_don_id', $hoaDonId)
        ->findOrFail($utilitiesPaymentId);

    if ($action === 'admin_confirm') {
        // Nếu slot đã thanh toán rồi thì bỏ qua
        if ($utilitiesPayment->da_thanh_toan) {
            return response()->json([
                'success' => true,
                'message' => 'Khoản điện · nước này đã được xác nhận trước đó.',
            ]);
        }

        // Cập nhật slot
        $utilitiesPayment->update([
            'da_thanh_toan' => true,
            'trang_thai' => HoaDonUtilitiesPayment::TRANG_THAI_DA_THANH_TOAN,
            'ngay_thanh_toan' => now(),
            'hinh_thuc_thanh_toan' => $request->hinh_thuc_thanh_toan,
            'ghi_chu' => $request->ghi_chu,
            'xac_nhan_boi' => Auth::id(),
        ]);
    } else {
        // Sinh viên gửi yêu cầu thanh toán
        if ($utilitiesPayment->da_thanh_toan) {
            return response()->json([
                'success' => false,
                'message' => 'Khoản điện · nước này đã được thanh toán.',
            ], 409);
        }

        if ($utilitiesPayment->trang_thai === HoaDonUtilitiesPayment::TRANG_THAI_CHO_XAC_NHAN) {
            return response()->json([
                'success' => false,
                'message' => 'Khoản điện · nước đang chờ xác nhận từ ban quản lý.',
            ], 409);
        }

        $utilitiesPayment->trang_thai = HoaDonUtilitiesPayment::TRANG_THAI_CHO_XAC_NHAN;
        $utilitiesPayment->client_requested_at = now();
        $utilitiesPayment->client_ghi_chu = $request->ghi_chu;
        $utilitiesPayment->hinh_thuc_thanh_toan = $request->hinh_thuc_thanh_toan;

        if ($request->hasFile('anh_chuyen_khoan')) {
            $storedPath = $request->file('anh_chuyen_khoan')->store('utilities-payments', 'public');
            $utilitiesPayment->client_transfer_image_path = $storedPath;
        }

        $utilitiesPayment->save();

        return response()->json([
            'success' => true,
            'message' => 'Đã gửi yêu cầu thanh toán điện · nước, vui lòng chờ xác nhận.',
            'status' => $utilitiesPayment->trang_thai,
        ]);
    }

    // Làm mới dữ liệu hóa đơn
    $hoaDon->refresh();

    $totalUtilities = $hoaDon->utilitiesPayments->count();
    $paidUtilities = $hoaDon->utilitiesPayments->where('da_thanh_toan', true)->count();

    // Nếu tất cả slot đã thanh toán thì cập nhật hóa đơn tổng
    if ($paidUtilities >= $totalUtilities && $totalUtilities > 0 && !$hoaDon->da_thanh_toan_dien_nuoc) {
        $hoaDon->update([
            'da_thanh_toan_dien_nuoc' => true,
            'ngay_thanh_toan_dien_nuoc' => $hoaDon->ngay_thanh_toan_dien_nuoc ?? now(),
            'hinh_thuc_thanh_toan_dien_nuoc' => $request->hinh_thuc_thanh_toan,
            'ghi_chu_thanh_toan_dien_nuoc' => $request->ghi_chu,
            'trang_thai' => 'Đã thanh toán', // thêm dòng này để hiển thị đúng
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Xác nhận thanh toán điện · nước thành công!',
        'paid_slots' => $paidUtilities,
        'total_slots' => $totalUtilities,
        'is_completed' => $paidUtilities >= $totalUtilities,
        'status' => $utilitiesPayment->trang_thai,
    ]);
}


    /**
     * Đánh dấu hóa đơn đã gửi cho sinh viên (hiển thị ở client)
     */
    public function sendToClient(Request $request, $id)
    {
        $hoaDon = HoaDon::with(['phong.slots.sinhVien', 'slotPayments'])->findOrFail($id);

        if (!$hoaDon->phong) {
            return back()->with('error', 'Không thể gửi vì chưa xác định phòng.');
        }

        $type = $request->input('type', 'tien-phong');

        if ($type === 'dien-nuoc') {
            if ($hoaDon->sent_dien_nuoc_to_client) {
                return back()->with('info', 'Hóa đơn điện · nước đã được gửi trước đó.');
            }

            $this->enrichHoaDonWithPhongPricing($hoaDon);
            $this->attachSlotBreakdown($hoaDon);
            $this->initializeUtilitiesPayments($hoaDon);

            $hoaDon->sent_dien_nuoc_to_client = true;
            $hoaDon->sent_dien_nuoc_at = now();
            if (isset($hoaDon->slot_breakdowns)) {
                unset($hoaDon->slot_breakdowns);
            }
            $hoaDon->save();

            return back()->with('success', 'Đã gửi hóa đơn điện · nước đến sinh viên.');
        }

        if ($hoaDon->sent_to_client) {
            return back()->with('info', 'Hóa đơn tiền phòng đã được gửi cho sinh viên.');
        }

        $this->enrichHoaDonWithPhongPricing($hoaDon);
        $this->attachSlotBreakdown($hoaDon);
        $this->initializeSlotPayments($hoaDon);

        $hoaDon->sent_to_client = true;
        $hoaDon->sent_to_client_at = now();

        // Chỉ là dữ liệu tính toán tạm thời, không lưu vào DB
        if (isset($hoaDon->slot_breakdowns)) {
            unset($hoaDon->slot_breakdowns);
        }

        $hoaDon->save();

        return back()->with('success', 'Đã gửi hóa đơn tiền phòng đến sinh viên.');
    }

    /**
     * Chuẩn bị danh sách hóa đơn kèm dữ liệu tính toán dùng chung cho nhiều trang quản lý.
     */
    protected function prepareHoaDonListing(Request $request): array
    {
        $trangThai = $request->get('trang_thai');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $khu = $request->get('khu');
        $phongId = $request->get('phong_id');
        $isUtilitiesView = $request->routeIs('hoadon.diennuoc');

        $hoaDons = HoaDon::with(['phong.khu'])
            ->when($isUtilitiesView, function ($query) {
                $query->where('invoice_type', HoaDon::LOAI_DIEN_NUOC);
            }, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('invoice_type', HoaDon::LOAI_TIEN_PHONG)
                        ->orWhereNull('invoice_type');
                });
            })
            ->when($khu, function ($query) use ($khu) {
                $query->whereHas('phong.khu', function ($q) use ($khu) {
                    $q->where('ten_khu', $khu);
                });
            })
            ->when($phongId, function ($query) use ($phongId) {
                $query->where('phong_id', $phongId);
            })
            ->when($trangThai === 'da_thanh_toan', function ($query) use ($isUtilitiesView) {
                return $isUtilitiesView
                    ? $query->where('da_thanh_toan_dien_nuoc', true)
                    : $query->where('da_thanh_toan', true);
            })
            ->when($trangThai === 'chua_thanh_toan', function ($query) use ($isUtilitiesView) {
                return $isUtilitiesView
                    ? $query->where('da_thanh_toan_dien_nuoc', false)
                    : $query->where('da_thanh_toan', false);
            })
            ->when($fromDate, fn($q) => $q->whereDate('created_at', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('created_at', '<=', $toDate))
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($hoaDon) {
                $so_dien = max(0, ($hoaDon->so_dien_moi ?? 0) - ($hoaDon->so_dien_cu ?? 0));
                $so_nuoc = max(0, ($hoaDon->so_nuoc_moi ?? 0) - ($hoaDon->so_nuoc_cu ?? 0));

                $hoaDon->khoang_thoi_gian = ($hoaDon->created_at ? $hoaDon->created_at->format('d/m/Y') : '-') . ' → ' .
                    ($hoaDon->ngay_thanh_toan ? \Carbon\Carbon::parse($hoaDon->ngay_thanh_toan)->format('d/m/Y') : '-');

                $this->enrichHoaDonWithPhongPricing($hoaDon);
                $giaPhong = $hoaDon->tien_phong_slot ?? 0;

                $hoaDon->gia_phong = $giaPhong;
                $hoaDon->san_luong_dien = $so_dien;
                $hoaDon->san_luong_nuoc = $so_nuoc;
                $hoaDon->tien_dien = $so_dien * ($hoaDon->don_gia_dien ?? 0);
                $hoaDon->tien_nuoc = $so_nuoc * ($hoaDon->don_gia_nuoc ?? 0);
                $hoaDon->thanh_tien = $hoaDon->tien_dien + $hoaDon->tien_nuoc + $giaPhong;

                return $hoaDon;
            });

        $dsPhongs = Phong::all();

        return [$hoaDons, $dsPhongs];
    }

    public function xacNhanUtilitiesSlot($slotId, Request $request)
{
    $data = $request->validate([
        'hinh_thuc_thanh_toan' => 'required|in:tien_mat,chuyen_khoan',
        'ghi_chu' => 'nullable|string|max:255',
    ]);

    // Tìm slot cần xác nhận
    $slot = HoaDonUtilitiesPayment::findOrFail($slotId);

    if ($slot->da_thanh_toan) {
        return response()->json([
            'success' => true,
            'message' => 'Slot này đã được xác nhận trước đó.',
        ]);
    }

    // Cập nhật slot
    $slot->update([
        'da_thanh_toan' => true,
        'trang_thai' => HoaDonUtilitiesPayment::TRANG_THAI_DA_THANH_TOAN,
        'ngay_thanh_toan' => now(),
        'hinh_thuc_thanh_toan' => $data['hinh_thuc_thanh_toan'],
        'ghi_chu' => $data['ghi_chu'] ?? 'Xác nhận nhanh bởi BQL',
        'xac_nhan_boi' => Auth::id(),
    ]);

    // Kiểm tra hóa đơn tổng
    $hoaDon = $slot->hoaDon; // Quan hệ belongsTo
    $totalSlots = $hoaDon->utilitiesPayments()->count();
    $paidSlots = $hoaDon->utilitiesPayments()->where('da_thanh_toan', true)->count();

    if ($paidSlots >= $totalSlots && $totalSlots > 0) {
        $hoaDon->update([
            'da_thanh_toan_dien_nuoc' => true,
            'ngay_thanh_toan_dien_nuoc' => now(),
            'hinh_thuc_thanh_toan_dien_nuoc' => $data['hinh_thuc_thanh_toan'],
            'ghi_chu_thanh_toan_dien_nuoc' => $data['ghi_chu'] ?? 'Xác nhận nhanh bởi BQL',
            'trang_thai' => 'Đã thanh toán', // Cập nhật trạng thái hóa đơn
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => '✅ Đã xác nhận slot thành công!',
    ]);
}

}
