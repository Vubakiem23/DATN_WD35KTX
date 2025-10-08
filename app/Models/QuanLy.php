<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuanLy extends Model
{
    use HasFactory;

    public $table = 'quan_ly';
    public $fillable = [
        'ma_quan_ly',
        'ho_ten',
        'chuc_vu',
        'user_id',
    ];

    // Các thuộc tính khác của model...
}
