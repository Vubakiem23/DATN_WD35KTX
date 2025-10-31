<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Exceptions\PhongException;

class Phong extends Model
{
    protected $table = 'phong';
    protected $fillable = ['ten_phong', 'khu', 'khu_id', 'loai_phong', 'gioi_tinh', 'suc_chua', 'gia_phong', 'trang_thai', 'ghi_chu', 'hinh_anh'];

    protected $attributes = [
        'trang_thai' => 'Trống',
    ];

    /**
     * Relations
     */
    public function sinhVien()
    {
        return $this->hasMany(SinhVien::class, 'phong_id');
    }

    public function taiSan()
    {
        return $this->hasMany(TaiSan::class, 'phong_id');
    }

    public function slots()
    {
        return $this->hasMany(\App\Models\Slot::class, 'phong_id');
    }

    public function khu()
    {
        return $this->belongsTo(\App\Models\Khu::class, 'khu_id', 'id');
    }

    /**
     * Lấy số slot đã được sử dụng (có sinh viên)
     */
    public function usedSlots()
    {
        return $this->slots()->whereNotNull('sinh_vien_id')->count();
    }

    /**
     * Alias cho code cũ nếu có gọi tiếng Việt
     */
    public function soLuongHienTai()
    {
        return $this->usedSlots();
    }

    /**
     * Tổng số slot của phòng
     */
    public function totalSlots()
    {
        return $this->slots()->count();
    }

    /**
     * Số slot còn trống
     */
    public function availableSlots()
    {
        $total = $this->totalSlots();
        return max(0, $total - $this->usedSlots());
    }

    /**
     * Nhãn hiển thị tình trạng phòng
     */
    public function occupancyLabel(): string
    {
        $total = $this->totalSlots();
        $used = $this->usedSlots();
        $available = max(0, $total - $used);
        
        if ($total === 0) {
            return 'Chưa có slot';
        }
        if ($available === 0) {
            return 'Đã ở full';
        }
        return 'Trống ' . $available;
    }

    /**
     * Lấy tỉ lệ % lấp đầy
     */
    public function occupancyRate(): float
    {
        $total = $this->totalSlots();
        if ($total === 0) {
            return 0;
        }
        return round(($this->usedSlots() / $total) * 100, 2);
    }

    /**
     * Kiểm tra phòng có đầy không
     */
    public function isFull(): bool
    {
        return $this->availableSlots() === 0;
    }

    /**
     * Kiểm tra phòng có trống không
     */
    public function isEmpty(): bool
    {
        return $this->usedSlots() === 0;
    }

    /**
     * Kiểm tra phòng có đang bảo trì không
     */
    public function isUnderMaintenance(): bool
    {
        return $this->trang_thai === 'Bảo trì';
    }

    /**
     * Label loại phòng theo số slot
     */
    public static function labelLoaiPhongBySlots(int $totalSlots): string
    {
        if ($totalSlots <= 0) return '';
        if ($totalSlots === 1) return 'Đơn';
        if ($totalSlots === 2) return 'Đôi';
        // Từ 3 trở lên đặt theo số giường
        return 'Phòng ' . $totalSlots;
    }

    /**
     * Cập nhật loại phòng dựa trên số slot
     */
    public function updateLoaiPhongFromSlots(): void
    {
        try {
            $total = $this->totalSlots();
            $this->loai_phong = self::labelLoaiPhongBySlots($total);
            $this->save();
            
            Log::info('Cập nhật loại phòng', [
                'phong_id' => $this->id,
                'loai_phong' => $this->loai_phong,
                'total_slots' => $total
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật loại phòng: ' . $e->getMessage(), [
                'phong_id' => $this->id
            ]);
        }
    }

