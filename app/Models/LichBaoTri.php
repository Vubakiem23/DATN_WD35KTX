<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LichBaoTri extends Model
{
    use HasFactory;

    protected $table = 'lich_bao_tri';

    protected $fillable = [
        'tai_san_id',
        'kho_tai_san_id',
        'location_type',
        'location_id',
        'ngay_bao_tri',
        'ngay_hoan_thanh',
        'chi_phi',
        'trang_thai',
        'nguoi_tao',
        'mo_ta',
        'hinh_anh_truoc', 
        'hinh_anh',
    ];

    protected $casts = [
        'chi_phi' => 'decimal:2',
        'ngay_bao_tri' => 'date',
        'ngay_hoan_thanh' => 'date',
    ];

    public function taiSan()
    {
        return $this->belongsTo(TaiSan::class);
    }

    public function khoTaiSan()
    {
        return $this->belongsTo(KhoTaiSan::class);
    }
    public function hoaDonBaoTri()
{
    return $this->hasOne(HoaDonBaoTri::class, 'lich_bao_tri_id');
}

}
