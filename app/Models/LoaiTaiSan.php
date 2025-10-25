<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoaiTaiSan extends Model
{
    protected $table = 'loai_tai_san';
    protected $fillable = ['ma_loai', 'ten_loai', 'mo_ta', 'hinh_anh'];

    public function taiSan()
    {
        return $this->hasMany(TaiSan::class, 'loai_tai_san_id');
    }
    public function khoTaiSan()
    {
        return $this->hasMany(KhoTaiSan::class, 'loai_id');
    }
}
