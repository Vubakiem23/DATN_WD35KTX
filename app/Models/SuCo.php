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
        'trang_thai',      // trạng thái tiếp nhận: pending/in_progress/resolved
        'payment_amount',   // số tiền
        'is_paid',          // thanh toán: true/false
        'anh',              // ảnh minh chứng
        'nguoi_tao',        // sinh_vien hoặc nhan_vien
        'ngay_hoan_thanh',  // 🆕 ngày hoàn thành sự cố
        'ngay_thanh_toan',  // 🆕 ngày thanh toán hóa đơn
    ];

    protected $casts = [
        'payment_amount' => 'decimal:2',
        'is_paid' => 'boolean',
        'ngay_hoan_thanh' => 'datetime', // 🆕 cast ngày hoàn thành
        'ngay_thanh_toan' => 'datetime', // 🆕 cast ngày thanh toán
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
