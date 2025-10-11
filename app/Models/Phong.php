<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Phong extends Model
{
    protected $table = 'phong';

    protected $fillable = [
        'ten_phong',
        'khu',
        'loai_phong',
        'gioi_tinh',   // nullable (added via migration)
        'suc_chua',
        'trang_thai',
        'ghi_chu',      // nullable
    ];

    /**
     * Quan hệ: 1 phòng có nhiều sinh viên
     */
    public function sinhviens(): HasMany
    {
        return $this->hasMany(\App\Models\SinhVien::class, 'phong_id', 'id');
    }

    /**
     * Trả về số sinh viên "hiện đang ở" tính theo cột trang_thai_ho_so = 'Đã duyệt'.
     * Lý do: dựa trên cấu trúc SQL của dự án, cột trạng thái của sinh viên là trang_thai_ho_so.
     */
    public function soLuongHienTai(): int
    {
        return $this->sinhviens()->where('trang_thai_ho_so', 'Đã duyệt')->count();
    }

    /**
     * Cập nhật trạng thái phòng theo sức chứa: Trống / Đã ở / Bảo trì.
     * Nếu phòng đang Bảo trì giữ nguyên.
     */
    public function updateStatusBasedOnCapacity(): void
    {
        if ($this->trang_thai === 'Bảo trì') {
            return;
        }

        $so = $this->soLuongHienTai();
        $sucChua = (int)$this->suc_chua;

        if ($so >= $sucChua && $sucChua > 0) {
            $this->trang_thai = 'Đã ở';
        } elseif ($so === 0) {
            $this->trang_thai = 'Trống';
        } else {
            $this->trang_thai = 'Trống';
        }

        $this->save();
    }
}
