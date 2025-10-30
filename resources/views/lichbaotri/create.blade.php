@extends('admin.layouts.admin')

@section('title', 'Lên lịch bảo trì')

@section('content')
<div class="container mt-4">
  <h4 class="mb-3">🛠️ Lên lịch bảo trì</h4>

  {{-- Hiển thị lỗi --}}
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

    {{-- 🔹 Chọn loại tài sản --}}
    <div class="mb-3">
      <label class="form-label">Chọn loại tài sản</label>
      <select id="loai_tai_san" class="form-select form-control" required>
        <option value="">-- Chọn loại --</option>
        <option value="phong" {{ request('taisan_id') ? 'selected' : '' }}>Tài sản trong phòng</option>
        <option value="kho">Tài sản trong kho</option>
      </select>
    </div>

    {{-- 🔹 Chọn tài sản --}}
    <div class="mb-3">
      <label class="form-label">Chọn tài sản</label>
      <select name="tai_san_or_kho" id="tai_san_or_kho" class="form-select form-control" required>
        <option value="">-- Vui lòng chọn loại tài sản trước --</option>
      </select>
    </div>

    {{-- 🔹 Ngày bảo trì --}}
    <div class="mb-3">
      <label class="form-label">Ngày bảo trì</label>
      <input type="date" name="ngay_bao_tri" class="form-control" required>
    </div>

    {{-- 🔹 Mô tả --}}
    <div class="mb-3">
      <label class="form-label">Mô tả</label>
      <textarea name="mo_ta" class="form-control" rows="3" placeholder="Nhập mô tả (nếu có)"></textarea>
    </div>

    {{-- 🔹 Ảnh minh chứng --}}
    <div class="mb-3">
      <label class="form-label">Ảnh minh chứng (nếu có)</label>
      <input type="file" name="hinh_anh_truoc" class="form-control" accept="image/*">
    </div>

    <button type="submit" class="btn btn-primary">💾 Lưu lịch bảo trì</button>
    <a href="{{ route('lichbaotri.index') }}" class="btn btn-secondary">Quay lại</a>
  </form>
</div>

{{-- 🧠 Script --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
  let loaiSelect = $('#loai_tai_san');
  let taiSanSelect = $('#tai_san_or_kho');
  let selectedTaiSanId = "{{ request('taisan_id') ?? '' }}";

  // Hàm load tài sản theo loại
  function loadTaiSan(loai, preselectId = null) {
    taiSanSelect.html('<option value="">-- Đang tải dữ liệu... --</option>');
    if (!loai) {
      taiSanSelect.html('<option value="">-- Vui lòng chọn loại tài sản trước --</option>');
      return;
    }

    $.get(`/lichbaotri/get-tai-san/${loai}`, function(data) {
      taiSanSelect.empty().append('<option value="">-- Chọn tài sản --</option>');

      if (loai === 'phong') {
        data.forEach(function(item) {
          let value = "ts_" + item.id;
          let text = `${item.ten_tai_san} - Phòng: ${item.phong?.ten_phong ?? '-'}`;
          let selected = preselectId && preselectId == item.id ? 'selected' : '';
          taiSanSelect.append(`<option value="${value}" ${selected}>${text}</option>`);
        });
      } else {
        data.forEach(function(item) {
          let value = "kho_" + item.id;
          let text = `${item.ten_tai_san} (SL: ${item.so_luong})`;
          taiSanSelect.append(`<option value="${value}">${text}</option>`);
        });
      }
    }).fail(function() {
      taiSanSelect.html('<option value="">-- Lỗi tải dữ liệu --</option>');
    });
  }

  // Khi đổi loại
  loaiSelect.on('change', function() {
    loadTaiSan($(this).val());
  });

  // Nếu có sẵn tài sản (từ URL)
  if (selectedTaiSanId) {
    let loai = 'phong';
    loadTaiSan(loai, selectedTaiSanId);
  }
});
</script>
@endsection
