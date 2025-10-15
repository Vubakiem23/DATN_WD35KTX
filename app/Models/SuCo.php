<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuCo extends Model
{
    use HasFactory;

    protected $table = 'su_co';

    protected $fillable = [
        'sinh_vien_id',
        'phong_id',
        'mo_ta',
        'ngay_gui',
        'trang_thai',
    ];

    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class, 'sinh_vien_id');
    }

    public function phong()
    {
        return $this->belongsTo(Phong::class, 'phong_id');
    }
}
