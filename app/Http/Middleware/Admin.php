<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Admin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Nếu chưa đăng nhập -> chuyển về login
        if (!Auth::check()) {
            return redirect()->route('auth.login');
        }

        $user = Auth::user();

        // Kiểm tra user có role admin không
        // Giả sử user->roles là quan hệ Many-to-Many với bảng roles
        if ($user->roles->contains('ten_quyen', 'admin')) {
            return $next($request);
        }

        // Nếu không phải admin -> logout và chuyển về login
        Auth::logout();
        return redirect()->route('auth.login')->with('error', 'Chỉ tài khoản admin mới được truy cập.');
    }
}
