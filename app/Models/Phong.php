<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Phong extends Model
{
    protected $table = 'phong';
    protected $fillable = ['ten_phong', 'khu', 'loai_phong', 'gioi_tinh', 'suc_chua', 'trang_thai', 'ghi_chu', 'hinh_anh'];

    public function sinhVien()
    {
        return $this->hasMany(SinhVien::class, 'phong_id');
    }

    public function taiSan()
    {
        return $this->hasMany(TaiSan::class, 'phong_id');
    }

    // số slot đã dùng (đếm theo slots có sinh_vien_id)
    public function usedSlots()
    {
        return $this->slots()->whereNotNull('sinh_vien_id')->count();
    }
    public function slots()
    {
        return $this->hasMany(\App\Models\Slot::class, 'phong_id');
    }

    // alias cho code cũ nếu có gọi tiếng việt
    public function soLuongHienTai()
    {
        return $this->usedSlots();
    }

    public function totalSlots()
    {
        return $this->slots()->count();
    }

    public function availableSlots()
    {
        $total = $this->totalSlots();
        return max(0, $total - $this->usedSlots());
    }

    public function occupancyLabel(): string
    {
        $total = $this->totalSlots();
        $used = $this->usedSlots();
        $available = max(0, $total - $used);
        if ($total === 0) {
            return 'Chưa có slot';
        }
        if ($available === 0) {
            return 'Đã ở full';
        }
        return 'Trống ' . $available;
    }

    public static function labelLoaiPhongBySlots(int $totalSlots): string
    {
        if ($totalSlots <= 0) return '';
        if ($totalSlots === 1) return 'Đơn';
        if ($totalSlots === 2) return 'Đôi';
        // Từ 3 trở lên đặt theo số giường
        return 'Phòng ' . $totalSlots;
    }

    public function updateLoaiPhongFromSlots(): void
    {
        $total = $this->totalSlots();
        $this->loai_phong = self::labelLoaiPhongBySlots($total);
        $this->save();
    }
}
