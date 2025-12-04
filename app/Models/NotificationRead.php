<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationRead extends Model
{
    use HasFactory;

    protected $table = 'notification_reads';
    public $timestamps = false; // migration không có created_at / updated_at

    protected $fillable = [
        'user_id',
        'type',
        'type_id',
        'read_at',
    ];
}
