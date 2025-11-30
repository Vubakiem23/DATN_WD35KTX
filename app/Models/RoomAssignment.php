<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomAssignment extends Model
{
    public const STATUS_PENDING_CONFIRMATION = 'cho_xac_nhan';
    public const STATUS_CONFIRMED = 'da_xac_nhan';
    public const STATUS_REJECTED = 'da_tu_choi';

    protected $fillable = ['sinh_vien_id','phong_id','start_date','end_date','trang_thai'];

    protected $attributes = [
        'trang_thai' => self::STATUS_PENDING_CONFIRMATION,
    ];

    public function sinhVien() { return $this->belongsTo(SinhVien::class, 'sinh_vien_id'); }
    public function phong()    { return $this->belongsTo(Phong::class, 'phong_id'); }

    public function isPendingConfirmation(): bool
    {
        return $this->trang_thai === self::STATUS_PENDING_CONFIRMATION;
    }

    public function isConfirmed(): bool
    {
        return $this->trang_thai === self::STATUS_CONFIRMED;
    }

    public function isRejected(): bool
    {
        return $this->trang_thai === self::STATUS_REJECTED;
    }
}
