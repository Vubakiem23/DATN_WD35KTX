<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Quan hệ n-n với Role
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_users', 'user_id', 'role_id');
    }
    public function thongBaos()
    {
        return $this->hasMany(ThongBao::class, 'user_id');
    }


    // Lấy tên quyền đầu tiên (hiển thị trực tiếp trong view)
    public function getRoleNameAttribute()
    {
        return $this->roles->first()?->ten_quyen ?? 'Chưa gán';
    }

    // Hàm lấy mã quyền (hoặc tên quyền) của user
    public function getRole()
    {
        return $this->roles->first()?->ma_quyen ?? null;
    }

    /**
     * Quan hệ với SinhVien
     */
    public function sinhVien()
    {
        return $this->hasOne(\App\Models\SinhVien::class, 'user_id', 'id');
    }
}
