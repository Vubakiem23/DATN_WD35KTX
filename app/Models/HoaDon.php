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
    'tien_phong_slot',
    'slot_unit_price',
    'slot_billing_count',
    'thang',
    'trang_thai',
    'da_thanh_toan', 
    'ngay_thanh_toan', 
    'hinh_thuc_thanh_toan', 
    'ghi_chu_thanh_toan',
    'sent_to_client',
    'sent_to_client_at',
    'sent_dien_nuoc_to_client',
    'sent_dien_nuoc_at',
    'da_thanh_toan_dien_nuoc',
    'ngay_thanh_toan_dien_nuoc',
    'hinh_thuc_thanh_toan_dien_nuoc',
    'ghi_chu_thanh_toan_dien_nuoc',
];

protected $casts = [
    'da_thanh_toan' => 'boolean',
    'da_thanh_toan_dien_nuoc' => 'boolean',
    'sent_to_client' => 'boolean',
    'sent_dien_nuoc_to_client' => 'boolean',
    'slot_billing_count' => 'integer',
    'slot_unit_price' => 'integer',
    'tien_phong_slot' => 'integer',
    'ngay_thanh_toan' => 'datetime',
    'ngay_thanh_toan_dien_nuoc' => 'datetime',
    'sent_to_client_at' => 'datetime',
    'sent_dien_nuoc_at' => 'datetime',
];


   public function phong()
{
    return $this->belongsTo(Phong::class, 'phong_id');
}

public function slotPayments()
{
    return $this->hasMany(HoaDonSlotPayment::class, 'hoa_don_id');
}

public function getHinhThucThanhToanLabelAttribute()
{
    return match($this->hinh_thuc_thanh_toan) {
        'tien_mat' => 'Tiền mặt',
        'chuyen_khoan' => 'Chuyển khoản',
        default => 'Không xác định',
    };
}

/**
 * Lấy số slot đã thanh toán
 */
public function getSoSlotDaThanhToanAttribute(): int
{
    return $this->slotPayments()->where('da_thanh_toan', true)->count();
}

/**
 * Lấy tổng số slot cần thanh toán
 */
public function getTongSoSlotAttribute(): int
{
    return $this->slotPayments()->count();
}


    // Các thuộc tính khác của model...
}
