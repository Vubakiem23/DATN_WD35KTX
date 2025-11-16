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
        'completion_percent', // phần trăm hoàn thiện 0-100
        'payment_amount',   // số tiền
        'is_paid',          // thanh toán: true/false
        'anh',              // ảnh minh chứng
        'anh_sau',          // ảnh sau khi xử lý
        'nguoi_tao',        // sinh_vien hoặc nhan_vien
        'ngay_hoan_thanh',  // 🆕 ngày hoàn thành sự cố
        'ngay_thanh_toan',  // 🆕 ngày thanh toán hóa đơn
        'rating',           // đánh giá 1-5
        'feedback',         // góp ý
        'rated_at',         // thời gian đánh giá
    ];

    protected $casts = [
        'payment_amount' => 'decimal:2',
        'is_paid' => 'boolean',
        'ngay_gui' => 'datetime',
        'ngay_hoan_thanh' => 'datetime', // 🆕 cast ngày hoàn thành
        'ngay_thanh_toan' => 'datetime', // 🆕 cast ngày thanh toán
        'rated_at' => 'datetime',
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
    // 🖼️ Ảnh sau xử lý
    public function getAnhSauUrlAttribute()
    {
        return $this->anh_sau ? asset($this->anh_sau) : null;
    }
    public function thong_bao()
    {
        return $this->hasOne(\App\Models\ThongBaoSuCo::class, 'su_co_id');
    }

    public function sinh_vien()
    {
        return $this->belongsTo(\App\Models\SinhVien::class, 'sinh_vien_id');
    }
    
    public function getDisplayAnhAttribute()
{
    return $this->anh_sau 
        ? asset($this->anh_sau) 
        : ($this->anh ? asset($this->anh) : 'https://dummyimage.com/150x150/eff3f9/9aa8b8&text=IMG');
}

}
