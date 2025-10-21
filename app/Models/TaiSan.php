<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaiSan extends Model
{
    use HasFactory;

    protected $table = 'tai_san';

    protected $fillable = [
        'kho_tai_san_id',
        'ten_tai_san',
        'so_luong',
        'tinh_trang',
        'tinh_trang_hien_tai',
        'phong_id',
        'hinh_anh',
    ];

    public function khoTaiSan()
    {
        return $this->belongsTo(KhoTaiSan::class, 'kho_tai_san_id');
    }

    public function phong()
    {
        return $this->belongsTo(Phong::class, 'phong_id');
    }
}
