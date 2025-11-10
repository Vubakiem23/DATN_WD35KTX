<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Student
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user()->loadMissing(['roles', 'sinhVien']);

            // Kiểm tra theo mã quyền chuẩn
            if (!$user->roles->contains('ma_quyen', 'student')) {
                return redirect(route('error'));
            }

            $sinhVien = $user->sinhVien;
            if (!$sinhVien || $sinhVien->trang_thai_ho_so !== 'Đã duyệt') {
                return redirect()
                    ->route('public.home')
                    ->with('warning', 'Hồ sơ sinh viên của bạn chưa được duyệt. Vui lòng hoàn thành và chờ phê duyệt để sử dụng các chức năng dành cho sinh viên.');
            }

            return $next($request);
        }
        return redirect(route('auth.login'));
    }
}
