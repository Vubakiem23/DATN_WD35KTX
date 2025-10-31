<?php


namespace App\Models;




use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class HoaDon extends Model
{
    use HasFactory;

    public $table = 'hoa_don';
    public $timestamps = true;
    protected $fillable = [
    'phong_id',
    'so_dien_cu',
    'so_dien_moi',
    'so_nuoc_cu',
    'so_nuoc_moi',
    'don_gia_dien',
    'don_gia_nuoc',
    'thanh_tien',
    'thang',
    'trang_thai',
    'da_thanh_toan', 
    'ngay_thanh_toan', 
    'hinh_thuc_thanh_toan', 
    'ghi_chu_thanh_toan',
];


   public function phong()
{
    return $this->belongsTo(Phong::class, 'phong_id');
}
public function getHinhThucThanhToanLabelAttribute()
{
    return match($this->hinh_thuc_thanh_toan) {
        'tien_mat' => 'Tiền mặt',
        'chuyen_khoan' => 'Chuyển khoản',
        default => 'Không xác định',
    };
}


    // Các thuộc tính khác của model...
}
