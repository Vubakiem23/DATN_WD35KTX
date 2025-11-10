<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThongBaoSuCo extends Model
{
    use HasFactory;

    protected $table = 'thong_bao_su_co';

    protected $fillable = [
        'su_co_id',
        'tieu_de',
        'noi_dung',
        'ngay_tao',
    ];

    public $timestamps = false;

    // Quan hệ với su_co
    public function su_co()
    {
        return $this->belongsTo(\App\Models\SuCo::class, 'su_co_id');
    }

    // Quan hệ tới sinh viên thông qua su_co
    public function sinh_vien()
    {
        return $this->hasOneThrough(
            \App\Models\SinhVien::class, // Model đích
            \App\Models\SuCo::class,     // Model trung gian
            'id',                        // Khóa ngoại của su_co trên thong_bao_su_co (su_co_id)
            'id',                        // Khóa chính của sinh_vien
            'su_co_id',                  // Khóa ngoại của thong_bao_su_co tới su_co
            'sinh_vien_id'               // Khóa ngoại của su_co tới sinh_vien
        );
    }
    
}

