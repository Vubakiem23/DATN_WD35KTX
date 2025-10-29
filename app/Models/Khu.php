<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Khu extends Model
{
    protected $table = 'khu';

    protected $fillable = [
        'ten_khu',
        'gioi_tinh',
        'mo_ta',
    ];

    public function phongs(): HasMany
    {
        return $this->hasMany(Phong::class, 'khu_id');
    }
}



