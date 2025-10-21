<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThongBao extends Model
{
    use HasFactory;

    protected $table = 'thong_bao';

    protected $fillable = [
        'tieu_de',
        'noi_dung',
        'ngay_dang',
        'doi_tuong',
        'anh',
        'phong_id',
    ];

    /**
     * Quan hệ với phòng
     */
    public function phong()
    {
        return $this->belongsTo(Phong::class, 'phong_id');
    }

    /**
     * Accessor trả về thông tin phòng đầy đủ
     */
    public function getPhongInfoAttribute()
    {
        if ($this->phong) {
            return [
                'ten_phong' => $this->phong->ten_phong,
                'khu' => $this->phong->khu,
            ];
        }

        return null;
    }
}