    /**
     * Thu gọn các slot đang trống để khớp với sức chứa mới.
     * Chỉ xóa slot TRỐNG, ưu tiên xóa slot có id lớn (cuối cùng).
     * Trả về số slot đã xóa.
     */
    public function pruneEmptySlotsToCapacity(?int $targetCapacity = null): int
    {
        $removed = 0;
        try {
            $capacity = $targetCapacity !== null ? (int) $targetCapacity : (int) $this->suc_chua;
            if ($capacity < 0) { $capacity = 0; }

            $totalSlots = $this->totalSlots();
            if ($totalSlots <= $capacity) {
                return 0; // không cần xóa
            }

            $needRemove = $totalSlots - $capacity;

            // Xác định các slot trống (không có sinh viên)
            $emptySlotsQuery = $this->slots()
                ->whereNull('sinh_vien_id')
                ->orderBy('id', 'desc');

            $emptyCount = (clone $emptySlotsQuery)->count();
            if ($emptyCount <= 0) {
                return 0; // không có slot trống để xóa
            }

            $removeCount = min($needRemove, $emptyCount);
            $ids = $emptySlotsQuery->limit($removeCount)->pluck('id');

            if ($ids->isNotEmpty()) {
                \App\Models\Slot::whereIn('id', $ids)->delete();
                $removed = $ids->count();
            }

            // Cập nhật loại phòng theo số slot còn lại
            $this->updateLoaiPhongFromSlots();

            Log::info('Thu gọn slot trống về sức chứa', [
                'phong_id' => $this->id,
                'target_capacity' => $capacity,
                'removed' => $removed
            ]);
        } catch (\Throwable $e) {
            Log::error('Lỗi khi thu gọn slot: ' . $e->getMessage(), [
                'phong_id' => $this->id
            ]);
        }

        return $removed;
    }

