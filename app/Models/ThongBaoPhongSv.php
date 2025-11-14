<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThongBaoPhongSv extends Model
{
    protected $table = 'thong_bao_phong_sv';

    protected $fillable = [
        'sinh_vien_id',
        'phong_id',
        'noi_dung',
    ];

    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class);
    }

    public function phong()
    {
        return $this->belongsTo(Phong::class);
    }
}


