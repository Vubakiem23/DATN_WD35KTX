<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TieuDe extends Model
{
    use HasFactory;

    protected $table = 'tieu_de';

    protected $fillable = [
        'ten_tieu_de',
    ];

    /**
     * Một tiêu đề có thể có nhiều thông báo
     */
    public function thongBaos()
    {
        return $this->hasMany(ThongBao::class, 'tieu_de_id');
    }
}
