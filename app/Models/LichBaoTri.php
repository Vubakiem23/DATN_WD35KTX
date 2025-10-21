<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LichBaoTri extends Model
{
    use HasFactory;

    protected $table = 'lich_bao_tri'; // tên bảng tiếng Việt

    protected $fillable = [
        'tai_san_id',
        'ngay_bao_tri',
        'mo_ta',
        'trang_thai',
        'ngay_hoan_thanh',
        'hinh_anh',
    ];

    public function taiSan()
    {
        return $this->belongsTo(TaiSan::class, 'tai_san_id');
    }
    
}
