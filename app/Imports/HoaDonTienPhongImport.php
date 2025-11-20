<?php

namespace App\Imports;

use App\Models\HoaDon;
use App\Models\Phong;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class HoaDonTienPhongImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    /**
     * Tạo một bản ghi hóa đơn tiền phòng từ dòng Excel.
     */
    public function model(array $row)
    {
        if (empty($row['phong_id']) || !is_numeric($row['phong_id'])) {
            Log::warning('[IMPORT_TIEN_PHONG] Thiếu phong_id hoặc không hợp lệ: ' . json_encode($row));
            return null;
        }

        if (empty($row['thang'])) {
            Log::warning('[IMPORT_TIEN_PHONG] Thiếu cột thang: ' . json_encode($row));
            return null;
        }

        $phong = Phong::with(['khu', 'slots'])->find($row['phong_id']);
        if (!$phong) {
            Log::warning('[IMPORT_TIEN_PHONG] Phòng không tồn tại: phong_id = ' . $row['phong_id']);
            return null;
        }

        $slotCount = $this->castToInt($row['so_slot_tinh_phi'] ?? null, $phong->billableSlotCount(true));
        if ($slotCount <= 0) {
            Log::warning('[IMPORT_TIEN_PHONG] Số slot tính phí không hợp lệ: ' . json_encode($row));
            return null;
        }

        $slotUnitPrice = $this->castToInt($row['don_gia_slot'] ?? null, $phong->giaSlot());
        if ($slotUnitPrice < 0) {
            Log::warning('[IMPORT_TIEN_PHONG] Đơn giá slot không hợp lệ: ' . json_encode($row));
            return null;
        }

        $tienPhong = $this->castToInt($row['tien_phong'] ?? null, $slotCount * $slotUnitPrice);
        if ($tienPhong < 0) {
            Log::warning('[IMPORT_TIEN_PHONG] Tiền phòng không hợp lệ: ' . json_encode($row));
            return null;
        }

        $trangThai = trim($row['trang_thai'] ?? '') ?: 'Chưa thanh toán';

        return new HoaDon([
            'phong_id' => $phong->id,
            'invoice_type' => HoaDon::LOAI_TIEN_PHONG,
            'slot_billing_count' => $slotCount,
            'slot_unit_price' => $slotUnitPrice,
            'tien_phong_slot' => $tienPhong,
            'thanh_tien' => $tienPhong,
            'thang' => $row['thang'],
            'trang_thai' => $trangThai,
            'ghi_chu_thanh_toan' => $row['ghi_chu'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'phong_id' => 'required|numeric|exists:phong,id',
            'thang' => 'required',
            'so_slot_tinh_phi' => 'nullable|numeric|min:0',
            'don_gia_slot' => 'nullable|numeric|min:0',
            'tien_phong' => 'nullable|numeric|min:0',
            'trang_thai' => 'nullable|string|max:255',
            'ghi_chu' => 'nullable|string|max:500',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            Log::warning(
                sprintf(
                    '[IMPORT_TIEN_PHONG] Lỗi dòng %d: %s',
                    $failure->row(),
                    implode(', ', $failure->errors())
                )
            );
        }
    }

    /**
     * Ép giá trị về số nguyên, fallback khi null/không hợp lệ.
     */
    protected function castToInt($value, int $fallback): int
    {
        if (is_numeric($value)) {
            return (int) $value;
        }

        return $fallback;
    }
}

