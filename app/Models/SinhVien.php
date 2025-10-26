<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SinhVien extends Model
{
    protected $table = 'sinh_vien';

    protected $fillable = [
        'ma_sinh_vien',
        'ho_ten',
        'ngay_sinh',
        'gioi_tinh',
        'que_quan',
        'noi_o_hien_tai',
        'lop',
        'nganh',
        'khoa_hoc',
        'so_dien_thoai',
        'email',
        'phong_id',
        'trang_thai_ho_so',
    ];

    /**
     * Sinh viên thuộc phòng nào (nullable)
     */
    public function phong(): BelongsTo
    {
        return $this->belongsTo(Phong::class, 'phong_id', 'id');
    }

    /**
     * Quan hệ: Một sinh viên có nhiều sự cố
     */
    public function suCos(): HasMany
    {
        return $this->hasMany(SuCo::class, 'sinh_vien_id');
    }

    /**
     * Quan hệ với slot
     */
    public function slot()
    {
        return $this->hasOne(\App\Models\Slot::class, 'sinh_vien_id');
    }
}
