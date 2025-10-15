<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoaDon extends Model
{
    use HasFactory;

    public $table = 'hoa_don';
    public $fillable = ['sinh_vien_id', 'loai_phi', 'so_tien', 'ngay_tao', 'trang_thai'];

    public function sinhVien()
{
    return $this->belongsTo(SinhVien::class, 'sinh_vien_id');
}

    // Các thuộc tính khác của model...
}
