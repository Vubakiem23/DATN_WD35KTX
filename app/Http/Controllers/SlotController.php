<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slot;
use App\Models\Phong;
use App\Models\SinhVien;
use Illuminate\Validation\Rule;

class SlotController extends Controller
{
    // Tạo slot mới cho phòng
     public function store(Request $request, $phong)
    {
        // ensure phong exists
        $phongModel = Phong::findOrFail($phong);

        $data = $request->validate([
            'ma_slot'      => ['required','string','max:255'],
            'ghi_chu'      => ['nullable','string'],
            // optional: sinh_vien_id if you want to create assigned directly
            'sinh_vien_id' => ['nullable','integer', Rule::exists('sinh_vien','id')],
            'cs_vat_chat'  => ['nullable','string'],
            'hinh_anh'     => ['nullable','image','max:5120'],
        ]);

        // prevent duplicate ma_slot for same phong
        $exists = Slot::where('phong_id', $phongModel->id)
                      ->where('ma_slot', $data['ma_slot'])
                      ->exists();
        if ($exists) {
            return response()->json(['message' => 'Mã slot đã tồn tại trong phòng này'], 422);
        }

        $imagePath = null;
        if ($request->hasFile('hinh_anh')) {
            $imagePath = $request->file('hinh_anh')->store('slots', 'public');
        }

        $slot = Slot::create([
            'phong_id'     => $phongModel->id,
            'ma_slot'      => $data['ma_slot'],
            'ghi_chu'      => $data['ghi_chu'] ?? null,
            'sinh_vien_id' => $data['sinh_vien_id'] ?? null,
            'cs_vat_chat'  => $data['cs_vat_chat'] ?? null,
            'hinh_anh'     => $imagePath,
        ]);

        // Sau khi tạo, cập nhật loại phòng theo tổng slots
        if (method_exists($phongModel, 'updateLoaiPhongFromSlots')) {
            $phongModel->updateLoaiPhongFromSlots();
        }

        // trả về JSON để client xử lý (status 201)
        return response()->json([
            'message' => 'Tạo slot thành công',
            'slot'    => $slot,
        ], 201);
    }
    // Gán sinh viên vào slot (hoặc bỏ gán nếu null)
    public function assignStudent(Request $request, $id)
    {
        $slot = Slot::findOrFail($id);

        $data = $request->validate([
            'sinh_vien_id' => ['nullable','integer', Rule::exists('sinh_vien','id')],
            'cs_vat_chat'  => ['nullable','string'],
            'hinh_anh'     => ['nullable','image','max:5120'],
        ]);

        // Nếu gán sinh viên mới: kiểm tra sinh viên có đang ở slot khác không (tuỳ yêu cầu)
        if (!empty($data['sinh_vien_id'])) {
            // optional: prevent student being in multiple slots
            $other = Slot::where('sinh_vien_id', $data['sinh_vien_id'])->where('id','<>',$slot->id)->first();
            if ($other) {
                return response()->json(['message'=>'Sinh viên này đã được gán vào slot khác'], 422);
            }
        }

        $slot->sinh_vien_id = $data['sinh_vien_id'] ?? null;
        if(array_key_exists('cs_vat_chat', $data)){
            $slot->cs_vat_chat = $data['cs_vat_chat'];
        }
        if ($request->hasFile('hinh_anh')) {
            $path = $request->file('hinh_anh')->store('slots', 'public');
            $slot->hinh_anh = $path;
        }
        $slot->save();

        // trả về slot kèm thông tin sinh viên
        $slot->load('sinhVien');
        return response()->json(['slot'=>$slot]);
    }

    // Cập nhật thông tin slot (mã, ghi chú, cs_vat_chat, ảnh)
    public function update(Request $request, $id)
    {
        $slot = Slot::findOrFail($id);

        $data = $request->validate([
            // Không cho phép sửa mã slot để đảm bảo tính ổn định tham chiếu
            'ma_slot'     => ['sometimes','string','max:255'],
            'ghi_chu'     => ['nullable','string'],
            'cs_vat_chat' => ['nullable','string'],
            'hinh_anh'    => ['nullable','image','max:5120'],
        ]);
        // Không cập nhật ma_slot (giữ nguyên)
        $slot->ghi_chu = $data['ghi_chu'] ?? null;
        if (array_key_exists('cs_vat_chat', $data)) {
            $slot->cs_vat_chat = $data['cs_vat_chat'];
        }
        if ($request->hasFile('hinh_anh')) {
            $path = $request->file('hinh_anh')->store('slots', 'public');
            $slot->hinh_anh = $path;
        }
        $slot->save();

        return response()->json(['message' => 'Cập nhật slot thành công', 'slot'=>$slot]);
    }
    // Lấy slots theo phòng (dùng AJAX trên view)
    public function slotsByPhong($phongId)
    {
        $phong = Phong::with(['slots.sinhVien'])->findOrFail($phongId);
        return response()->json(['phong'=>$phong]);
    }
}
