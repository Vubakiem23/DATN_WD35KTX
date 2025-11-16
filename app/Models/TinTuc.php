<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TinTuc extends Model
{
    protected $table = 'tin_tuc';

    // Thêm 'hinh_anh' vào fillable để mass assignment
    protected $fillable = [
        'tieu_de',
        'noi_dung',
        'slug',
        'ngay_tao',
        'hinh_anh',
    ];

    // Cast ngày tháng
    protected $casts = [
        'ngay_tao' => 'date',
    ];

    // Quan hệ với Hashtag (many-to-many)
    public function hashtags()
    {
        return $this->belongsToMany(Hashtag::class, 'tin_tuc_hashtag');
    }
}
