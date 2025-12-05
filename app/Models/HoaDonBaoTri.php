<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoaDonBaoTri extends Model
{
    use HasFactory;

    protected $table = 'hoa_don_bao_tri';

    protected $fillable = [
        'lich_bao_tri_id',
        'chi_phi',
        'trang_thai_thanh_toan',
        'phuong_thuc_thanh_toan',
        'ghi_chu',
    ];

    // Quan hệ với lịch bảo trì
    public function lichBaoTri()
    {
        return $this->belongsTo(LichBaoTri::class, 'lich_bao_tri_id');
    }
}
