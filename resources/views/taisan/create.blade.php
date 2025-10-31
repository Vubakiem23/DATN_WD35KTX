@extends('admin.layouts.admin')

@section('title', 'Thêm tài sản vào phòng')

@section('content')
<div class="container mt-4">
    <h4 class="mb-3">🛠️ Thêm tài sản thiết bị phòng</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 1️⃣ Form GET: chọn loại và tài sản để hiển thị preview --}}
    <form method="GET" action="{{ route('taisan.create') }}" class="mb-3">
        <label>Loại tài sản:</label>
        <select name="loai_id" class="form-select mb-2 form-control" onchange="this.form.submit()">
            <option value="">-- Chọn loại tài sản --</option>
            @foreach($loaiTaiSans as $loai)
                <option value="{{ $loai->id }}" {{ optional($selectedLoai)->id == $loai->id ? 'selected' : '' }}>
                    {{ $loai->ten_loai }}
                </option>
            @endforeach
        </select>

        @if($taiSans->isNotEmpty())
            <label>Tài sản:</label>
            <select name="kho_tai_san_id" class="form-select form-control" onchange="this.form.submit()">
                <option value="">-- Chọn tài sản --</option>
                @foreach($taiSans as $taiSan)
                    <option value="{{ $taiSan->id }}" {{ optional($selectedTaiSan)->id == $taiSan->id ? 'selected' : '' }}>
                        {{ $taiSan->ten_tai_san }} (Còn: {{ $taiSan->so_luong }})
                    </option>
                @endforeach
            </select>
        @endif
    </form>

    {{-- 2️⃣ Form POST: lưu tài sản vào phòng --}}
    @if($selectedTaiSan)
    <form action="{{ route('taisan.store') }}" method="POST">
        @csrf
        <input type="hidden" name="kho_tai_san_id" value="{{ $selectedTaiSan->id }}">

        {{-- Preview ảnh --}}
        <div class="mb-3 text-center">
            <img src="{{ $selectedTaiSan->hinh_anh }}" style="max-width:200px; border-radius:5px;">
        </div>

        {{-- Chọn phòng --}}
        <div class="mb-3">
            <label>Phòng:</label>
            <select name="phong_id" class="form-select form-control" required>
                <option value="">-- Chọn phòng --</option>
                @foreach($phongs as $phong)
                    <option value="{{ $phong->id }}">{{ $phong->ten_phong }}</option>
                @endforeach
            </select>
        </div>

        {{-- Tình trạng --}}
        <div class="mb-3">
            <label>Tình trạng:</label>
            <select name="tinh_trang" class="form-select form-control" required>
                <option value="Bình thường">Bình thường</option>
                <option value="Hỏng">Hỏng</option>
                <option value="Cần bảo trì">Cần bảo trì</option>
            </select>
        </div>

        {{-- Số lượng --}}
        <div class="mb-3">
            <label>Số lượng:</label>
            <input type="number" name="so_luong" class="form-control" min="1" required placeholder="Nhập số lượng">
        </div>

        <button type="submit" class="btn btn-primary">💾 Lưu tài sản</button>
    </form>
    @endif
</div>
@endsection
