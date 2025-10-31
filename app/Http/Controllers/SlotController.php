<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slot;
use App\Models\Phong;
use App\Models\SinhVien;
use App\Exceptions\SlotException;
use App\Exceptions\PhongException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class SlotController extends Controller
{
    /**
     * Tạo slot mới cho phòng
     */
    public function store(Request $request, $phong)
    {
        DB::beginTransaction();
        try {
            // Ensure phong exists
            $phongModel = Phong::findOrFail($phong);

            // Kiểm tra phòng có đang bảo trì không
            if ($phongModel->trang_thai === 'Bảo trì') {
                throw PhongException::phongBaoTri($phongModel->ten_phong);
            }

            // Validate input
            $data = $request->validate([
                'ma_slot'      => ['required', 'string', 'max:255'],
                'ghi_chu'      => ['nullable', 'string', 'max:500'],
                'sinh_vien_id' => ['nullable', 'integer', Rule::exists('sinh_vien', 'id')],
                'cs_vat_chat'  => ['nullable', 'string'],
                'hinh_anh'     => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
            ]);

            // Kiểm tra mã slot trùng trong cùng phòng
            $exists = Slot::where('phong_id', $phongModel->id)
                ->where('ma_slot', $data['ma_slot'])
                ->exists();
            if ($exists) {
                throw SlotException::maSlotTrung($data['ma_slot'], $phongModel->ten_phong);
            }

            // Đồng bộ sức chứa khi tạo slot mới: nếu đã đạt sức chứa, tự tăng sức chứa thêm 1
            $soSlotHienTai = $phongModel->totalSlots();
            if ($soSlotHienTai >= (int)$phongModel->suc_chua) {
                $phongModel->suc_chua = $soSlotHienTai + 1;
                $phongModel->save();
            }

            // Nếu gán sinh viên ngay khi tạo slot
            if (!empty($data['sinh_vien_id'])) {
                $sinhVien = SinhVien::findOrFail($data['sinh_vien_id']);

                // Kiểm tra trạng thái hồ sơ
                if ($sinhVien->trang_thai_ho_so !== 'Đã duyệt') {
                    throw SlotException::sinhVienChuaDuocDuyet(
                        $sinhVien->ho_ten, 
                        $sinhVien->trang_thai_ho_so
                    );
                }

                // Kiểm tra sinh viên đã có slot chưa
                $existingSlot = Slot::where('sinh_vien_id', $data['sinh_vien_id'])->first();
                if ($existingSlot) {
                    $phongCu = $existingSlot->phong;
                    throw SlotException::sinhVienDaCoSlot(
                        $sinhVien->ho_ten, 
                        $phongCu ? $phongCu->ten_phong : 'N/A'
                    );
                }

                // Kiểm tra giới tính phù hợp (ưu tiên theo khu nếu có)
                $requiredGender = ($phongModel->khu && $phongModel->khu->gioi_tinh) ? $phongModel->khu->gioi_tinh : $phongModel->gioi_tinh;
                if ($requiredGender !== 'Cả hai' && 
                    $requiredGender !== $sinhVien->gioi_tinh) {
                    throw SlotException::gioiTinhKhongPhuHop(
                        $sinhVien->ho_ten,
                        $sinhVien->gioi_tinh,
                        $requiredGender
                    );
                }
            }

            // Xử lý upload hình ảnh
            $imagePath = null;
            if ($request->hasFile('hinh_anh')) {
                try {
                    $file = $request->file('hinh_anh');
                    
                    if (!$file->isValid()) {
                        throw new Exception('File không hợp lệ');
                    }

                    if ($file->getSize() > 5 * 1024 * 1024) {
                        throw new Exception('Kích thước file vượt quá 5MB');
                    }

                    $imagePath = $file->store('slots', 'public');
                } catch (Exception $e) {
                    Log::error('Lỗi upload hình ảnh slot: ' . $e->getMessage());
                    throw new Exception('Upload hình ảnh thất bại: ' . $e->getMessage());
                }
            }

            // Tạo slot
            $slot = Slot::create([
                'phong_id'     => $phongModel->id,
                'ma_slot'      => $data['ma_slot'],
                'ghi_chu'      => $data['ghi_chu'] ?? null,
                'sinh_vien_id' => $data['sinh_vien_id'] ?? null,
                'cs_vat_chat'  => $data['cs_vat_chat'] ?? null,
                'hinh_anh'     => $imagePath,
            ]);

            // Cập nhật loại phòng theo tổng slots và trạng thái
            if (method_exists($phongModel, 'updateLoaiPhongFromSlots')) {
                $phongModel->updateLoaiPhongFromSlots();
            }
            if (method_exists($phongModel, 'updateStatusBasedOnCapacity')) {
                $phongModel->updateStatusBasedOnCapacity();
            }

            DB::commit();

            Log::info('Tạo slot thành công', [
                'slot_id' => $slot->id,
                'phong_id' => $phongModel->id,
                'ma_slot' => $slot->ma_slot
            ]);

            return response()->json([
                'message' => 'Tạo slot thành công',
                'slot'    => $slot->load('sinhVien'),
            ], 201);

        } catch (SlotException | PhongException $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode() ?: 422);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::warning('Không tìm thấy phòng khi tạo slot: ID ' . $phong);
            return response()->json([
                'message' => 'Phòng không tồn tại trong hệ thống'
            ], 404);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi tạo slot: ' . $e->getMessage(), [
                'phong_id' => $phong,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Có lỗi xảy ra khi tạo slot. Vui lòng thử lại!'
            ], 500);
        }
    }

    /**
     * Gán sinh viên vào slot (hoặc bỏ gán nếu null)
     */
    public function assignStudent(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $slot = Slot::with(['phong', 'sinhVien'])->findOrFail($id);

            // Validate input
            $data = $request->validate([
                'sinh_vien_id' => ['nullable', 'integer', Rule::exists('sinh_vien', 'id')],
                'hinh_anh'     => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
            ]);

            // Kiểm tra phòng có đang bảo trì không
            if ($slot->phong && $slot->phong->trang_thai === 'Bảo trì') {
                throw PhongException::phongBaoTri($slot->phong->ten_phong);
            }

            // Nếu gán sinh viên mới
            if (!empty($data['sinh_vien_id'])) {
                $sinhVien = SinhVien::findOrFail($data['sinh_vien_id']);

                // Kiểm tra trạng thái hồ sơ
                if ($sinhVien->trang_thai_ho_so !== 'Đã duyệt') {
                    throw SlotException::sinhVienChuaDuocDuyet(
                        $sinhVien->ho_ten, 
                        $sinhVien->trang_thai_ho_so
                    );
                }

                // Kiểm tra sinh viên đã được gán vào slot khác chưa
                $otherSlot = Slot::where('sinh_vien_id', $data['sinh_vien_id'])
                    ->where('id', '<>', $slot->id)
                    ->with('phong')
                    ->first();
                    
                if ($otherSlot) {
                    $phongKhac = $otherSlot->phong ? $otherSlot->phong->ten_phong : 'N/A';
                    throw SlotException::sinhVienDaCoSlot($sinhVien->ho_ten, $phongKhac);
                }

                // Kiểm tra giới tính phù hợp
                if ($slot->phong && 
                    $slot->phong->gioi_tinh !== 'Cả hai' && 
                    $slot->phong->gioi_tinh !== $sinhVien->gioi_tinh) {
                    throw SlotException::gioiTinhKhongPhuHop(
                        $sinhVien->ho_ten,
                        $sinhVien->gioi_tinh,
                        $slot->phong->gioi_tinh
                    );
                }
            }

            // Cập nhật thông tin slot
            $slot->sinh_vien_id = $data['sinh_vien_id'] ?? null;
            
            // bỏ trường cs_vat_chat: không còn nhận từ form

            // Xử lý upload hình ảnh
            if ($request->hasFile('hinh_anh')) {
                try {
                    $file = $request->file('hinh_anh');
                    
                    if (!$file->isValid()) {
                        throw new Exception('File không hợp lệ');
                    }

                    if ($file->getSize() > 5 * 1024 * 1024) {
                        throw new Exception('Kích thước file vượt quá 5MB');
                    }

                    // Xóa ảnh cũ nếu có
                    if ($slot->hinh_anh && Storage::disk('public')->exists($slot->hinh_anh)) {
                        Storage::disk('public')->delete($slot->hinh_anh);
                    }

                    $path = $file->store('slots', 'public');
                    $slot->hinh_anh = $path;
                } catch (Exception $e) {
                    Log::error('Lỗi upload hình ảnh slot: ' . $e->getMessage());
                    throw new Exception('Upload hình ảnh thất bại: ' . $e->getMessage());
                }
            }

            $slot->save();

            // Tự động bàn giao bộ CSVC mặc định khi gán sinh viên vào slot
            if (!empty($slot->sinh_vien_id)) {
                try {
                    $this->assignDefaultKitToSlot($slot);
                } catch (\Throwable $e) {
                    Log::warning('Không thể tự động bàn giao kit cho slot', [
                        'slot_id' => $slot->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            DB::commit();

            Log::info('Gán sinh viên vào slot thành công', [
                'slot_id' => $slot->id,
                'sinh_vien_id' => $slot->sinh_vien_id
            ]);

            // Trả về slot kèm thông tin sinh viên
            $slot->load('sinhVien', 'phong');
            return response()->json([
                'message' => 'Gán sinh viên thành công',
                'slot' => $slot
            ]);

        } catch (SlotException | PhongException $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode() ?: 422);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::warning('Không tìm thấy slot: ID ' . $id);
            return response()->json([
                'message' => 'Slot không tồn tại trong hệ thống'
            ], 404);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi gán sinh viên vào slot: ' . $e->getMessage(), [
                'slot_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Có lỗi xảy ra khi gán sinh viên. Vui lòng thử lại!'
            ], 500);
        }
    }

    /**
     * Cập nhật thông tin slot (ghi chú, ảnh)
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $slot = Slot::with('phong')->findOrFail($id);

            // Validate input
            $data = $request->validate([
                'ghi_chu'     => ['nullable', 'string', 'max:500'],
                'hinh_anh'    => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
            ]);

            // Cập nhật thông tin
            if (array_key_exists('ghi_chu', $data)) {
                $slot->ghi_chu = $data['ghi_chu'];
            }
            // bỏ trường cs_vat_chat

            // Xử lý upload hình ảnh
            if ($request->hasFile('hinh_anh')) {
                try {
                    $file = $request->file('hinh_anh');
                    
                    if (!$file->isValid()) {
                        throw new Exception('File không hợp lệ');
                    }

                    if ($file->getSize() > 5 * 1024 * 1024) {
                        throw new Exception('Kích thước file vượt quá 5MB');
                    }

                    // Xóa ảnh cũ nếu có
                    if ($slot->hinh_anh && Storage::disk('public')->exists($slot->hinh_anh)) {
                        Storage::disk('public')->delete($slot->hinh_anh);
                    }

                    $path = $file->store('slots', 'public');
                    $slot->hinh_anh = $path;
                } catch (Exception $e) {
                    Log::error('Lỗi upload hình ảnh slot: ' . $e->getMessage());
                    throw new Exception('Upload hình ảnh thất bại: ' . $e->getMessage());
                }
            }

            $slot->save();

            DB::commit();

            Log::info('Cập nhật slot thành công', ['slot_id' => $slot->id]);

            return response()->json([
                'message' => 'Cập nhật slot thành công',
                'slot' => $slot->load('sinhVien', 'phong')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::warning('Không tìm thấy slot: ID ' . $id);
            return response()->json([
                'message' => 'Slot không tồn tại trong hệ thống'
            ], 404);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật slot: ' . $e->getMessage(), [
                'slot_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Có lỗi xảy ra khi cập nhật slot. Vui lòng thử lại!'
            ], 500);
        }
    }

    /**
     * Lấy slots theo phòng (dùng AJAX trên view)
     */
    public function slotsByPhong($phongId)
    {
        try {
            $phong = Phong::with(['slots.sinhVien'])->findOrFail($phongId);
            
            return response()->json([
                'message' => 'Lấy danh sách slot thành công',
                'phong' => $phong
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Không tìm thấy phòng: ID ' . $phongId);
            return response()->json([
                'message' => 'Phòng không tồn tại trong hệ thống'
            ], 404);
        } catch (Exception $e) {
            Log::error('Lỗi khi lấy danh sách slot: ' . $e->getMessage(), [
                'phong_id' => $phongId,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Có lỗi xảy ra khi lấy danh sách slot'
            ], 500);
        }
    }

    /**
     * Lấy danh sách tài sản của phòng để bàn giao cho slot
     */
    public function assets(Request $request, $slotId)
    {
        try {
            $slot = Slot::with('phong')->findOrFail($slotId);
            $phong = $slot->phong;
            if (!$phong) {
                return response()->json(['message' => 'Slot không thuộc phòng hợp lệ'], 422);
            }

            // Tài sản ở cấp phòng kèm thông tin slot đã nhận
            $assets = \App\Models\TaiSan::with(['slots.sinhVien'])
                ->where('phong_id', $phong->id)
                ->get();

            // Tính số lượng đã gán cho tất cả slot và cho slot hiện tại
            // Gợi ý đề xuất theo bộ mặc định
            $defaultKeywords = ['gối', 'màn', 'chiếu', 'chăn'];

            $response = $assets->map(function($ts) use ($slot, $defaultKeywords) {
                $slots = $ts->slots ?? collect();

                $assignedAll = $slots->sum(function ($slotItem) {
                    return (int) ($slotItem->pivot->so_luong ?? 0);
                });

                $currentSlot = $slots->firstWhere('id', $slot->id);
                $assignedThis = $currentSlot ? (int) ($currentSlot->pivot->so_luong ?? 0) : 0;

                $availableForThisSlot = max(0, (int) $ts->so_luong - $assignedAll + $assignedThis);
                $extraCapacity = max(0, $availableForThisSlot - $assignedThis);

                $otherAssignments = $slots
                    ->filter(function ($slotItem) use ($slot) {
                        return $slotItem->id !== $slot->id && (int) ($slotItem->pivot->so_luong ?? 0) > 0;
                    })
                    ->map(function ($slotItem) {
                        $student = $slotItem->sinhVien;
                        return [
                            'slot_id' => $slotItem->id,
                            'ma_slot' => $slotItem->ma_slot,
                            'so_luong' => (int) ($slotItem->pivot->so_luong ?? 0),
                            'sinh_vien' => $student ? [
                                'id' => $student->id,
                                'ho_ten' => $student->ho_ten,
                                'ma_sinh_vien' => $student->ma_sinh_vien,
                            ] : null,
                        ];
                    })
                    ->values();

                $loai = optional(optional($ts->khoTaiSan)->loai)->ten_loai;
                $name = mb_strtolower(trim($loai ?: $ts->ten_tai_san));
                $suggested = false;
                foreach ($defaultKeywords as $kw) {
                    if (mb_strpos($name, $kw) !== false) { $suggested = true; break; }
                }

                return [
                    'id' => $ts->id,
                    'ten_tai_san' => $ts->ten_tai_san,
                    'hinh_anh' => $ts->hinh_anh ? asset('storage/'.$ts->hinh_anh) : null,
                    'so_luong_phong' => (int) $ts->so_luong,
                    'da_gan_cho_slot_nay' => $assignedThis,
                    'con_lai_co_the_gan' => $availableForThisSlot,
                    'extra_capacity' => $extraCapacity,
                    'khong_the_gan_them' => $extraCapacity <= 0 && $assignedThis <= 0 && $otherAssignments->isNotEmpty(),
                    'dang_duoc_giu' => $otherAssignments,
                    'tinh_trang' => $ts->tinh_trang,
                    'ma' => $ts->khoTaiSan ? ($ts->khoTaiSan->ma_tai_san ?? null) : null,
                    'suggested' => $suggested,
                ];
            });

            return response()->json([
                'slot' => ['id' => $slot->id, 'ma_slot' => $slot->ma_slot],
                'assets' => $response
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Slot không tồn tại'], 404);
        } catch (Exception $e) {
            Log::error('Lỗi lấy tài sản cho slot: '.$e->getMessage(), ['slot' => $slotId]);
            return response()->json(['message' => 'Không thể lấy tài sản'], 500);
        }
    }

    /**
     * Bàn giao tài sản (set số lượng) cho slot từ tài sản cấp phòng
     */
    public function assignAssets(Request $request, $slotId)
    {
        DB::beginTransaction();
        try {
            $slot = Slot::with('phong')->findOrFail($slotId);
            $phong = $slot->phong;
            if (!$phong) {
                return response()->json(['message' => 'Slot không thuộc phòng hợp lệ'], 422);
            }

            $data = $request->validate([
                'assets' => ['required','array'],
                'assets.*' => ['nullable','integer','min:0']
            ]);

            $assetsInput = $data['assets'];
            foreach ($assetsInput as $taiSanId => $qtyRaw) {
                $qty = (int) $qtyRaw;

                $taiSan = \App\Models\TaiSan::where('phong_id', $phong->id)->findOrFail($taiSanId);

                // Số đã gán cho tất cả slot & cho slot hiện tại
                $assignedAllOtherSlots = (int) $taiSan->slots()->where('slot_id', '<>', $slot->id)->sum('slot_tai_san.so_luong');
                $maxAssignable = max(0, (int)$taiSan->so_luong - $assignedAllOtherSlots);
                if ($qty > $maxAssignable) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Số lượng vượt quá tồn có thể bàn giao',
                        'tai_san_id' => $taiSanId,
                        'max' => $maxAssignable
                    ], 422);
                }

                // Upsert pivot: set số lượng cho slot này
                if ($qty <= 0) {
                    // Bỏ gán tài sản này khỏi slot
                    $slot->taiSans()->detach($taiSan->id);
                } else {
                    $slot->taiSans()->syncWithoutDetaching([$taiSan->id => ['so_luong' => $qty]]);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Bàn giao tài sản cho slot thành công']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Tài sản hoặc slot không tồn tại'], 404);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Lỗi bàn giao tài sản cho slot: '.$e->getMessage(), ['slot' => $slotId]);
            return response()->json(['message' => 'Không thể bàn giao tài sản'], 500);
        }
    }

    /**
     * Xóa toàn bộ tài sản đã gán cho slot
     */
    public function clearAssets($slotId)
    {
        try {
            $slot = Slot::findOrFail($slotId);
            $slot->taiSans()->detach();
            return response()->json(['message' => 'Đã bỏ gán toàn bộ CSVC cho slot']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Slot không tồn tại'], 404);
        } catch (Exception $e) {
            Log::error('Lỗi clear assets: '.$e->getMessage(), ['slot' => $slotId]);
            return response()->json(['message' => 'Không thể bỏ gán CSVC'], 500);
        }
    }

    /**
     * Bàn giao bộ CSVC mặc định (ví dụ: Gối, Màn, Chiếu …) cho slot nếu còn tồn ở phòng
     */
    private function assignDefaultKitToSlot(\App\Models\Slot $slot): void
    {
        // Danh sách loại/tên mặc định (có thể mở rộng): mỗi thứ 1 cái
        $defaultKeywords = ['gối', 'màn', 'chiếu', 'chăn'];

        // Toàn bộ tài sản thuộc phòng của slot
        $assets = \App\Models\TaiSan::with(['khoTaiSan.loai', 'slots'])
            ->where('phong_id', $slot->phong_id)
            ->get();

        foreach ($assets as $taiSan) {
            $loai = optional(optional($taiSan->khoTaiSan)->loai)->ten_loai;
            $name = mb_strtolower(trim($loai ?: $taiSan->ten_tai_san));

            // Chỉ áp dụng cho các loại nằm trong danh sách mặc định
            $isDefault = false;
            foreach ($defaultKeywords as $kw) {
                if (mb_strpos($name, $kw) !== false) { $isDefault = true; break; }
            }
            if (!$isDefault) { continue; }

            // Tính số lượng còn có thể gán (trừ các slot khác, cộng lại phần đã gán cho slot này)
            $assignedOther = (int) $taiSan->slots()
                ->where('slot_id', '<>', $slot->id)
                ->sum('slot_tai_san.so_luong');
            $currentThis = (int) $taiSan->slots()
                ->where('slot_id', $slot->id)
                ->sum('slot_tai_san.so_luong');

            $maxAssignable = max(0, (int)$taiSan->so_luong - $assignedOther);
            if ($maxAssignable <= 0) { continue; }

            // Mặc định phát 1 đơn vị/món; nếu đã có thì giữ nguyên, nếu chưa có thì gán 1
            $targetQty = $currentThis > 0 ? $currentThis : 1;
            $targetQty = min($targetQty, $maxAssignable);

            $slot->taiSans()->syncWithoutDetaching([$taiSan->id => ['so_luong' => $targetQty]]);
        }
    }

    /**
     * Xóa slot (chỉ cho phép xóa slot trống)
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $slot = Slot::findOrFail($id);

            // Kiểm tra slot có sinh viên không
            if ($slot->sinh_vien_id) {
                throw SlotException::khongTheXoaSlotCoSinhVien($slot->ma_slot);
            }

            // Xóa hình ảnh nếu có
            if ($slot->hinh_anh && Storage::disk('public')->exists($slot->hinh_anh)) {
                Storage::disk('public')->delete($slot->hinh_anh);
            }

            $maSlot = $slot->ma_slot;
            $slot->delete();

            DB::commit();

            Log::info('Xóa slot thành công', ['ma_slot' => $maSlot]);

            // Sau khi xóa slot, cập nhật lại loại phòng dựa trên tổng slot hiện có
            try {
                $phong = \App\Models\Phong::find($slot->phong_id);
                if ($phong && method_exists($phong, 'updateLoaiPhongFromSlots')) {
                    $phong->updateLoaiPhongFromSlots();
                }
            } catch (\Throwable $e) {
                // ignore non-critical
            }

            return response()->json([
                'message' => 'Xóa slot thành công'
            ]);

        } catch (SlotException $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode() ?: 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Slot không tồn tại trong hệ thống'
            ], 404);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi xóa slot: ' . $e->getMessage(), [
                'slot_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Có lỗi xảy ra khi xóa slot'
            ], 500);
        }
    }
}
