<?php

namespace App\Http\Controllers;

use App\Http\Requests\PhongRequest;
use App\Models\Phong;
use App\Models\KhoTaiSan;
use App\Models\TaiSan;
use App\Models\Slot;
use App\Models\SinhVien;
use App\Models\Khu;
use Illuminate\Support\Facades\Schema;
use App\Exceptions\PhongException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class PhongController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // nếu dev muốn mở, có thể tạm comment
    }

    /**
     * Hiển thị danh sách phòng với filter/pagination
     */
    public function index(Request $request)
    {
        try {
            $q = Phong::query()->with('khu');

            // Validate và filter
            if ($request->filled('khu_id')) {
                $q->where('khu_id', $request->khu_id);
            }
            if ($request->filled('loai_phong')) {
                $q->where('loai_phong', $request->loai_phong);
            }
            if ($request->filled('gioi_tinh')) {
                $q->where('gioi_tinh', $request->gioi_tinh);
            }
            if ($request->filled('trang_thai')) {
                $q->where('trang_thai', $request->trang_thai);
            }
            if ($request->filled('search')) {
                $search = trim($request->search);
                $q->where('ten_phong', 'like', '%' . $search . '%');
            }

            // Hiển thị tất cả phòng
            $phongs = $q->orderBy('ten_phong')->get();

            // Thống kê tổng quan
            $totals = [
                'total' => Phong::count(),
                'trong' => Phong::where('trang_thai', 'Trống')->count(),
                'da_o' => Phong::where('trang_thai', 'Đã ở')->count(),
                'bao_tri' => Phong::where('trang_thai', 'Bảo trì')->count(),
            ];

            return view('phong.index', compact('phongs', 'totals'));
        } catch (Exception $e) {
            Log::error('Lỗi khi load danh sách phòng: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Có lỗi xảy ra khi tải danh sách phòng. Vui lòng thử lại!');
        }
    }

    public function create()
    {
        try {
            $khoTaiSans = KhoTaiSan::where('so_luong', '>', 0)->orderBy('ten_tai_san')->get();
            $khus = Schema::hasTable('khu') ? Khu::orderBy('ten_khu')->get() : collect();
            return view('phong.create', compact('khoTaiSans', 'khus'));
        } catch (Exception $e) {
            Log::error('Lỗi khi load form tạo phòng: ' . $e->getMessage());
            return redirect()->route('phong.index')->with('error', 'Không thể tải form tạo phòng!');
        }
    }

    public function store(PhongRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            // Phòng mới luôn khởi tạo ở trạng thái "Trống"
            $data['trang_thai'] = 'Trống';

            // Nếu chọn khu, ép giới tính phòng theo khu
            if (!empty($data['khu_id'])) {
                $khu = Khu::findOrFail($data['khu_id']);
                $data['gioi_tinh'] = $khu->gioi_tinh; // nam/nữ theo khu
            }

            // Validate assets from warehouse (optional)
            $assets = $request->input('assets', []);
            $assets = is_array($assets) ? array_filter($assets, function ($qty) {
                return is_numeric($qty) && (int)$qty > 0;
            }) : [];
            if (!empty($assets)) {
                foreach ($assets as $khoId => $qty) {
                    $kho = KhoTaiSan::lockForUpdate()->find($khoId);
                    if (!$kho) {
                        throw new Exception('Tài sản kho không tồn tại: ID ' . $khoId);
                    }
                    if ($kho->so_luong < (int)$qty) {
                        throw new Exception('Kho "' . $kho->ten_tai_san . '" không đủ số lượng (' . $kho->so_luong . ' < ' . (int)$qty . ')');
                    }
                }
            }

            // Kiểm tra tên phòng trùng
            $exists = Phong::where('ten_phong', $data['ten_phong'])->exists();
            if ($exists) {
                throw PhongException::tenPhongTrung($data['ten_phong']);
            }

            // Xử lý upload hình ảnh
            if ($request->hasFile('hinh_anh')) {
                try {
                    $file = $request->file('hinh_anh');

                    // Validate file
                    if (!$file->isValid()) {
                        throw PhongException::uploadFailed('File không hợp lệ');
                    }

                    // Check file size (max 5MB)
                    if ($file->getSize() > 5 * 1024 * 1024) {
                        throw PhongException::uploadFailed('Kích thước file vượt quá 5MB');
                    }

                    // Check file extension
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    $extension = strtolower($file->getClientOriginalExtension());
                    if (!in_array($extension, $allowedExtensions)) {
                        throw PhongException::uploadFailed('Chỉ chấp nhận file ảnh: jpg, jpeg, png, gif, webp');
                    }

                    $path = $file->store('phong', 'public');
                    $data['hinh_anh'] = $path;
                } catch (Exception $e) {
                    Log::error('Lỗi upload hình ảnh phòng: ' . $e->getMessage());
                    throw PhongException::uploadFailed($e->getMessage());
                }
            }

            // Tạo phòng mới
            $phong = Phong::create($data);

            // Tự động tạo slots theo sức chứa
            if (isset($data['suc_chua']) && $data['suc_chua'] > 0) {
                for ($i = 1; $i <= $data['suc_chua']; $i++) {
                    Slot::create([
                        'phong_id' => $phong->id,
                        'ma_slot' => $data['ten_phong'] . '-' . sprintf('%02d', $i),
                        'ghi_chu' => 'Slot tự động tạo khi tạo phòng'
                    ]);
                }

                // Cập nhật loại phòng theo số slot
                if (method_exists($phong, 'updateLoaiPhongFromSlots')) {
                    $phong->updateLoaiPhongFromSlots();
                }
            }

            // Nếu có chọn tài sản từ kho, tiến hành cấp cho phòng và phân cho slots
            if (!empty($assets)) {
                // Chỉ tự động bàn giao cho các slot đã có sinh viên ở
                $slots = $phong->slots()
                    ->whereNotNull('sinh_vien_id')
                    ->orderBy('id')
                    ->get();
                $slotCount = $slots->count();

                foreach ($assets as $khoId => $qtyRaw) {
                    $qty = (int)$qtyRaw;
                    if ($qty <= 0) {
                        continue;
                    }

                    $kho = KhoTaiSan::lockForUpdate()->findOrFail($khoId);

                    // Tạo tài sản cấp cho phòng
                    $taiSan = TaiSan::create([
                        'kho_tai_san_id' => $kho->id,
                        'ten_tai_san' => $kho->ten_tai_san,
                        'so_luong' => $qty,
                        'tinh_trang' => 'Mới',
                        'tinh_trang_hien_tai' => null,
                        'phong_id' => $phong->id,
                        'hinh_anh' => $kho->hinh_anh,
                    ]);

                    // Trừ kho
                    $kho->so_luong = max(0, (int)$kho->so_luong - $qty);
                    $kho->save();

                    // Phân tài sản cho slot nếu có slot
                    if ($slotCount > 0) {
                        $base = intdiv($qty, $slotCount);
                        $rem = $qty % $slotCount;
                        foreach ($slots as $i => $slot) {
                            $assign = $base + ($i < $rem ? 1 : 0);
                            if ($assign > 0) {
                                $slot->taiSans()->attach($taiSan->id, ['so_luong' => $assign]);
                            }
                        }
                    }
                }
            }

            DB::commit();

            Log::info('Tạo phòng thành công', ['phong_id' => $phong->id, 'ten_phong' => $phong->ten_phong]);
            return redirect()->route('phong.index')->with('status', 'Thêm phòng thành công! Tài sản đã được cấp cho phòng.');
        } catch (PhongException $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi tạo phòng: ' . $e->getMessage(), [
                'data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi tạo phòng. Vui lòng thử lại!');
        }
    }

    public function edit(Phong $phong)
    {
        try {
            $khus = Schema::hasTable('khu') ? Khu::orderBy('ten_khu')->get() : collect();
            // Hiển thị bảng chọn tài sản giống như tạo mới
            $khoTaiSans = KhoTaiSan::where('so_luong', '>', 0)->orderBy('ten_tai_san')->get();
            return view('phong.edit', compact('phong', 'khus', 'khoTaiSans'));
        } catch (Exception $e) {
            Log::error('Lỗi khi load form sửa phòng: ' . $e->getMessage());
            return redirect()->route('phong.index')->with('error', 'Không thể tải form chỉnh sửa phòng!');
        }
    }

    public function update(PhongRequest $request, Phong $phong)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            // Nếu chọn khu, ép giới tính theo khu
            if (!empty($data['khu_id'])) {
                $khu = Khu::findOrFail($data['khu_id']);
                $data['gioi_tinh'] = $khu->gioi_tinh;
            }

            // Kiểm tra tên phòng trùng (trừ phòng hiện tại)
            $exists = Phong::where('ten_phong', $data['ten_phong'])
                ->where('id', '!=', $phong->id)
                ->exists();
            if ($exists) {
                throw PhongException::tenPhongTrung($data['ten_phong']);
            }

            // Kiểm tra sức chứa không được nhỏ hơn số sinh viên hiện tại
            $soSinhVienHienTai = $phong->usedSlots();
            if (isset($data['suc_chua']) && $data['suc_chua'] < $soSinhVienHienTai) {
                throw new PhongException(
                    "Không thể giảm sức chứa xuống {$data['suc_chua']} vì hiện có {$soSinhVienHienTai} sinh viên đang ở",
                    422
                );
            }

            // Xử lý upload hình ảnh
            if ($request->hasFile('hinh_anh')) {
                try {
                    $file = $request->file('hinh_anh');

                    if (!$file->isValid()) {
                        throw PhongException::uploadFailed('File không hợp lệ');
                    }

                    if ($file->getSize() > 5 * 1024 * 1024) {
                        throw PhongException::uploadFailed('Kích thước file vượt quá 5MB');
                    }

                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    $extension = strtolower($file->getClientOriginalExtension());
                    if (!in_array($extension, $allowedExtensions)) {
                        throw PhongException::uploadFailed('Chỉ chấp nhận file ảnh');
                    }

                    // Xóa ảnh cũ nếu có
                    if ($phong->hinh_anh && Storage::disk('public')->exists($phong->hinh_anh)) {
                        Storage::disk('public')->delete($phong->hinh_anh);
                    }

                    $path = $file->store('phong', 'public');
                    $data['hinh_anh'] = $path;
                } catch (Exception $e) {
                    Log::error('Lỗi upload hình ảnh phòng: ' . $e->getMessage());
                    throw PhongException::uploadFailed($e->getMessage());
                }
            }

            // Cập nhật thông tin phòng
            $oldTotalSlots = $phong->totalSlots();
            $phong->update($data);

            // Đồng bộ số lượng slot theo sức chứa mới
            if (array_key_exists('suc_chua', $data)) {
                $targetCapacity = (int) $data['suc_chua'];
                $currentTotal = $phong->totalSlots();

                if ($currentTotal < $targetCapacity) {
                    // Tăng slot: tạo thêm slot TRỐNG cho đủ sức chứa
                    $need = $targetCapacity - $currentTotal;

                    // Tìm suffix lớn nhất đang dùng để tiếp tục đánh số
                    $maxSuffix = 0;
                    foreach ($phong->slots()->pluck('ma_slot') as $code) {
                        if (preg_match('/-(\d+)$/', (string)$code, $m)) {
                            $maxSuffix = max($maxSuffix, (int)$m[1]);
                        }
                    }

                    for ($i = 1; $i <= $need; $i++) {
                        $maxSuffix++;
                        $code = $phong->ten_phong . '-' . sprintf('%02d', $maxSuffix);
                        // đảm bảo không trùng mã slot trong phòng
                        if (Slot::where('phong_id', $phong->id)->where('ma_slot', $code)->exists()) {
                            // fallback: dùng count-based nếu trùng bất thường
                            $code = $phong->ten_phong . '-' . sprintf('%02d', $currentTotal + $i);
                        }
                        Slot::create([
                            'phong_id' => $phong->id,
                            'ma_slot'  => $code,
                            'ghi_chu'  => 'Slot tự động thêm khi tăng sức chứa'
                        ]);
                    }
                } elseif ($currentTotal > $targetCapacity && method_exists($phong, 'pruneEmptySlotsToCapacity')) {
                    // Giảm slot: chỉ xóa slot TRỐNG cho khớp sức chứa
                    $phong->pruneEmptySlotsToCapacity($targetCapacity);
                }

                // Cập nhật loại phòng theo tổng slot hiện tại
                if (method_exists($phong, 'updateLoaiPhongFromSlots')) {
                    $phong->updateLoaiPhongFromSlots();
                }
            }

            // Cập nhật trạng thái dựa trên tình trạng slot/sức chứa
            if (method_exists($phong, 'updateStatusBasedOnCapacity')) {
                $phong->updateStatusBasedOnCapacity();
            }

            // Nếu người dùng chọn cấp thêm tài sản từ kho khi sửa phòng
            $assets = $request->input('assets', []);
            $assets = is_array($assets) ? array_filter($assets, function ($qty) {
                return is_numeric($qty) && (int)$qty > 0;
            }) : [];
            if (!empty($assets)) {
                // Chỉ tự động bàn giao cho các slot đã có sinh viên ở
                $slots = $phong->slots()
                    ->whereNotNull('sinh_vien_id')
                    ->orderBy('id')
                    ->get();
                $slotCount = $slots->count();

                foreach ($assets as $khoId => $qtyRaw) {
                    $qty = (int)$qtyRaw;
                    if ($qty <= 0) {
                        continue;
                    }

                    $kho = KhoTaiSan::lockForUpdate()->findOrFail($khoId);
                    if ($kho->so_luong < $qty) {
                        throw new Exception('Kho "' . $kho->ten_tai_san . '" không đủ số lượng (' . $kho->so_luong . ' < ' . $qty . ')');
                    }

                    // Tạo tài sản cấp cho phòng
                    $taiSan = TaiSan::create([
                        'kho_tai_san_id' => $kho->id,
                        'ten_tai_san' => $kho->ten_tai_san,
                        'so_luong' => $qty,
                        'tinh_trang' => 'Mới',
                        'tinh_trang_hien_tai' => null,
                        'phong_id' => $phong->id,
                        'hinh_anh' => $kho->hinh_anh,
                    ]);

                    // Trừ kho
                    $kho->so_luong = max(0, (int)$kho->so_luong - $qty);
                    $kho->save();

                    // Phân tài sản cho slot nếu có slot
                    if ($slotCount > 0) {
                        $base = intdiv($qty, $slotCount);
                        $rem = $qty % $slotCount;
                        foreach ($slots as $i => $slot) {
                            $assign = $base + ($i < $rem ? 1 : 0);
                            if ($assign > 0) {
                                $slot->taiSans()->attach($taiSan->id, ['so_luong' => $assign]);
                            }
                        }
                    }
                }
            }

            DB::commit();

            Log::info('Cập nhật phòng thành công', ['phong_id' => $phong->id]);
            return redirect()->route('phong.index')->with('status', 'Cập nhật phòng thành công!');
        } catch (PhongException $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật phòng: ' . $e->getMessage(), [
                'phong_id' => $phong->id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi cập nhật phòng. Vui lòng thử lại!');
        }
    }

    public function show($id)
    {
        try {
            $phong = Phong::with([
                'slots.sinhVien',
                'slots.taiSans.khoTaiSan',
                'slots.taiSans.lichBaoTri' => function ($q) {
                    $q->latest('ngay_bao_tri');
                }
            ])->findOrFail($id);

            // Chỉ lấy sinh viên đã duyệt và CHƯA được gán vào slot nào
            $assignedIds = Slot::whereNotNull('sinh_vien_id')->pluck('sinh_vien_id');
            $sinhViens = SinhVien::where('trang_thai_ho_so', 'Đã duyệt')
                ->whereNotIn('id', $assignedIds)
                ->orderBy('ho_ten')
                ->get();

            return view('phong.show', compact('phong', 'sinhViens'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Phòng không tồn tại: ID ' . $id);
            return redirect()->route('phong.index')->with('error', 'Phòng không tồn tại trong hệ thống!');
        } catch (Exception $e) {
            Log::error('Lỗi khi xem chi tiết phòng: ' . $e->getMessage(), [
                'phong_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('phong.index')->with('error', 'Có lỗi xảy ra khi xem chi tiết phòng!');
        }
    }

    public function destroy(Phong $phong)
    {
        try {
            // Dùng logic tập trung tại model
            if (method_exists($phong, 'canDelete')) {
                $check = $phong->canDelete();
                if (!$check['can_delete']) {
                    $message = 'Không thể xóa phòng ' . $phong->ten_phong . ': ' . implode('; ', $check['errors']);
                    return redirect()->route('phong.index')->with('error', $message);
                }
            } else {
                // Fallback kiểm tra cơ bản nếu thiếu method
                $slotsCoSinhVien = $phong->slots()->whereNotNull('sinh_vien_id')->count();
                if ($slotsCoSinhVien > 0) {
                    return redirect()->route('phong.index')
                        ->with('error', "Không thể xóa phòng {$phong->ten_phong} vì còn {$slotsCoSinhVien} sinh viên đang ở");
                }
            }

            DB::beginTransaction();

            // Xóa tất cả slots của phòng
            $phong->slots()->delete();

            // Xóa hình ảnh nếu có
            if ($phong->hinh_anh && Storage::disk('public')->exists($phong->hinh_anh)) {
                Storage::disk('public')->delete($phong->hinh_anh);
            }

            $tenPhong = $phong->ten_phong;
            $phong->delete();

            DB::commit();

            Log::info('Xóa phòng thành công', ['ten_phong' => $tenPhong]);
            return redirect()->route('phong.index')->with('status', 'Xóa phòng thành công!');
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            // Cố gắng suy luận lỗi FK để hiện thông điệp rõ ràng
            $message = 'Có lỗi xảy ra khi xóa phòng. Vui lòng thử lại!';
            $sqlState = $e->getCode();
            $msg = $e->getMessage();
            if (str_contains($msg, 'foreign key') || str_contains(strtolower($msg), 'constraint')) {
                $message = 'Không thể xóa vì còn dữ liệu liên quan (sinh viên, slot, tài sản hoặc sự cố). Hãy xóa/di chuyển các dữ liệu liên quan trước.';
            }
            Log::error('Lỗi QueryException khi xóa phòng: ' . $e->getMessage(), [
                'phong_id' => $phong->id,
                'sql_state' => $sqlState,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('phong.index')->with('error', $message);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi xóa phòng: ' . $e->getMessage(), [
                'phong_id' => $phong->id,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('phong.index')->with('error', 'Có lỗi xảy ra khi xóa phòng. Vui lòng thử lại!');
        }
    }

    /**
     * AJAX đổi trạng thái nhanh (POST)
     */
    public function changeStatus(Request $request, Phong $phong)
    {
        try {
            $validated = $request->validate([
                'trang_thai' => 'required|in:Trống,Đã ở,Bảo trì'
            ]);

            // Kiểm tra logic nghiệp vụ
            $soSinhVienHienTai = $phong->usedSlots();

            // Không cho đổi thành "Trống" nếu còn sinh viên
            if ($validated['trang_thai'] === 'Trống' && $soSinhVienHienTai > 0) {
                return response()->json([
                    'ok' => false,
                    'message' => "Không thể đổi sang trạng thái 'Trống' vì còn {$soSinhVienHienTai} sinh viên đang ở"
                ], 422);
            }

            $phong->trang_thai = $validated['trang_thai'];
            $phong->save();

            Log::info('Đổi trạng thái phòng', [
                'phong_id' => $phong->id,
                'trang_thai_moi' => $phong->trang_thai
            ]);

            return response()->json([
                'ok' => true,
                'trang_thai' => $phong->trang_thai,
                'message' => 'Đổi trạng thái phòng thành công'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('Lỗi khi đổi trạng thái phòng: ' . $e->getMessage(), [
                'phong_id' => $phong->id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'ok' => false,
                'message' => 'Có lỗi xảy ra khi đổi trạng thái phòng'
            ], 500);
        }
    }
}
