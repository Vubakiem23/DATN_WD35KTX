<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TinTuc extends Model
{
    protected $table = 'tin_tuc';
    protected $fillable = ['tieu_de', 'noi_dung', 'slug', 'ngay_tao'];

    public function hashtags()
    {
        return $this->belongsToMany(Hashtag::class, 'tin_tuc_hashtag');
    }
}
