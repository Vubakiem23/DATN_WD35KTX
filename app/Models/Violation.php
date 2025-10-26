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
}
