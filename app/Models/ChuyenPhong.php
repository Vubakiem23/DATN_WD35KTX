<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChuyenPhong extends Model
{
    use HasFactory;

    public $table = 'chuyen_phong';
    public $fillable = ['phong_cu_id', 'phong_moi_id', 'sinh_vien_id', 'ngay_chuyen'];


    // Các thuộc tính khác của model...
}
