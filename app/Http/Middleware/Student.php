<?php

namespace App\Http\Middleware;

use App\Models\SinhVien;
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
            if (!$sinhVien) {
                return redirect()
                    ->route('public.home')
                    ->with('warning', 'Không tìm thấy hồ sơ sinh viên gắn với tài khoản này.');
            }

            if ($sinhVien->trang_thai_ho_so === SinhVien::STATUS_PENDING_APPROVAL) {
                return redirect()
                    ->route('public.home')
                    ->with('warning', 'Hồ sơ sinh viên của bạn đang chờ ban quản lý duyệt.');
            }

            // Bỏ qua kiểm tra STATUS_PENDING_CONFIRMATION vì không còn bước xác nhận nữa

            return $next($request);
        }
        return redirect(route('auth.login'));
    }
}
