<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThongBaoSinhVien extends Model
{
    use HasFactory;

    protected $table = 'thongbao_sinh_vien';

    protected $fillable = [
        'sinh_vien_id',
        'noi_dung',
        'trang_thai',
    ];

    public function sinhVien()
    {
        return $this->belongsTo(\App\Models\SinhVien::class, 'sinh_vien_id');
    }
}

