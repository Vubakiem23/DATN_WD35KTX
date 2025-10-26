<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['ma_quyen', 'ten_quyen'];

    // Quan hệ n-n với User
    public function users()
    {
        // 'role_users' là bảng pivot, 'role_id' là khóa ở bảng pivot trỏ về Role
        return $this->belongsToMany(User::class, 'role_users', 'role_id', 'user_id');
    }
}
