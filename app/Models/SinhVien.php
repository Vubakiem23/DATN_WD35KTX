<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Support\Facades\Schema;


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
        'anh_sinh_vien',
        // mới
        'citizen_id_number',
        'citizen_issue_date',
        'citizen_issue_place',
        'guardian_name',
        'guardian_phone',
        'guardian_relationship',
    ];

    protected $casts = [
        'ngay_sinh' => 'date',
        'citizen_issue_date' => 'date',
    ];

    // lịch sử phòng
    public function roomAssignments()
    {
        return $this->hasMany(\App\Models\RoomAssignment::class, 'sinh_vien_id');
    }

    public function currentRoomAssignment()
    {
        return $this->hasOne(\App\Models\RoomAssignment::class, 'sinh_vien_id')
            ->whereNull('end_date')
            ->latest('start_date');
    }

    // vi phạm
    public function violations()
    {
        return $this->hasMany(\App\Models\Violation::class, 'sinh_vien_id');
    }

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
    /* ===== Scopes lọc ===== */

    // q: mã SV, họ tên, SĐT, email + lớp/ngành
    public function scopeSearch($q, $term)
    {
        if (!$term) return $q;
        $like = "%{$term}%";
        return $q->where(function ($s) use ($like) {
            $s->where('ma_sinh_vien', 'like', $like)
                ->orWhere('ho_ten', 'like', $like)
                ->orWhere('so_dien_thoai', 'like', $like)
                ->orWhere('email', 'like', $like)
                ->orWhere('lop', 'like', $like)
                ->orWhere('nganh', 'like', $like);
        });
    }

    // giới tính
    public function scopeGender($q, $gender)
    {
        return $gender ? $q->where('gioi_tinh', $gender) : $q;
    }

    // tình trạng hồ sơ
    public function scopeHoSoStatus($q, $status)
    {
        return $status ? $q->where('trang_thai_ho_so', $status) : $q;
    }

    // phòng
    public function scopeInRoom($q, $roomId)
    {
        return $roomId ? $q->where('phong_id', $roomId) : $q;
    }

    // khu (cột khu nằm ở bảng phong)
    public function scopeInKhu($q, $khu)
    {
        if (!$khu) return $q;
        if (!Schema::hasTable('phong') || !Schema::hasColumn('phong', 'khu')) return $q;
        return $q->whereHas('phong', fn($r) => $r->where('khu', $khu));
    }

    // lớp/ngành/niên khóa
    public function scopeClassLike($q, $lop)
    {
        return $lop ? $q->where('lop', 'like', "%{$lop}%") : $q;
    }

    public function scopeMajorLike($q, $nganh)
    {
        return $nganh ? $q->where('nganh', 'like', "%{$nganh}%") : $q;
    }

    public function scopeIntakeYear($q, $year)
    {
        return $year ? $q->where('khoa_hoc', $year) : $q;
    }
}
