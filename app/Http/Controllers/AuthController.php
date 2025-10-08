<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->getRole($user) == 'student') {
                return $this->return_success('student.index', 'Đăng nhập thành công!');
            }

            if ($user->getRole($user) == 'manager') {
                return $this->return_success('manager.index', 'Đăng nhập thành công!');
            }

            if ($user->getRole($user) == 'admin') {
                return $this->return_success('admin.index', 'Đăng nhập thành công!');
            }
        }
        return view('login');
    }

    public function error(Request $request)
    {
        return view('error');
    }

    public function register(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->getRole($user) == 'student.index') {
                return $this->return_success('student', 'Đăng nhập thành công!');
            }

            if ($user->getRole($user) == 'manager.index') {
                return $this->return_success('manager', 'Đăng nhập thành công!');
            }

            if ($user->getRole($user) == 'admin.index') {
                return $this->return_success('admin', 'Đăng nhập thành công!');
            }
        }
        return view('register');
    }

    public function handle_login(Request $request)
    {
        try {
            $email = $request->input('email');
            $password = $request->input('password');
            $remember = $request->input('remember');
            $url_callback = $request->input('url_callback');

            $credentials = [
                'email' => $email,
                'password' => $password,
            ];

            $user = User::where('email', $email)->first();
            if (!$user) {
                return $this->return_back_error('Không tìm thấy tài khoản!');
            }

            if (Auth::attempt($credentials, $remember)) {
                if ($url_callback) {
                    return redirect()->to($url_callback);
                }

                if ($user->getRole($user) == 'student') {
                    return $this->return_success('student.index', 'Đăng nhập thành công!');
                }

                if ($user->getRole($user) == 'manager') {
                    return $this->return_success('manager.index', 'Đăng nhập thành công!');
                }

                if ($user->getRole($user) == 'admin') {
                    return $this->return_success('admin.index', 'Đăng nhập thành công!');
                }
            }
            return $this->return_back_error('Thất bại, Vui lòng kiểm tra lại tài khoản hoặc mật khẩu!');
        } catch (\Exception $exception) {
            return $this->return_back_error($exception->getMessage());
        }
    }

    public function handle_register(Request $request)
    {
        try {
            $name = $request->input('name');
            $email = $request->input('email');
            $password = $request->input('password');
            $password_confirm = $request->input('password_confirm');

            $isEmail = filter_var($email, FILTER_VALIDATE_EMAIL);
            if (!$isEmail) {
                return $this->return_back_error('Email không hợp lệ!');
            }

            $is_valid = User::checkEmail($email);
            if (!$is_valid) {
                return $this->return_back_error('Email đã được sử dụng!');
            }

            if ($password != $password_confirm) {
                return $this->return_back_error('Mật khẩu và mật khẩu xác nhận không chính xác!');
            }

            if (strlen($password) < 5) {
                return $this->return_back_error('Mật khẩu không hợp lệ!');
            }

            $passwordHash = Hash::make($password);

            $user = new User();

            $user->name = $name ?? '';
            $user->email = $email;
            $user->password = $passwordHash;

            $success = $user->save();

            $studentRole = Role::where('ten_quyen', 'student')->first();

            RoleUser::create([
                'user_id' => $user->id,
                'role_id' => $studentRole->id,
            ]);

            if ($success) {
                return $this->return_success('auth.login', 'Đăng ký thành công!');
            }

            return $this->return_back_error('Đăng ký thất bại!');
        } catch (\Exception $exception) {
            return $this->return_back_error($exception->getMessage());
        }
    }

    public function logout(Request $request)
    {
        try {
            Auth::logout();
            return redirect(route('auth.login'));
        } catch (\Exception $exception) {
            return redirect(route('auth.login'));
        }
    }
}
