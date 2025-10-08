<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuCo extends Model
{
    use HasFactory;

    public $table = 'su_co';
    public $fillable = ['loai_su_co', 'noi_dung', 'ngay_tao'];

    // Các thuộc tính khác của model...
}
