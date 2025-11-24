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
        'user_id',
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
     * Quan hệ với User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
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

    // Lọc theo khu (dựa vào bảng khu mới với cột ten_khu)
    public function scopeInKhu($q, $khuTen)
    {
        if (!$khuTen) return $q;
        if (!Schema::hasTable('khu') || !Schema::hasTable('phong')) return $q;
        // whereHas quan hệ phong -> khu (ten_khu)
        return $q->whereHas('phong', function($r) use ($khuTen) {
            $r->whereHas('khu', function($k) use ($khuTen) {
                $k->where('ten_khu', $khuTen);
            });
        });
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



    public function thongBao()
    {
        return $this->hasMany(ThongBaoSinhVien::class, 'sinh_vien_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($sinhVien) {
            // Khi sinh viên được thêm => tự tạo thông báo
            ThongBaoSinhVien::create([
                'sinh_vien_id' => $sinhVien->id,
                'noi_dung' => 'Hồ sơ của bạn đang được duyệt.',
                'trang_thai' => 'Chờ duyệt',
            ]);
        });
    }
     public function utilitiesPayments()
    {
        return $this->hasMany(HoaDonUtilitiesPayment::class, 'sinh_vien_id', 'id');
    }
}
