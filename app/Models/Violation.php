<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Violation extends Model
{
    protected $fillable = [
        'sinh_vien_id',
        'violation_type_id',
        'occurred_at',
        'status',
        'penalty_amount',
        'receipt_no',
        'note',
        'image'
    ];


    protected $casts = ['occurred_at' => 'datetime'];

    public function student()
    {
        return $this->belongsTo(SinhVien::class, 'sinh_vien_id');
    }
    public function type()
    {
        return $this->belongsTo(ViolationType::class, 'violation_type_id');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->receipt_no)) {
                $model->receipt_no = self::generateReceiptNo();
            }
        });
    }

    /**
     * Sinh mã biên lai dạng: BL-YYYYMMDD-#### (theo ngày, tăng dần)
     */
    public static function generateReceiptNo(): string
    {
        $date = now()->format('Ymd');
        $prefix = "BL-{$date}-";

        // Tìm mã lớn nhất trong ngày
        $latest = self::where('receipt_no', 'like', $prefix . '%')
            ->orderByDesc('receipt_no')
            ->value('receipt_no');

        $next = 1;
        if ($latest && preg_match('/(\d{4})$/', $latest, $m)) {
            $next = intval($m[1]) + 1;
        }
        return $prefix . str_pad((string)$next, 4, '0', STR_PAD_LEFT);
    }
}
