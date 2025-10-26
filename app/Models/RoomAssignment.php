<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomAssignment extends Model
{
    protected $fillable = ['sinh_vien_id','phong_id','start_date','end_date'];

    public function sinhVien() { return $this->belongsTo(SinhVien::class, 'sinh_vien_id'); }
    public function phong()    { return $this->belongsTo(Phong::class, 'phong_id'); }
}
