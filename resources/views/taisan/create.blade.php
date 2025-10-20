@extends('admin.layouts.admin')

@section('title', 'Thêm tài sản')

@section('content')
<div class="container-fluid">

    <h3 class="mb-3">Thêm tài sản , thiết bị phòng</h3>

    {{-- Hiển thị thông báo lỗi nếu có --}}
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('taisan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="ten_tai_san" class="form-label">Tên tài sản</label>
            <input type="text" name="ten_tai_san" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="hinh_anh" class="form-label">Hình ảnh</label>
            <input type="file" name="hinh_anh" class="form-control" accept="image/*">
            @if(isset($taiSan) && $taiSan->hinh_anh)
            <img src="{{ asset('storage/' . $taiSan->hinh_anh) }}" alt="Ảnh tài sản" width="100" class="mt-2 rounded">
            @endif
        </div>

        <div class="mb-3">
            <label for="so_luong" class="form-label">Số lượng</label>
            <input type="number" name="so_luong" class="form-control" required min="1">
        </div>

        <div class="mb-3">
            <label for="tinh_trang" class="form-label">Tình trạng</label>
            <input type="text" name="tinh_trang" class="form-control" placeholder="Ví dụ: Mới, Hư hỏng...">
        </div>
        <div class="mb-3">
            <label for="tinh_trang_hien_tai" class="form-label">Tình trạng hiện tại</label>
            <select name="tinh_trang_hien_tai" class="form-select">
                <option value="">-- Chọn tình trạng --</option>
                <option value="Mới">Mới</option>
                <option value="Cũ">Cũ</option>
                <option value="Bảo trì">Bảo trì</option>
                <option value="Đã hỏng">Đã hỏng</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="phong_id" class="form-label">Phòng</label>
            <select name="phong_id" class="form-select">
                <option value="">-- Chọn phòng --</option>
                @foreach($phongs as $phong)
                <option value="{{ $phong->id }}">{{ $phong->ten_phong }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">Lưu</button>
        <a href="{{ route('taisan.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection