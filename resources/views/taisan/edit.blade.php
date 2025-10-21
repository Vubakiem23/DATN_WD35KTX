@extends('admin.layouts.admin')

@section('title', 'Chỉnh sửa tài sản phòng')

@section('content')
<div class="container-fluid">

    <h3 class="mb-3">Chỉnh sửa tài sản / thiết bị phòng</h3>

    {{-- Hiển thị lỗi --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('taisan.update', $taiSan->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            {{-- ✅ Chọn tài sản trong kho --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">Tài sản trong kho</label>
                <select name="kho_tai_san_id" class="form-select" required>
                    <option value="">-- Chọn tài sản --</option>
                    @foreach($khoTaiSans as $kho)
                        <option value="{{ $kho->id }}" {{ $taiSan->kho_tai_san_id == $kho->id ? 'selected' : '' }}>
                            {{ $kho->ma_tai_san }} - {{ $kho->ten_tai_san }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Số lượng</label>
                <input type="number" name="so_luong" class="form-control" value="{{ $taiSan->so_luong }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Tình trạng ban đầu</label>
                <input type="text" name="tinh_trang" class="form-control" value="{{ $taiSan->tinh_trang }}">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Tình trạng hiện tại</label>
                <select name="tinh_trang_hien_tai" class="form-select">
                    <option value="">-- Chọn tình trạng --</option>
                    <option value="Mới" {{ $taiSan->tinh_trang_hien_tai == 'Mới' ? 'selected' : '' }}>Mới</option>
                    <option value="Cũ" {{ $taiSan->tinh_trang_hien_tai == 'Cũ' ? 'selected' : '' }}>Cũ</option>
                    <option value="Bảo trì" {{ $taiSan->tinh_trang_hien_tai == 'Bảo trì' ? 'selected' : '' }}>Bảo trì</option>
                    <option value="Đã hỏng" {{ $taiSan->tinh_trang_hien_tai == 'Đã hỏng' ? 'selected' : '' }}>Đã hỏng</option>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Phòng</label>
                <select name="phong_id" class="form-select">
                    <option value="">-- Chọn phòng --</option>
                    @foreach($phongs as $phong)
                        <option value="{{ $phong->id }}" {{ $taiSan->phong_id == $phong->id ? 'selected' : '' }}>
                            {{ $phong->ten_phong }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- ✅ Hiển thị ảnh từ kho --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">Ảnh từ kho</label>
                @if($taiSan->hinh_anh)
                    <img src="{{ asset('storage/' . $taiSan->hinh_anh) }}" alt="Ảnh tài sản" width="120" class="rounded border mt-2">
                @else
                    <p class="text-muted mt-2">Chưa có hình ảnh</p>
                @endif
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-success">Cập nhật</button>
            <a href="{{ route('taisan.index') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    </form>
</div>
@endsection
