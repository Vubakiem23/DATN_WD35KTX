<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoaDonSuCo extends Model
{
    use HasFactory;

    protected $table = 'hoa_don_su_co';

    protected $fillable = [
        'su_co_id',
        'sinh_vien_id',
        'phong_id',
        'amount',
        'status',
        'ngay_tao',
        'ngay_thanh_toan',
    ];

    // 🔗 Quan hệ với sự cố
    public function suco()
    {
        return $this->belongsTo(SuCo::class, 'su_co_id');
    }

    // 🔗 Quan hệ với sinh viên
    public function sinhvien()
    {
        return $this->belongsTo(SinhVien::class, 'sinh_vien_id');
    }

    // 🔗 Quan hệ với phòng
    public function phong()
    {
        return $this->belongsTo(Phong::class, 'phong_id');
    }
}
