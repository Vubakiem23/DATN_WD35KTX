<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    protected $table = 'slots';
    protected $fillable = ['phong_id','ma_slot','ghi_chu','sinh_vien_id','cs_vat_chat','hinh_anh'];
    // Nếu cần lưu json, thêm: protected $casts = ['cs_vat_chat' => 'array'];

    public function phong() { return $this->belongsTo(Phong::class, 'phong_id'); }
    public function sinhVien() { return $this->belongsTo(SinhVien::class, 'sinh_vien_id'); }
}
