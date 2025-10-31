<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MucDo extends Model
{
    use HasFactory;

    protected $table = 'muc_do';

    protected $fillable = [
        'ten_muc_do',
        'mau_hien_thi', // (tùy chọn) nếu bạn muốn phân biệt bằng màu sắc (VD: đỏ = khẩn)
    ];

    /**
     * Một mức độ có thể có nhiều thông báo
     */
    public function thongBaos()
    {
        return $this->hasMany(ThongBao::class, 'muc_do_id');
    }
}
