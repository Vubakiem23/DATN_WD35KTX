<?php

namespace App\Exceptions;

use Exception;

class SlotException extends Exception
{
    public static function maSlotTrung(string $maSlot, string $tenPhong): self
    {
        return new self("Mã slot \"{$maSlot}\" đã tồn tại trong phòng {$tenPhong}.", 422);
    }

    public static function vuotQuaSucChua(string $tenPhong, int $sucChua, int $hienCo): self
    {
        return new self("Không thể tạo thêm slot. Phòng {$tenPhong} có sức chứa {$sucChua}, hiện có {$hienCo} slot.", 422);
    }

    public static function sinhVienChuaDuocDuyet(string $hoTen, string $trangThai): self
    {
        return new self("Sinh viên {$hoTen} chưa được duyệt hồ sơ (Trạng thái: {$trangThai}).", 422);
    }

    public static function sinhVienDaCoSlot(string $hoTen, string $tenPhongKhac): self
    {
        return new self("Sinh viên {$hoTen} đã được gán vào phòng {$tenPhongKhac}.", 422);
    }

    public static function gioiTinhKhongPhuHop(string $hoTen, string $gioiTinhSV, string $gioiTinhPhong): self
    {
        // Thông điệp đúng theo yêu cầu của người dùng
        // Ví dụ: Phòng Nam, không thể gán Nữ vào
        $prefix = $gioiTinhPhong === 'Cả hai' ? '' : "Phòng {$gioiTinhPhong}, ";
        return new self($prefix . "không thể gán {$gioiTinhSV} vào.", 422);
    }

    public static function khongTheXoaSlotCoSinhVien(string $maSlot): self
    {
        return new self("Không thể xoá slot {$maSlot} vì đang có sinh viên.", 422);
    }
}


