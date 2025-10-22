<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class HoaDon extends Model
{
    use HasFactory;

    public $table = 'hoa_don';
    public $timestamps = false; 
    protected $fillable = [
    'phong_id',
    'so_dien_cu',
    'so_dien_moi',
    'so_nuoc_cu',
    'so_nuoc_moi',
    'don_gia_dien',
    'don_gia_nuoc',
    'thanh_tien',
    'thang',
    
];


    public function sinhVien()
{
    return $this->belongsTo(SinhVien::class, 'sinh_vien_id');
}
    public function phong()
    {
        return $this->belongsTo(Phong::class);
    }

    // Các thuộc tính khác của model...
}
