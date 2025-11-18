<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\HoaDon;
use App\Exports\HoaDonExport;
use App\Models\Phong;
use App\Models\SinhVien;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\HoaDonDienNuocImport;
use Illuminate\Support\Facades\Mail;

use Illuminate\Http\Request;




class HoaDonController extends Controller
{

    public function importHoaDon(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new HoaDonDienNuocImport, $request->file('file'));

        return back()->with('success', 'Nhập hóa đơn thành công!');
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
        $hoaDon = HoaDon::findOrFail($id);
        $hoaDon->trang_thai = 'Đã thanh toán';
        $hoaDon->da_thanh_toan = true;
        $hoaDon->ngay_thanh_toan = now();
        $hoaDon->hinh_thuc_thanh_toan = $request->hinh_thuc_thanh_toan;
        $request->validate(['ghi_chu_thanh_toan' => 'required|string|max:255',]);
        $hoaDon->ghi_chu_thanh_toan = $request->ghi_chu_thanh_toan;

        $hoaDon->save();

        $bienLaiHtml = $this->hienThiBienLai($hoaDon);

    return response()->json([
        'success' => true,
        'bien_lai' => $bienLaiHtml,
    ]);
    }

    public function show($id)
    {
        $hoaDon = HoaDon::with('phong.khu', 'phong.slots.sinhVien')->findOrFail($id);

        // Tính toán lại nếu cần
        $so_dien = $hoaDon->so_dien_moi - $hoaDon->so_dien_cu;
        $so_nuoc = $hoaDon->so_nuoc_moi - $hoaDon->so_nuoc_cu;

        $hoaDon->tien_dien = $so_dien * $hoaDon->don_gia_dien;
        $hoaDon->tien_nuoc = $so_nuoc * $hoaDon->don_gia_nuoc;
        $this->enrichHoaDonWithPhongPricing($hoaDon);
        $hoaDon->thanh_tien = $hoaDon->tien_dien + $hoaDon->tien_nuoc + $hoaDon->tien_phong_slot;
        $this->attachSlotBreakdown($hoaDon);

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

   public function lichSu(Request $request)
{
    // Chỉ lấy hóa đơn đã thanh toán
    $query = HoaDon::with('phong')->where('da_thanh_toan', true);

    // 👉 Lọc theo ngày cụ thể nếu có
    if ($request->filled('ngay')) {
        $query->whereDate('ngay_thanh_toan', $request->ngay);
    }

    // 👉 Sắp xếp mới nhất lên đầu và phân trang
    $hoaDons = $query->orderByDesc('ngay_thanh_toan')->paginate(10);

    return view('hoadon.lichsu', compact('hoaDons'));
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
     * Lấy thông tin đơn giá/slot và tiền phòng của phòng
     */
    protected function getPhongPricing(?Phong $phong): array
    {
        if (!$phong) {
            return [
                'slot_unit_price' => 0,
                'slot_count' => 0,
                'tien_phong' => 0,
            ];
        }

        $slotUnitPrice = $phong->giaSlot();
        $occupiedSlotCount = $phong->billableSlotCount(true);

        return [
            'slot_unit_price' => $slotUnitPrice,
            'slot_count' => $occupiedSlotCount,
            'tien_phong' => $slotUnitPrice * $occupiedSlotCount,
        ];
    }

    /**
     * Gắn thông tin tiền phòng slot vào đối tượng hóa đơn
     */
    protected function enrichHoaDonWithPhongPricing(HoaDon $hoaDon): HoaDon
    {
        $pricing = $this->getPhongPricing($hoaDon->phong);

        $hoaDon->tien_phong_slot = $pricing['tien_phong'];
        $hoaDon->slot_unit_price = $pricing['slot_unit_price'];
        $hoaDon->slot_billing_count = $pricing['slot_count'];

        return $hoaDon;
    }

    /**
     * Gắn thông tin phân bổ chi phí theo slot vào hóa đơn
     */
    protected function attachSlotBreakdown(HoaDon $hoaDon): HoaDon
    {
        if (!$hoaDon->relationLoaded('phong')) {
            $hoaDon->load('phong');
        }

        if ($hoaDon->phong) {
            $hoaDon->phong->loadMissing('slots.sinhVien');
        }

        $hoaDon->slot_breakdowns = $this->buildSlotBreakdown($hoaDon);

        return $hoaDon;
    }

    /**
     * Tạo dữ liệu phân bổ chi phí điện/nước/phòng cho từng slot
     */
    protected function buildSlotBreakdown(HoaDon $hoaDon): array
    {
        $phong = $hoaDon->phong;
        if (!$phong) {
            return [];
        }

        $slots = $phong->slots
            ->filter(function ($slot) {
                return !is_null($slot->sinh_vien_id) || $slot->sinhVien;
            })
            ->sortBy(function ($slot) {
                return $slot->ma_slot ?? $slot->id;
            })
            ->values();

        $slotCount = (int) ($hoaDon->slot_billing_count ?? $slots->count());
        if ($slotCount <= 0) {
            return [];
        }

        $dienShares = $this->splitAmountAcrossSlots($slotCount, (int) round($hoaDon->tien_dien ?? 0));
        $nuocShares = $this->splitAmountAcrossSlots($slotCount, (int) round($hoaDon->tien_nuoc ?? 0));
        $phongShares = $this->splitAmountAcrossSlots($slotCount, (int) round($hoaDon->tien_phong_slot ?? 0));

        $breakdowns = [];
        for ($i = 0; $i < $slotCount; $i++) {
            $slot = $slots->get($i);
            $label = $slot ? ($slot->ma_slot ?? 'Slot ' . ($i + 1)) : 'Slot ' . ($i + 1);

            $breakdowns[] = [
                'label' => $label,
                'sinh_vien' => optional($slot?->sinhVien)->ho_ten ?? 'Chưa có sinh viên',
                'tien_dien' => $dienShares[$i] ?? 0,
                'tien_nuoc' => $nuocShares[$i] ?? 0,
                'tien_phong' => $phongShares[$i] ?? 0,
            ];
        }

        return $breakdowns;
    }

    /**
     * Chia đều số tiền cho từng slot và xử lý phần dư để đảm bảo tổng chính xác
     */
    protected function splitAmountAcrossSlots(int $slotCount, int $total): array
    {
        if ($slotCount <= 0) {
            return [];
        }

        $base = intdiv($total, $slotCount);
        $remainder = $total - ($base * $slotCount);

        $shares = array_fill(0, $slotCount, $base);
        for ($i = 0; $i < $remainder; $i++) {
            $shares[$i] += 1;
        }

        return $shares;
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

        $hoaDons = HoaDon::with(['phong.khu'])
            ->when($khu, function ($query) use ($khu) {
                $query->whereHas('phong.khu', function ($q) use ($khu) {
                    $q->where('ten_khu', $khu);
                });
            })
            ->when($phongId, function ($query) use ($phongId) {
                $query->where('phong_id', $phongId);
            })
            ->when($trangThai === 'da_thanh_toan', fn($q) => $q->where('da_thanh_toan', true))
            ->when($trangThai === 'chua_thanh_toan', fn($q) => $q->where('da_thanh_toan', false))
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
}
