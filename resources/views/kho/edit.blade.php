@extends('admin.layouts.admin')
@section('title', 'Chỉnh sửa tài sản')

@section('content')
<div class="container">
    <h4>✏️ Chỉnh sửa: {{ $taiSan->ten_tai_san }}</h4>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('kho.update', $taiSan->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Tên tài sản (disable, không sửa được) --}}
        <div class="mb-3">
            <label class="form-label">Tên tài sản</label>
            <input type="text" class="form-control" value="{{ $taiSan->ten_tai_san }}" disabled>
            <input type="hidden" name="ten_tai_san" value="{{ $taiSan->ten_tai_san }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Số lượng</label>
            <input type="number" name="so_luong" class="form-control" value="{{ old('so_luong', $taiSan->so_luong) }}" min="1" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Đơn vị</label>
            <input type="text" name="don_vi_tinh" class="form-control" value="{{ old('don_vi_tinh', $taiSan->don_vi_tinh) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Tình trạng</label>
            <select name="tinh_trang" class="form-control">
                <option value="">-- Chọn --</option>
                @foreach($tinhTrangOptions as $option)
                <option value="{{ $option }}" @selected(old('tinh_trang', $taiSan->tinh_trang) == $option)>{{ $option }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Ghi chú</label>
            <textarea name="ghi_chu" class="form-control">{{ old('ghi_chu', $taiSan->ghi_chu) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Hình ảnh</label>
            @if($taiSan->hinh_anh)
            <div class="mb-2">
                <img src="{{ asset('storage/' . $taiSan->hinh_anh) }}" width="150" alt="">
            </div>
            @endif
            <input type="file" name="hinh_anh" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">💾 Cập nhật</button>
        <a href="{{ route('kho.related', $taiSan->loai_id) }}" class="btn btn-secondary">↩️ Quay lại</a>
    </form>
</div>
@endsection