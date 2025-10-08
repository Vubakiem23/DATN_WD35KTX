<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DangKyKTX extends Model
{
    use HasFactory;

    public $table = 'dang_ky_k_t_x';
    public $fillable = ['sinh_vien_id', 'ngay_dang_ky', 'trang_thai'];

    // Các thuộc tính khác của model...
}
