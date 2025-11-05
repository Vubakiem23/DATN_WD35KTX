<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KhoTaiSan extends Model
{
    use HasFactory;

    protected $table = 'kho_tai_san';

    protected $fillable = [
        'ma_tai_san',
        'loai_id',
        'ten_tai_san',
        'don_vi_tinh',
        'tinh_trang',
        'so_luong',
        'hinh_anh',
        'ghi_chu',
        'phong_id',
    ];

    // 🔹 Liên kết tới tài sản gán vào phòng
    public function taiSans()
    {
        return $this->hasMany(TaiSan::class, 'kho_tai_san_id');
    }

    // 🔹 Liên kết với bảng lịch bảo trì
    public function lichBaoTri()
    {
        return $this->hasMany(LichBaoTri::class, 'kho_tai_san_id');
    }

    // 🔹 Loại tài sản
    public function loai()
    {
        return $this->belongsTo(LoaiTaiSan::class, 'loai_id');
    }

    // 🏠 Liên kết đến bảng trung gian phong_tai_san
    public function phongTaiSan()
    {
        return $this->hasOne(\App\Models\PhongTaiSan::class, 'kho_tai_san_id');
    }

    // 🏢 Quan hệ lấy trực tiếp phòng chứa tài sản
    public function phong()
{
    return $this->belongsTo(\App\Models\Phong::class, 'phong_id');
}

}
