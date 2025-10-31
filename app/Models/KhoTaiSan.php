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
    ];

    // Nếu muốn liên kết với tài sản từng phòng:
    public function taiSans()
    {
        return $this->hasMany(TaiSan::class, 'kho_tai_san_id');
    }
    public function lichBaoTri()
    {
        return $this->hasMany(LichBaoTri::class, 'kho_tai_san_id');
    }
    public function loai()
    {
        return $this->belongsTo(LoaiTaiSan::class, 'loai_id');
    }
}
