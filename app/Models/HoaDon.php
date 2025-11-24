<?php


namespace App\Models;




use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class HoaDon extends Model
{
    use HasFactory;

    public const LOAI_TIEN_PHONG = 'tien_phong';
    public const LOAI_DIEN_NUOC = 'dien_nuoc';

    public $table = 'hoa_don';
    public $timestamps = true;
    protected $fillable = [
        'phong_id',
        'invoice_type',
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
        'invoice_type' => 'string',
    ];

    protected $attributes = [
        'invoice_type' => self::LOAI_TIEN_PHONG,
    ];


   public function phong()
{
    return $this->belongsTo(Phong::class, 'phong_id');
}

public function slotPayments()
{
    return $this->hasMany(HoaDonSlotPayment::class, 'hoa_don_id');
}

public function utilitiesPayments()
{
    return $this->hasMany(HoaDonUtilitiesPayment::class, 'hoa_don_id');
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
public function getHinhThucThanhToanLabelAttribute()
{
    if (!$this->relationLoaded('slotPayments')) {
        $this->load('slotPayments');
    }

    // Lấy tất cả slot đã thanh toán
    $labels = $this->slotPayments
        ->where('da_thanh_toan', true)
        ->map(function ($slot) {
            return $slot->hinh_thuc_thanh_toan === 'tien_mat' ? 'Tiền mặt' : 'Chuyển khoản';
        })
        ->countBy(); // đếm số lượng từng loại

    // Ghép thành chuỗi: "1 Tiền mặt, 2 Chuyển khoản"
    return $labels->map(function ($count, $label) {
        return $count . ' ' . $label;
    })->implode(', ');
}



    // Các thuộc tính khác của model...
}