    /**
     * Tự động cập nhật trạng thái phòng dựa trên tình trạng slot
     */
    public function updateStatusBasedOnCapacity(): void
    {
        try {
            // Không tự động đổi nếu đang bảo trì
            if ($this->isUnderMaintenance()) {
                return;
            }

            $oldStatus = $this->trang_thai;
            
            if ($this->isEmpty()) {
                $this->trang_thai = 'Trống';
            } elseif ($this->isFull()) {
                $this->trang_thai = 'Đã ở';
            } else {
                // Có người nhưng chưa full
                $this->trang_thai = 'Đã ở';
            }

            // Chỉ save nếu trạng thái thay đổi
            if ($oldStatus !== $this->trang_thai) {
                $this->save();
                
                Log::info('Tự động cập nhật trạng thái phòng', [
                    'phong_id' => $this->id,
                    'trang_thai_cu' => $oldStatus,
                    'trang_thai_moi' => $this->trang_thai
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Lỗi khi tự động cập nhật trạng thái phòng: ' . $e->getMessage(), [
                'phong_id' => $this->id
            ]);
        }
    }

    /**
     * Kiểm tra có thể gán sinh viên vào phòng không
     */
    public function canAssignStudent(\App\Models\SinhVien $sinhVien): array
    {
        $errors = [];

        // Kiểm tra phòng đang bảo trì
        if ($this->isUnderMaintenance()) {
            $errors[] = "Phòng {$this->ten_phong} đang bảo trì";
        }

        // Kiểm tra phòng đã đầy
        if ($this->isFull()) {
            $errors[] = "Phòng {$this->ten_phong} đã đầy";
        }

        // Kiểm tra giới tính
        if ($this->gioi_tinh !== 'Cả hai' && $this->gioi_tinh !== $sinhVien->gioi_tinh) {
            $errors[] = "Phòng dành cho {$this->gioi_tinh}, không phù hợp với sinh viên {$sinhVien->gioi_tinh}";
        }

        // Kiểm tra trạng thái hồ sơ sinh viên
        if ($sinhVien->trang_thai_ho_so !== 'Đã duyệt') {
            $errors[] = "Sinh viên {$sinhVien->ho_ten} chưa được duyệt hồ sơ (Trạng thái: {$sinhVien->trang_thai_ho_so})";
        }

        return [
            'can_assign' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Kiểm tra có thể xóa phòng không
     */
    public function canDelete(): array
    {
        $errors = [];

        // Kiểm tra có sinh viên trong slots
        $slotsCoSinhVien = $this->slots()->whereNotNull('sinh_vien_id')->count();
        if ($slotsCoSinhVien > 0) {
            $errors[] = "Phòng còn {$slotsCoSinhVien} sinh viên đang ở";
        }

        // Kiểm tra quan hệ cũ (nếu còn)
        if (method_exists($this, 'sinhviens')) {
            $countSV = $this->sinhviens()->where('trang_thai_ho_so', 'Đã duyệt')->count();
            if ($countSV > 0) {
                $errors[] = "Phòng còn {$countSV} sinh viên đã duyệt";
            }
        }

        // Kiểm tra có tài sản
        $countTaiSan = $this->taiSan()->count();
        if ($countTaiSan > 0) {
            $errors[] = "Phòng còn {$countTaiSan} tài sản đang quản lý";
        }

        // Kiểm tra sự cố/bảo trì liên quan đến phòng
        if (method_exists(\App\Models\SuCo::class, 'query')) {
            try {
                $countSuCo = \App\Models\SuCo::where('phong_id', $this->id)->count();
                if ($countSuCo > 0) {
                    $errors[] = "Phòng còn {$countSuCo} báo sự cố liên quan";
                }
            } catch (\Throwable $e) {
                // ignore if table missing
            }
        }

        return [
            'can_delete' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Boot method - Events
     */
    protected static function boot()
    {
        parent::boot();

        // Trước khi tạo phòng mới
        static::creating(function ($phong) {
            Log::info('Đang tạo phòng mới', ['ten_phong' => $phong->ten_phong]);
        });

        // Sau khi tạo phòng
        static::created(function ($phong) {
            Log::info('Đã tạo phòng thành công', [
                'phong_id' => $phong->id,
                'ten_phong' => $phong->ten_phong
            ]);
        });

        // Trước khi cập nhật
        static::updating(function ($phong) {
            // Kiểm tra nếu giảm sức chứa
            if ($phong->isDirty('suc_chua')) {
                $oldSucChua = $phong->getOriginal('suc_chua');
                $newSucChua = $phong->suc_chua;
                
                if ($newSucChua < $oldSucChua) {
                    $usedSlots = $phong->usedSlots();
                    if ($newSucChua < $usedSlots) {
                        Log::warning('Cố gắng giảm sức chứa xuống dưới số sinh viên hiện tại', [
                            'phong_id' => $phong->id,
                            'suc_chua_cu' => $oldSucChua,
                            'suc_chua_moi' => $newSucChua,
                            'so_sinh_vien' => $usedSlots
                        ]);
                        // Laravel sẽ throw validation error ở controller
                    }
                }
            }
        });

        // Sau khi cập nhật
        static::updated(function ($phong) {
            Log::info('Đã cập nhật phòng', ['phong_id' => $phong->id]);
        });

        // Trước khi xóa
        static::deleting(function ($phong) {
            Log::info('Đang xóa phòng', [
                'phong_id' => $phong->id,
                'ten_phong' => $phong->ten_phong
            ]);
        });

        // Sau khi xóa
        static::deleted(function ($phong) {
            Log::info('Đã xóa phòng thành công', ['ten_phong' => $phong->ten_phong]);
        });
    }

    /**
     * Scope: Lọc phòng theo khu
     */
    public function scopeKhu($query, $khu)
    {
        return $query->where('khu', $khu);
    }

    /**
     * Scope: Lọc phòng theo giới tính
     */
    public function scopeGioiTinh($query, $gioiTinh)
    {
        return $query->where('gioi_tinh', $gioiTinh);
    }

    /**
     * Scope: Chỉ lấy phòng trống
     */
    public function scopeTrong($query)
    {
        return $query->where('trang_thai', 'Trống');
    }

    /**
     * Scope: Chỉ lấy phòng đang có người ở
     */
    public function scopeDaO($query)
    {
        return $query->where('trang_thai', 'Đã ở');
    }

    /**
     * Scope: Chỉ lấy phòng đang bảo trì
     */
    public function scopeBaoTri($query)
    {
        return $query->where('trang_thai', 'Bảo trì');
    }

    /**
     * Scope: Lấy phòng có slot trống
     */
    public function scopeCoSlotTrong($query)
    {
        return $query->whereHas('slots', function ($q) {
            $q->whereNull('sinh_vien_id');
        });
    }
}
