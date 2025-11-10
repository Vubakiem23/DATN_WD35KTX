<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class AuthController extends Controller
{
    // Hiển thị form login
    public function login()
    {
        return view('login'); // file: resources/views/auth/login.blade.php
    }

    // Hiển thị form đăng ký tài khoản thường
    public function register()
    {
        return view('register');
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
            $user->loadMissing(['roles', 'sinhVien']);
            $request->session()->regenerate();

            // Phân quyền: Admin → admin dashboard, Student → client dashboard
            if ($user->roles->contains('ten_quyen', 'admin')) {
                return redirect()->route('admin.index');
            } elseif ($user->roles->contains('ten_quyen', 'student')) {
                return redirect()->route('public.home');
            } else {
                // Không có quyền hợp lệ → logout
                Auth::logout();
                return back()->withErrors([
                    'access' => 'Tài khoản của bạn chưa được phân quyền. Vui lòng liên hệ quản trị viên.'
                ]);
            }
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không đúng.'
        ]);
    }

    // Xử lý đăng ký tài khoản thường
    public function handle_register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'password_confirm' => 'required|same:password',
            'terms' => 'accepted',
        ], [
            'password_confirm.same' => 'Xác nhận mật khẩu không khớp.',
            'terms.accepted' => 'Bạn cần đồng ý điều khoản trước khi đăng ký.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Gán quyền mặc định là student (nếu tồn tại)
        $studentRole = Role::where('ten_quyen', 'student')->first();
        if ($studentRole) {
            $user->roles()->attach($studentRole->id);
        }

        return redirect()->route('auth.login')->with('success', 'Tạo tài khoản thành công. Vui lòng đăng nhập.');
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
