@extends('admin.layouts.admin')

@section('title', 'Thêm người dùng mới')

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
        }

        .btn-dergin--muted {
            background: linear-gradient(135deg, #6b7280 0%, #9ca3af 100%);
        }

        .info-badge {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border: 1px solid #93c5fd;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            color: #1e40af;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-badge i {
            font-size: 1rem;
        }
    </style>
    @endpush

    <h4 class="page-title mb-0">Thêm người dùng mới</h4>
    <p class="text-muted mb-3">Tạo tài khoản quản trị viên mới cho hệ thống.</p>

    <div class="form-card">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- Thông báo mặc định là admin --}}
        <div class="info-badge mb-4">
            <i class="fa fa-info-circle"></i>
            <span>Tài khoản được tạo sẽ mặc định có quyền <strong>ADMIN</strong></span>
        </div>

        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Tên <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                       class="form-control @error('name') is-invalid @enderror" 
                       placeholder="Nhập tên người dùng" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                       class="form-control @error('email') is-invalid @enderror" 
                       placeholder="Nhập email" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                <input type="password" name="password" id="password"
                       class="form-control @error('password') is-invalid @enderror" 
                       placeholder="Nhập mật khẩu (tối thiểu 6 ký tự)" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn-dergin btn-dergin--success">
                    <i class="fa fa-plus"></i> Thêm mới
                </button>
                <a href="{{ route('users.index') }}" class="btn-dergin btn-dergin--muted">
                    <i class="fa fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
