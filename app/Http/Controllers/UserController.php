<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // 📋 Hiển thị danh sách người dùng
    public function index(Request $request)
    {
        $query = User::with('roles')->orderBy('id', 'desc');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(10)->withQueryString();
        return view('users.index', compact('users'));
    }

    // 🆕 Form thêm người dùng
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    // 💾 Lưu người dùng mới
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role_id'  => 'required|exists:roles,id'
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $user->roles()->sync([$request->role_id]);

        return redirect()->route('users.index')
                         ->with('success', 'Thêm người dùng thành công');
    }

    // 🧑‍💻 Form phân quyền người dùng (chỉ chỉnh vai trò, không chỉnh thông tin cá nhân)
    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $roles = Role::all();
        $userRole = $user->roles->first()->id ?? null;

        return view('users.edit', compact('user', 'roles', 'userRole'));
    }

    // 🔐 Cập nhật vai trò người dùng (chỉ cập nhật quyền)
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        // ❌ Không cập nhật name/email/password ở đây — chỉ cập nhật role
        $user->roles()->sync([$request->role_id]);

        return redirect()->route('users.index')
                         ->with('success', 'Phân quyền người dùng thành công');
    }

    // ❌ Xóa người dùng
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->roles()->detach();
        $user->delete();

        return redirect()->route('users.index')
                         ->with('success', 'Xóa người dùng thành công');
    }
}
