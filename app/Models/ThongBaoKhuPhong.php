<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThongBaoKhuPhong extends Model
{
    use HasFactory;

    protected $table = 'thong_bao_khu_phong';

    protected $fillable = [
        'tieu_de',
        'noi_dung',
        'loai',
        'doi_tuong_id',
    ];
}
