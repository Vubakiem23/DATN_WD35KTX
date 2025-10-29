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
        'ngay_bao_tri',
        'ngay_hoan_thanh',
        'trang_thai',
        'mo_ta',
        'hinh_anh_truoc', 
        'hinh_anh',
    ];

    public function taiSan()
    {
        return $this->belongsTo(TaiSan::class);
    }

    public function khoTaiSan()
    {
        return $this->belongsTo(KhoTaiSan::class);
    }
}
