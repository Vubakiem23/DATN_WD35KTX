@extends('admin.layouts.admin')

@section('title', 'Phân quyền người dùng')

@section('content')
<div class="container mt-4">

    @push('styles')
    <style>
        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
        }

        .form-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            padding: 2rem;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            padding: 0.65rem 1rem;
            transition: all 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #4e54c8;
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.1);
        }

        .form-control[readonly] {
            background-color: #f9fafb;
            cursor: not-allowed;
            color: #6b7280;
        }

        .btn-dergin {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .35rem;
            padding: .5rem 1.2rem;
            border-radius: 999px;
            font-weight: 600;
            font-size: .85rem;
            border: none;
            color: #fff;
            background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
            box-shadow: 0 6px 16px rgba(78, 84, 200, .22);
            transition: transform .2s ease, box-shadow .2s ease;
            text-decoration: none;
        }

        .btn-dergin:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(78, 84, 200, .32);
            color: #fff;
        }

        .btn-dergin i {
            font-size: .8rem;
        }

        .btn-dergin--success {
            background: linear-gradient(135deg, #10b981 0%, #22c55e 100%);
            box-shadow: 0 6px 16px rgba(16, 185, 129, .22);
        }

        .btn-dergin--success:hover {
            box-shadow: 0 10px 22px rgba(16, 185, 129, .32);
        }

        .btn-dergin--muted {
            background: linear-gradient(135deg, #6b7280 0%, #9ca3af 100%);
        }

        .info-section {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #bae6fd;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .info-section-title {
            font-weight: 600;
            color: #0369a1;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-section-title i {
            font-size: 1rem;
        }

        .info-section-text {
            color: #0c4a6e;
            font-size: 0.875rem;
            margin: 0;
        }

        .role-select-wrapper {
            position: relative;
        }

        .role-select-wrapper::after {
            content: "🎭";
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            font-size: 1.2rem;
        }
    </style>
    @endpush

    <h4 class="page-title mb-0">Phân quyền người dùng</h4>
    <p class="text-muted mb-3">Thay đổi quyền truy cập cho tài khoản người dùng.</p>

    <div class="form-card">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- Thông tin người dùng --}}
        <div class="info-section">
            <div class="info-section-title">
                <i class="fa fa-user"></i>
                <span>Thông tin người dùng</span>
            </div>
            <p class="info-section-text mb-0">Thông tin tài khoản không thể thay đổi tại đây. Chỉ có thể cập nhật phân quyền.</p>
        </div>

        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Thông tin người dùng (readonly) --}}
            <div class="mb-3">
                <label class="form-label">Tên người dùng</label>
                <input type="text" class="form-control" value="{{ $user->name }}" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="text" class="form-control" value="{{ $user->email }}" readonly>
            </div>

            {{-- Phân quyền --}}
            <div class="mb-3">
                <label for="role_id" class="form-label">Phân quyền <span class="text-danger">*</span></label>
                <div class="role-select-wrapper">
                    <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                        <option value="">-- Chọn quyền --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $userRole) == $role->id ? 'selected' : '' }}>
                                {{ ucfirst($role->ten_quyen) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('role_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn-dergin btn-dergin--success">
                    <i class="fa fa-save"></i> Lưu phân quyền
                </button>
                <a href="{{ route('users.index') }}" class="btn-dergin btn-dergin--muted">
                    <i class="fa fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
