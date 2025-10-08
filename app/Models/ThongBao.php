<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThongBao extends Model
{
    use HasFactory;

    public $table = 'thong_bao';
    public $fillable = ['tieu_de', 'noi_dung', 'ngay_tao'];

    // Các thuộc tính khác của model...
}
