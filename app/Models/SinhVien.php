<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SinhVien extends Model
{
    use HasFactory;

    public $table = 'sinh_vien';
    public $fillable = ['ma_sv', 'ho_ten', 'ngay_sinh', 'gioi_tinh', 'lop', 'khoa', 'email', 'so_dien_thoai'];

    // Các thuộc tính khác của model...
}
