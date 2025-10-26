@extends('admin.layouts.admin')
@section('title', 'Thêm tài sản mới')

@section('content')
<div class="container mt-4">
    <h4 class="mb-3">➕ Thêm tài sản mới cho loại: {{ $loai->ten_loai }}</h4>

    <a href="{{ route('kho.related', $loai->id) }}" class="btn btn-outline-secondary mb-3">
        ← Quay lại
    </a>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('kho.store', $loai->id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Hiển thị tên tài sản nhưng disable --}}
        <div class="mb-3">
            <label class="form-label">Tên tài sản</label>
            <input type="text" class="form-control" 
                   value="{{ $loai->ten_loai }}" disabled>
            <input type="hidden" name="ten_tai_san" value="{{ $loai->ten_loai }}">
        </div>

        {{-- Số lượng --}}
        <div class="mb-3">
            <label for="quantity" class="form-label">Số lượng</label>
            <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1">
        </div>

        {{-- Đơn vị tính --}}
        <div class="mb-3">
            <label for="don_vi_tinh" class="form-label">Đơn vị tính</label>
            <input type="text" name="don_vi_tinh" id="don_vi_tinh" class="form-control">
        </div>

        {{-- Tình trạng --}}
        <div class="mb-3">
            <label for="tinh_trang" class="form-label">Tình trạng</label>
            <select name="tinh_trang" id="tinh_trang" class="form-select form-control">
                <option value="">-- Chọn tình trạng --</option>
                @foreach($tinhTrangOptions as $status)
                    <option value="{{ $status }}">{{ $status }}</option>
                @endforeach
            </select>
        </div>

        {{-- Ghi chú --}}
        <div class="mb-3">
            <label for="ghi_chu" class="form-label">Ghi chú</label>
            <textarea name="ghi_chu" id="ghi_chu" class="form-control" rows="3"></textarea>
        </div>

        {{-- Hình ảnh --}}
        <div class="mb-3">
            <label for="hinh_anh" class="form-label">Hình ảnh</label>
            <input type="file" name="hinh_anh" id="hinh_anh" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">💾 Lưu tài sản</button>
    </form>
</div>
@endsection
