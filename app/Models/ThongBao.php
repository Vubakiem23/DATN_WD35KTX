<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThongBao extends Model
{
    use HasFactory;

    protected $table = 'thong_bao';

    protected $fillable = [
    'tieu_de_id', // khóa ngoại tới bảng tiêu đề
    'noi_dung',
    'ngay_dang',
    'doi_tuong',
    'anh',
    'file',       // đường dẫn file PDF, Word, Excel
    'muc_do_id',  // khóa ngoại tới bảng mức độ
    'phong_id',   // nếu có liên kết tới phòng
    'user_id',    // khóa ngoại tới bảng users (người viết thông báo)
];
    /**
     * Quan hệ với bảng tiêu đề
     */
    public function tieuDe()
    {
        return $this->belongsTo(TieuDe::class, 'tieu_de_id');
    }

    /**
     * Quan hệ với bảng mức độ
     */
    public function mucDo()
    {
        return $this->belongsTo(MucDo::class, 'muc_do_id');
    }

    /**
     * Quan hệ nhiều-nhiều với Khu
     */
    public function khus()
    {
        return $this->belongsToMany(Khu::class, 'thong_bao_khu', 'thong_bao_id', 'khu_id');
    }

    /**
     * Quan hệ nhiều-nhiều với Phòng
     */
    public function phongs()
    {
        return $this->belongsToMany(Phong::class, 'thong_bao_phong', 'thong_bao_id', 'phong_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Accessor trả về thông tin phòng đầy đủ
     * Nếu muốn lấy tất cả phòng liên kết nhiều-nhiều, có thể sửa như sau:
     */
    public function getPhongInfoAttribute()
    {
        if ($this->phongs && $this->phongs->count()) {
            return $this->phongs->map(function ($phong) {
                return [
                    'ten_phong' => $phong->ten_phong,
                    'khu' => $phong->khu,
                ];
            });
        }

        return null;
    }
}
