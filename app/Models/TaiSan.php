<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaiSan extends Model
{
    use HasFactory;

    public $table = 'tai_san';
    public $fillable = ['ten_tai_san', 'so_luong', 'tinh_trang', 'ngay_tao'];

    // Các thuộc tính khác của model...
}
