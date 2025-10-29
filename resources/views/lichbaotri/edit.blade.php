@extends('admin.layouts.admin')

@section('title', 'Sửa lịch bảo trì')

@section('content')
<div class="container mt-4">
    <h2>✏️ Sửa lịch bảo trì</h2>

    {{-- Form cập nhật --}}
    <form action="{{ route('lichbaotri.update', $lichBaoTri) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Tài sản --}}
      <div class="mb-3">
    <label for="tai_san_or_kho" class="form-label">Tài sản</label>
    <select name="tai_san_or_kho" id="tai_san_or_kho" class="form-control" required disabled>
        @foreach($taiSan as $ts)
            <option value="ts_{{ $ts->id }}"
                {{ $lichBaoTri->tai_san_id == $ts->id ? 'selected' : '' }}>
                {{ $ts->ten_tai_san }} (Phòng: {{ $ts->phong->ten_phong ?? 'Chưa gán' }})
            </option>
        @endforeach

        @foreach($khoTaiSan as $kho)
            <option value="kho_{{ $kho->id }}"
                {{ $lichBaoTri->kho_tai_san_id == $kho->id ? 'selected' : '' }}>
                {{ $kho->ten_tai_san }} (Kho)
            </option>
        @endforeach
    </select>
</div>



        {{-- Ngày bảo trì --}}
        <div class="mb-3">
            <label for="ngay_bao_tri" class="form-label">Ngày bảo trì</label>
            <input type="date" name="ngay_bao_tri" id="ngay_bao_tri" class="form-control" value="{{ $lichBaoTri->ngay_bao_tri }}" required>
        </div>

        {{-- Ngày hoàn thành --}}
        <div class="mb-3">
            <label for="ngay_hoan_thanh" class="form-label">Ngày hoàn thành</label>
            <input type="date" name="ngay_hoan_thanh" id="ngay_hoan_thanh" class="form-control" value="{{ $lichBaoTri->ngay_hoan_thanh }}">
        </div>

        {{-- Mô tả --}}
        <div class="mb-3">
            <label for="mo_ta" class="form-label">Mô tả</label>
            <textarea name="mo_ta" id="mo_ta" class="form-control" rows="3">{{ $lichBaoTri->mo_ta }}</textarea>
        </div>

        {{-- Hình ảnh trước bảo trì --}}
        <div class="mb-3">
            <label class="form-label">Ảnh trước bảo trì</label>
            @if($lichBaoTri->hinh_anh_truoc && file_exists(public_path('uploads/lichbaotri/'.$lichBaoTri->hinh_anh_truoc)))
            <div class="mb-2">
                <img src="{{ asset('uploads/lichbaotri/'.$lichBaoTri->hinh_anh_truoc) }}" alt="Ảnh trước bảo trì" class="img-fluid rounded shadow-sm" style="max-height:200px;">
            </div>
            @else
            <div class="text-muted">Không có ảnh</div>
            @endif
            <input type="file" name="hinh_anh_truoc" class="form-control" accept="image/*">
        </div>

        {{-- Hình ảnh sau bảo trì --}}
        <div class="mb-3">
            <label class="form-label">Ảnh sau bảo trì</label>
            @if($lichBaoTri->hinh_anh && file_exists(public_path('uploads/lichbaotri/'.$lichBaoTri->hinh_anh)))
            <div class="mb-2">
                <img src="{{ asset('uploads/lichbaotri/'.$lichBaoTri->hinh_anh) }}" alt="Ảnh sau bảo trì" class="img-fluid rounded shadow-sm" style="max-height:200px;">
            </div>
            @else
            <div class="text-muted">Chưa cập nhật</div>
            @endif
            <input type="file" name="hinh_anh" class="form-control" accept="image/*">
        </div>

        {{-- Trạng thái --}}
        <div class="mb-3">
            <label for="trang_thai" class="form-label">Trạng thái</label>
            <select name="trang_thai" id="trang_thai" class="form-control">
                <option value="Đang bảo trì" {{ $lichBaoTri->trang_thai == 'Đang bảo trì' ? 'selected' : '' }}>Đang bảo trì</option>
                <option value="Hoàn thành" {{ $lichBaoTri->trang_thai == 'Hoàn thành' ? 'selected' : '' }}>Hoàn thành</option>
            </select>
        </div>

        {{-- Nút submit --}}
        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('lichbaotri.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection