@extends('admin.layouts.admin')

@section('title', 'Thêm thông báo')

@section('content')
<div class="container-fluid">

    <h3 class="mb-3">Thêm thông báo mới</h3>

    {{-- Hiển thị thông báo lỗi nếu có --}}
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Form thêm thông báo --}}
    <form action="{{ route('thongbao.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="tieu_de" class="form-label">Tiêu đề</label>
            <input type="text" name="tieu_de" class="form-control" value="{{ old('tieu_de') }}" required>
        </div>

        <div class="mb-3">
            <label for="noi_dung" class="form-label">Nội dung</label>
            <textarea name="noi_dung" class="form-control" rows="5" required>{{ old('noi_dung') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="ngay_dang" class="form-label">Ngày đăng</label>
            <input type="date" name="ngay_dang" class="form-control" value="{{ old('ngay_dang') }}" required>
        </div>

        <div class="mb-3">
            <label for="doi_tuong" class="form-label">Đối tượng</label>
            <select name="doi_tuong" class="form-select" required>
                <option value="">-- Chọn đối tượng --</option>
                <option value="Sinh viên" {{ old('doi_tuong') == 'Sinh viên' ? 'selected' : '' }}>Sinh viên</option>
                <option value="Giảng viên" {{ old('doi_tuong') == 'Giảng viên' ? 'selected' : '' }}>Giảng viên</option>
                <option value="Tất cả" {{ old('doi_tuong') == 'Tất cả' ? 'selected' : '' }}>Tất cả</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Lưu</button>
        <a href="{{ route('thongbao.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
