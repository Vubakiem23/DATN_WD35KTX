<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuCo extends Model
{
    use HasFactory;

    protected $table = 'su_co';

    protected $fillable = [
        'sinh_vien_id',
        'phong_id',
        'mo_ta',
        'ngay_gui',
        'trang_thai',
        'anh', // ✅ thêm dòng này để lưu ảnh minh chứng
    ];

    // 🧩 Quan hệ: Một sự cố thuộc về một sinh viên
    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class, 'sinh_vien_id');
    }

    // 🧩 Quan hệ: Một sự cố thuộc về một phòng
    public function phong()
    {
        return $this->belongsTo(Phong::class, 'phong_id');
    }

    // 🖼️ Lấy đường dẫn ảnh đầy đủ
    public function getAnhUrlAttribute()
    {
        return $this->anh ? asset($this->anh) : asset('images/no-image.png');
    }
}
