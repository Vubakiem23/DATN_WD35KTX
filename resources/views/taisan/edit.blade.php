@extends('admin.layouts.admin')

@section('title', 'Chỉnh sửa tài sản')

@section('content')

<div class="container-fluid">

    <h3 class="mb-3">Chỉnh sửa tài sản / thiết bị phòng</h3>

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

    <form action="{{ route('taisan.update', $taiSan->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="kho_tai_san_id" value="{{ $taiSan->kho_tai_san_id }}">

        <div class="mb-3">
            <label for="ten_tai_san" class="form-label">Tên tài sản</label>
            <input type="text" name="ten_tai_san" readonly class="form-control"
                value="{{ old('ten_tai_san', $taiSan->ten_tai_san) }}" required>
        </div>

       

        <div class="mb-3">
            <label for="so_luong" class="form-label">Số lượng</label>
            <input type="number" name="so_luong" class="form-control"
                required min="1" value="{{ old('so_luong', $taiSan->so_luong) }}">
        </div>

        <div class="mb-3">
            <label for="tinh_trang" class="form-label">Tình trạng ban đầu</label>
            <input type="text" name="tinh_trang" class="form-control"
                value="{{ old('tinh_trang', $taiSan->tinh_trang) }}"
                placeholder="Ví dụ: Mới, Hư hỏng..." readonly>
        </div>

        <div class="mb-3">
            <label for="tinh_trang_hien_tai" class="form-label">Tình trạng hiện tại</label>
            <select name="tinh_trang_hien_tai" class="form-select form-control">
                <option value="">-- Chọn tình trạng --</option>
                <option value="Mới" {{ old('tinh_trang_hien_tai', $taiSan->tinh_trang_hien_tai) == 'Mới' ? 'selected' : '' }}>Mới</option>
                <option value="Cũ" {{ old('tinh_trang_hien_tai', $taiSan->tinh_trang_hien_tai) == 'Cũ' ? 'selected' : '' }}>Cũ</option>
                <option value="Bảo trì" {{ old('tinh_trang_hien_tai', $taiSan->tinh_trang_hien_tai) == 'Bảo trì' ? 'selected' : '' }}>Bảo trì</option>
                <option value="Đã hỏng" {{ old('tinh_trang_hien_tai', $taiSan->tinh_trang_hien_tai) == 'Đã hỏng' ? 'selected' : '' }}>Đã hỏng</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="phong_id" class="form-label">Phòng</label>
            <select name="phong_id" class="form-select form-control">
                <option value="">-- Chọn phòng --</option>
                @foreach($phongs as $phong)
                <option value="{{ $phong->id }}"
                    {{ old('phong_id', $taiSan->phong_id) == $phong->id ? 'selected' : '' }}>
                    {{ $phong->ten_phong }}
                </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('taisan.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>

{{-- Script xem trước ảnh --}}
<script>
    function previewImage(event) {
        const preview = document.getElementById('preview');
        const file = event.target.files[0];
        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('d-none');
        } else {
            preview.src = '#';
            preview.classList.add('d-none');
        }
    }
</script>
@endsection