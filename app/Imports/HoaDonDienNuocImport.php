<?php

namespace App\Imports;

use App\Models\HoaDon;
use App\Models\Phong;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;

class HoaDonDienNuocImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    protected $failures = [];

    public function model(array $row)
    {
        // Kiểm tra phong_id có tồn tại và hợp lệ
        if (empty($row['phong_id']) || !is_numeric($row['phong_id'])) {
            Log::warning('Thiếu hoặc sai phong_id tại dòng: ' . json_encode($row));
            return null; // Bỏ qua dòng lỗi
        }

        // Kiểm tra phòng có tồn tại không
        $phong = Phong::with(['khu', 'slots'])->find($row['phong_id']);
        if (!$phong) {
            Log::warning('Phòng không tồn tại: phong_id = ' . $row['phong_id']);
            return null; // Bỏ qua dòng lỗi
        }

        // Chỉ tính các phòng có người ở (nhất quán logic tiền phòng)
        $billableSlots = $phong->billableSlotCount(true);
        if ($billableSlots <= 0) {
            Log::info('[IMPORT_DIEN_NUOC] Bỏ qua phòng trống phong_id = ' . $row['phong_id']);
            return null;
        }

        // Kiểm tra các trường bắt buộc
        $requiredFields = ['so_dien_cu', 'so_dien_moi', 'so_nuoc_cu', 'so_nuoc_moi', 'don_gia_dien', 'don_gia_nuoc', 'thang'];
        foreach ($requiredFields as $field) {
            if (!isset($row[$field]) || $row[$field] === '' || $row[$field] === null) {
                Log::warning("Thiếu trường bắt buộc: {$field} tại dòng: " . json_encode($row));
                return null;
            }
        }

        // Tính toán số điện và số nước
        $so_dien_cu = (int)($row['so_dien_cu'] ?? 0);
        $so_dien_moi = (int)($row['so_dien_moi'] ?? 0);
        $so_nuoc_cu = (int)($row['so_nuoc_cu'] ?? 0);
        $so_nuoc_moi = (int)($row['so_nuoc_moi'] ?? 0);
        
        $so_dien = $so_dien_moi - $so_dien_cu;
        $so_nuoc = $so_nuoc_moi - $so_nuoc_cu;

        // Kiểm tra số điện/nước mới phải lớn hơn số cũ
        if ($so_dien < 0) {
            Log::warning('Số điện mới nhỏ hơn số điện cũ tại dòng: ' . json_encode($row));
            return null;
        }
        if ($so_nuoc < 0) {
            Log::warning('Số nước mới nhỏ hơn số nước cũ tại dòng: ' . json_encode($row));
            return null;
        }

        // Đơn giá
        $don_gia_dien = (int)($row['don_gia_dien'] ?? 0);
        $don_gia_nuoc = (int)($row['don_gia_nuoc'] ?? 0);

        if ($don_gia_dien < 0 || $don_gia_nuoc < 0) {
            Log::warning('Đơn giá không hợp lệ tại dòng: ' . json_encode($row));
            return null;
        }

        // Tiền phòng theo slot/khu - chỉ tính slot có người ở (nhất quán với Controller)
        $tienPhongSlot = $billableSlots * $phong->giaSlot();

        // Thành tiền = Tiền điện + Tiền nước + Tiền phòng
        $tien_dien = $so_dien * $don_gia_dien;
        $tien_nuoc = $so_nuoc * $don_gia_nuoc;
        $thanh_tien = $tien_dien + $tien_nuoc + $tienPhongSlot;

        // Tạo hóa đơn
        return new HoaDon([
            'phong_id'      => $row['phong_id'],
            'invoice_type'  => HoaDon::LOAI_DIEN_NUOC,
            'so_dien_cu'    => $so_dien_cu,
            'so_dien_moi'   => $so_dien_moi,
            'so_nuoc_cu'    => $so_nuoc_cu,
            'so_nuoc_moi'   => $so_nuoc_moi,
            'don_gia_dien'  => $don_gia_dien,
            'don_gia_nuoc'  => $don_gia_nuoc,
            'thanh_tien'    => $thanh_tien,
            'thang'         => $row['thang'],
        ]);
    }

    public function rules(): array
    {
        return [
            'phong_id' => 'required|numeric|exists:phong,id',
            'so_dien_cu' => 'required|numeric|min:0',
            'so_dien_moi' => 'required|numeric|min:0',
            'so_nuoc_cu' => 'required|numeric|min:0',
            'so_nuoc_moi' => 'required|numeric|min:0',
            'don_gia_dien' => 'required|numeric|min:0',
            'don_gia_nuoc' => 'required|numeric|min:0',
            'thang' => 'required',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            Log::warning('Lỗi validation tại dòng ' . $failure->row() . ': ' . implode(', ', $failure->errors()));
        }
    }
}
