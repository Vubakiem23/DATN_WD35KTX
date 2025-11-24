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
        'completion_percent',
        'payment_amount',
        'chi_phi_thuc_te',
        'is_paid',
        'anh',          // ảnh trước sửa (ảnh cũ)
        'anh_sau',      // ảnh sau khi xử lý (ảnh mới)
        'nguoi_tao',
        'ngay_hoan_thanh',
        'ngay_thanh_toan',
        'rating',
        'feedback',
        'rated_at',
    ];

    protected $casts = [
        'payment_amount' => 'decimal:2',
        'chi_phi_thuc_te' => 'decimal:2',
        'is_paid' => 'boolean',
        'ngay_gui' => 'datetime',
        'ngay_hoan_thanh' => 'datetime',
        'ngay_thanh_toan' => 'datetime',
        'rated_at' => 'datetime',
    ];

    // QUAN HỆ
    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class, 'sinh_vien_id');
    }

    public function phong()
    {
        return $this->belongsTo(Phong::class, 'phong_id');
    }

    public function thong_bao()
    {
        return $this->hasOne(\App\Models\ThongBaoSuCo::class, 'su_co_id');
    }

    // ========== ACCESSOR HIỂN THỊ ẢNH ==========

    // Ảnh cũ (ảnh ban đầu sinh viên gửi)
    public function getDisplayAnhAttribute()
    {
        return $this->anh
            ? asset($this->anh)
            : 'https://dummyimage.com/150x150/eff3f9/9aa8b8&text=NO+IMG';
    }

    // Ảnh mới (sau khi hoàn thành)
    public function getDisplayAnhMoiAttribute()
    {
        return $this->anh_sau
            ? asset($this->anh_sau)
            : null; // chưa có ảnh mới
    }

    // Giữ lại accessor cũ cho ai dùng
    public function getAnhUrlAttribute()
    {
        return $this->anh
            ? asset($this->anh)
            : asset('images/no-image.png');
    }

    public function getAnhSauUrlAttribute()
    {
        return $this->anh_sau ? asset($this->anh_sau) : null;
    }
}
