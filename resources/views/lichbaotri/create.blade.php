@extends('admin.layouts.admin')

@section('title', 'Lên lịch bảo trì')

@section('content')
<div class="container mt-4">
    <h4 class="mb-3">🛠️ Lên lịch bảo trì</h4>

    {{-- Hiển thị lỗi nếu có --}}
    @if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('lichbaotri.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Chọn tài sản --}}
        <div class="mb-3">
            <label class="form-label">Chọn tài sản</label>
            <select name="tai_san_or_kho" class="form-select form-control" required>
                <option value="">-- Chọn tài sản --</option>

                <optgroup label="Tài sản trong phòng">
                    @foreach($taiSanPhong as $ts)
                        <option value="ts_{{ $ts->id }}">
                            {{ $ts->ten_tai_san }} - Phòng: {{ $ts->phong->ten_phong ?? '-' }}
                        </option>
                    @endforeach
                </optgroup>

                <optgroup label="Tài sản trong kho">
                    @foreach($khoTaiSans as $kho)
                        <option value="kho_{{ $kho->id }}">
                            {{ $kho->ten_tai_san }} - Kho (SL: {{ $kho->so_luong }})
                        </option>
                    @endforeach
                </optgroup>
            </select>
        </div>

        {{-- Ngày bảo trì --}}
        <div class="mb-3">
            <label class="form-label">Ngày bảo trì</label>
            <input type="date" name="ngay_bao_tri" class="form-control" required>
        </div>

        {{-- Mô tả --}}
        <div class="mb-3">
            <label class="form-label">Mô tả</label>
            <textarea name="mo_ta" class="form-control" rows="3" placeholder="Nhập mô tả (nếu có)"></textarea>
        </div>

        {{-- Ảnh minh chứng --}}
        <div class="mb-3">
            <label class="form-label">Ảnh minh chứng (nếu có)</label>
            <input type="file" name="hinh_anh_truoc" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">💾 Lưu lịch bảo trì</button>
        <a href="{{ route('lichbaotri.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
