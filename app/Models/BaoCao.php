<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaoCao extends Model
{
    use HasFactory;

    public $table = 'bao_cao';
    public $fillable = ['loai_bao_cao', 'noi_dung', 'ngay_tao'];
    // Các thuộc tính khác của model...
}
