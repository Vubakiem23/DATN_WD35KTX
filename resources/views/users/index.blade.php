@extends('admin.layouts.admin')

@section('content')
<div class="x_panel">
    <div class="x_title">
        <h2><i class="fa fa-users text-primary"></i> Danh sách người dùng</h2>
        <ul class="nav navbar-right panel_toolbox">
            <li>
                <a href="{{ route('users.create') }}" class="btn btn-success btn-sm">
                    <i class="fa fa-plus"></i> Thêm người dùng
                </a>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>

    <div class="x_content">

        {{-- 🔍 Tìm kiếm --}}
        <form method="GET" class="form-inline mb-3">
            <div class="form-group">
                <input type="text" name="search" value="{{ request('search') ?? '' }}" class="form-control input-sm" placeholder="Tìm kiếm theo tên, email, quyền">
            </div>
            <button type="submit" class="btn btn-primary btn-sm ml-2">Tìm</button>
            @if(request('search'))
                <a href="{{ route('users.index') }}" class="btn btn-light btn-sm ml-2">Xóa lọc</a>
            @endif
        </form>

        {{-- 🟢 Thông báo --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        {{-- 📋 Bảng danh sách --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="bg-light text-center">
                    <tr>
                        <th width="40">ID</th>
                        <th>Tên</th>
                        <th>Email</th>
                        <th>Quyền</th>
                        <th width="140">Ngày tạo</th>
                        <th width="120">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="text-center">{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td class="text-center">
                            @if($user->roles->isNotEmpty())
                                @foreach($user->roles as $role)
                                    <span class="badge badge-info">{{ $role->ten_quyen }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">Chưa gán</span>
                            @endif
                        </td>
                        <td class="text-center">
                            {{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm" title="Sửa">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <!-- <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Xác nhận xóa người dùng này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Xóa">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form> -->
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Chưa có người dùng nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- 📑 Phân trang --}}
        <div class="d-flex justify-content-center mt-3">
            {{ $users->onEachSide(1)->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>

{{-- 🎨 CSS --}}
<style>
    .table th, .table td {
        vertical-align: middle !important;
    }

    .badge {
        font-size: 90%;
    }

    .d-flex.gap-1 > form {
        margin: 0;
    }
</style>
@endsection
