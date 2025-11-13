@extends('admin.layouts.admin')

@section('title', 'Chỉnh sửa tài sản')

@section('content')
<div class="container mt-4">
    <div class="card p-4 shadow-sm">
        <h4 class="mb-4 text-primary fw-semibold">✏️ Chỉnh sửa tài sản / thiết bị phòng</h4>

        {{-- Hiển thị lỗi --}}
        @if ($errors->any())
        <div class="alert alert-danger rounded-3 mb-3">
            <ul class="mb-0">
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

            {{-- Tên tài sản --}}
            <div class="mb-3">
                <label for="ten_tai_san" class="form-label fw-semibold text-secondary">Tên tài sản</label>
                <input type="text" name="ten_tai_san" readonly class="form-control"
                    value="{{ old('ten_tai_san', $taiSan->ten_tai_san) }}" required>
            </div>

            {{-- Số lượng --}}
            <div class="mb-3">
                <label for="so_luong" class="form-label fw-semibold text-secondary">Số lượng</label>
                <input type="number" name="so_luong" class="form-control"
                    required min="1" value="{{ old('so_luong', $taiSan->so_luong) }}" readonly>
            </div>

            {{-- Tình trạng ban đầu --}}
            <div class="mb-3">
                <label for="tinh_trang" class="form-label fw-semibold text-secondary">Tình trạng ban đầu</label>
                <input type="text" name="tinh_trang" class="form-control"
                    value="{{ old('tinh_trang', $taiSan->tinh_trang) }}"
                    placeholder="Ví dụ: Mới, Hư hỏng..." readonly>
            </div>

            {{-- Tình trạng hiện tại --}}
            <div class="mb-3">
                <label for="tinh_trang_hien_tai" class="form-label fw-semibold text-secondary">Tình trạng hiện tại</label>
                <select name="tinh_trang_hien_tai" class="form-select form-control">
                    <option value="">-- Chọn tình trạng --</option>
                    <option value="Mới" {{ old('tinh_trang_hien_tai', $taiSan->tinh_trang_hien_tai) == 'Mới' ? 'selected' : '' }}>Mới</option>
                    <option value="Bình thường" {{ old('tinh_trang_hien_tai', $taiSan->tinh_trang_hien_tai) == 'Bình thường' ? 'selected' : '' }}>Bình thường</option>
                    <option value="Cũ" {{ old('tinh_trang_hien_tai', $taiSan->tinh_trang_hien_tai) == 'Cũ' ? 'selected' : '' }}>Cũ</option>
                    <option value="Đang bảo trì" {{ old('tinh_trang_hien_tai', $taiSan->tinh_trang_hien_tai) == 'Đang bảo trì' ? 'selected' : '' }}>Đang bảo trì</option>
                    <option value="Hỏng" {{ old('tinh_trang_hien_tai', $taiSan->tinh_trang_hien_tai) == 'Hỏng' ? 'selected' : '' }}>Hỏng</option>
                </select>
            </div>

            {{-- Phòng --}}
            <div class="mb-3">
                <label for="phong_id" class="form-label fw-semibold text-secondary">Phòng</label>
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

            {{-- Nút submit --}}
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">💾 Cập nhật</button>
                <a href="{{ route('taisan.index') }}" class="btn btn-secondary">Quay lại</a>
            </div>
        </form>
    </div>
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
