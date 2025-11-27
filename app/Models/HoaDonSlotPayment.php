<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HoaDonSlotPayment extends Model
{
    use HasFactory;

    protected $table = 'hoa_don_slot_payments';

    public const TRANG_THAI_CHUA_THANH_TOAN = 'chua_thanh_toan';
    public const TRANG_THAI_CHO_XAC_NHAN = 'cho_xac_nhan';
    public const TRANG_THAI_DA_THANH_TOAN = 'da_thanh_toan';

    protected $fillable = [
        'hoa_don_id',
        'slot_id',
        'slot_label',
        'sinh_vien_id',
        'sinh_vien_ten',
        'trang_thai',
        'da_thanh_toan',
        'ngay_thanh_toan',
        'hinh_thuc_thanh_toan',
        'ghi_chu',
        'client_ghi_chu',
        'client_transfer_image_path',
        'client_requested_at',
        'xac_nhan_boi',
    ];

    protected $casts = [
        'da_thanh_toan' => 'boolean',
        'ngay_thanh_toan' => 'datetime',
        'client_requested_at' => 'datetime',
    ];

    public function hoaDon(): BelongsTo
    {
        return $this->belongsTo(HoaDon::class, 'hoa_don_id');
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(Slot::class, 'slot_id');
    }

    public function sinhVien(): BelongsTo
    {
        return $this->belongsTo(SinhVien::class, 'sinh_vien_id');
    }


    public function confirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'xac_nhan_boi');
    }
    
}
