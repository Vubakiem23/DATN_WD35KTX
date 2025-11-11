@extends('admin.layouts.admin')
@section('title', 'Thêm tài sản mới')

@section('content')
<style>
  /* 🎨 Giao diện đồng màu với trang Lên lịch bảo trì */
  .page-title {
    font-weight: 700;
    color: #1e293b;
  }

  .card {
    border: none;
    border-radius: 14px;
    box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
  }

  .form-control,
  .form-select {
    border-radius: 10px;
    border: 1px solid #d1d5db;
    padding: 10px 14px;
    transition: 0.2s ease;
  }

  .form-control:focus,
  .form-select:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
  }

  .btn-primary {
    background-color: #2563eb;
    border: none;
    border-radius: 8px;
    padding: 10px 18px;
    font-weight: 500;
  }

  .btn-primary:hover {
    background-color: #1d4ed8;
  }

  .btn-secondary {
    border-radius: 8px;
    padding: 10px 18px;
  }

  .form-label {
    font-weight: 600;
    color: #334155;
  }

  .section-title {
    font-weight: 600;
    color: #2563eb;
    border-left: 4px solid #2563eb;
    padding-left: 10px;
    margin-bottom: 15px;
  }
</style>

<div class="container mt-4">
  <h4 class="page-title mb-3">➕ Thêm tài sản mới cho loại: {{ $loai->ten_loai }}</h4>

  <a href="{{ route('kho.related', $loai->id) }}" class="btn btn-secondary mb-3">
    ← Quay lại
  </a>

  {{-- Hiển thị lỗi --}}
  @if ($errors->any())
    <div class="alert alert-danger shadow-sm">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('kho.store', $loai->id) }}" method="POST" enctype="multipart/form-data" class="p-4 card">
    @csrf

    <h6 class="section-title">Thông tin tài sản</h6>

    {{-- Hiển thị tên tài sản --}}
    <div class="mb-3">
      <label class="form-label">Tên tài sản</label>
      <input type="text" class="form-control" value="{{ $loai->ten_loai }}" disabled>
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
      <input type="text" name="don_vi_tinh" id="don_vi_tinh" class="form-control" placeholder="VD: chiếc, bộ, cái...">
    </div>

    {{-- Tình trạng --}}
    <div class="mb-3">
      <label for="tinh_trang" class="form-label">Tình trạng</label>
      <select name="tinh_trang" id="tinh_trang" class="form-select">
        <option value="">-- Chọn tình trạng --</option>
        @foreach($tinhTrangOptions as $status)
          <option value="{{ $status }}">{{ $status }}</option>
        @endforeach
      </select>
    </div>

    {{-- Ghi chú --}}
    <div class="mb-3">
      <label for="ghi_chu" class="form-label">Ghi chú</label>
      <textarea name="ghi_chu" id="ghi_chu" class="form-control" rows="3" placeholder="Nhập ghi chú (nếu có)"></textarea>
    </div>

    {{-- Hình ảnh --}}
    <div class="mb-3">
      <label for="hinh_anh" class="form-label">Hình ảnh tài sản</label>
      <input type="file" name="hinh_anh" id="hinh_anh" class="form-control" accept="image/*">
    </div>

    <div class="text-end">
      <button type="submit" class="btn btn-primary me-2">💾 Lưu tài sản</button>
      <a href="{{ route('kho.related', $loai->id) }}" class="btn btn-secondary">↩️ Quay lại</a>
    </div>
  </form>
</div>
@endsection
