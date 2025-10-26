@extends('admin.layouts.admin')

@section('content')
<div class="x_panel">
    <div class="x_title">
        <h2><i class="fa fa-user-shield text-primary"></i> Phân quyền người dùng</h2>
        <ul class="nav navbar-right panel_toolbox">
            <li><a href="{{ route('users.index') }}" class="btn btn-sm btn-light"><i class="fa fa-arrow-left"></i> Quay lại</a></li>
        </ul>
        <div class="clearfix"></div>
    </div>

    <div class="x_content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- 🧍 Thông tin người dùng (readonly) --}}
            <div class="mb-3">
                <label class="form-label">Tên người dùng</label>
                <input type="text" class="form-control" value="{{ $user->name }}" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="text" class="form-control" value="{{ $user->email }}" readonly>
            </div>

            {{-- 🎭 Phân quyền --}}
            <div class="mb-3">
                <label for="role_id" class="form-label">Phân quyền</label>
                <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror">
                    <option value="">-- Chọn quyền --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id', $userRole) == $role->id ? 'selected' : '' }}>
                            {{ $role->ten_quyen }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Lưu phân quyền</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Quay lại</a>
            </div>
        </form>
    </div>
</div>
@endsection
