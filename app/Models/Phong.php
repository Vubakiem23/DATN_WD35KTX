<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phong extends Model
{
    use HasFactory;

    public $table = 'phong';
    public $fillable = ['ten_phong', 'khu', 'loai_phong', 'suc_chua', 'trang_thai'];

    // Các thuộc tính khác của model...S
}
