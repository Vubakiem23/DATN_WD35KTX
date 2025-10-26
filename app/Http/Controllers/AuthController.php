<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // Hiển thị form login
    public function login()
    {
        return view('login'); // file: resources/views/auth/login.blade.php
    }

    // Xử lý login
    public function handle_login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Chỉ cho phép login nếu là admin
            if ($user->roles->contains('ten_quyen', 'admin')) {
                $request->session()->regenerate();
                return redirect()->route('admin.index');
            } else {
                // Không phải admin → logout ngay và thông báo lỗi
                Auth::logout();
                return back()->withErrors([
                    'access' => 'Tài khoản thường không thể đăng nhập vào hệ thống. Chỉ admin mới được phép.'
                ]);
            }
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không đúng.'
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('auth.login');
    }
}
