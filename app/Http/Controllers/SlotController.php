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

            // Kiểm tra số lượng slot không vượt quá sức chứa
            $soSlotHienTai = $phongModel->totalSlots();
            if ($soSlotHienTai >= $phongModel->suc_chua) {
                throw SlotException::vuotQuaSucChua(
                    $phongModel->ten_phong, 
                    $phongModel->suc_chua, 
                    $soSlotHienTai
                );
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

                // Kiểm tra giới tính phù hợp
                if ($phongModel->gioi_tinh !== 'Cả hai' && 
                    $phongModel->gioi_tinh !== $sinhVien->gioi_tinh) {
                    throw SlotException::gioiTinhKhongPhuHop(
                        $sinhVien->ho_ten,
                        $sinhVien->gioi_tinh,
                        $phongModel->gioi_tinh
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

            // Cập nhật loại phòng theo tổng slots
            if (method_exists($phongModel, 'updateLoaiPhongFromSlots')) {
                $phongModel->updateLoaiPhongFromSlots();
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
                'cs_vat_chat'  => ['nullable', 'string'],
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
            
            if (array_key_exists('cs_vat_chat', $data)) {
                $slot->cs_vat_chat = $data['cs_vat_chat'];
            }

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
     * Cập nhật thông tin slot (mã, ghi chú, cs_vat_chat, ảnh)
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $slot = Slot::with('phong')->findOrFail($id);

            // Validate input
            $data = $request->validate([
                'ghi_chu'     => ['nullable', 'string', 'max:500'],
                'cs_vat_chat' => ['nullable', 'string'],
                'hinh_anh'    => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
            ]);

            // Cập nhật thông tin
            if (array_key_exists('ghi_chu', $data)) {
                $slot->ghi_chu = $data['ghi_chu'];
            }
            
            if (array_key_exists('cs_vat_chat', $data)) {
                $slot->cs_vat_chat = $data['cs_vat_chat'];
            }

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
