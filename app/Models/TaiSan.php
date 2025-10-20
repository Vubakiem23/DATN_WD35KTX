<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaiSan extends Model
{
    use HasFactory;

    protected $table = 'tai_san';

    protected $fillable = [
        'ten_tai_san',
        'so_luong',
        'tinh_trang',
        'tinh_trang_hien_tai',
        'ngay_tao',
        'phong_id',
        'hinh_anh',
    ];

    // ✅ Thêm quan hệ đến bảng phòng
    public function phong()
    {
        return $this->belongsTo(Phong::class, 'phong_id');
    }
}
