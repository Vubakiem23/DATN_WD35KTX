@extends('admin.layouts.admin')

@section('title', 'Lên lịch bảo trì')

@section('content')
<div class="container mt-4">
  <h4 class="mb-3">🛠️ Lên lịch bảo trì</h4>

  <form action="{{ route('lichbaotri.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- Nếu có tài sản được chọn từ trang trước --}}
    @if(isset($selectedTaiSan))
      <div class="mb-3">
        <label class="form-label">Tài sản</label>
        <input type="hidden" name="tai_san_id" value="{{ $selectedTaiSan->id }}">
        <input type="text" class="form-control" value="{{ $selectedTaiSan->khoTaiSan->ten_tai_san ?? $selectedTaiSan->ten_tai_san }}" readonly>
      </div>

      <div class="mb-3">
        <label class="form-label">Phòng</label>
        <input type="text" class="form-control" value="{{ $selectedTaiSan->phong->ten_phong ?? 'Chưa gán phòng' }}" readonly>
      </div>
    @else
      {{-- Nếu không có sẵn thì cho chọn từ danh sách --}}
      <div class="mb-3">
        <label class="form-label">Chọn tài sản</label>
        <select name="tai_san_id" class="form-select form-control" required>
          <option value="">-- Chọn tài sản --</option>
          @foreach($taiSan as $ts)
            <option value="{{ $ts->id }}">
              {{ $ts->khoTaiSan->ten_tai_san ?? $ts->ten_tai_san }} - {{ $ts->phong->ten_phong ?? 'Chưa có phòng' }}
            </option>
          @endforeach
        </select>
      </div>
    @endif

    <div class="mb-3">
      <label class="form-label">Ngày bảo trì</label>
      <input type="date" name="ngay_bao_tri" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Mô tả</label>
      <textarea name="mo_ta" class="form-control" rows="3" placeholder="Nhập mô tả (nếu có)"></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Ảnh minh chứng (nếu có)</label>
      <input type="file" name="hinh_anh" class="form-control">
    </div>

    <button type="submit" class="btn btn-primary">💾 Lưu lịch bảo trì</button>
    <a href="{{ route('lichbaotri.index') }}" class="btn btn-secondary">Quay lại</a>
  </form>
</div>
@endsection
