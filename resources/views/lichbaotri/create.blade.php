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

    {{-- 🔹 Cấp 1: Chọn vị trí --}}
    <div class="mb-3">
      <label class="form-label">Chọn vị trí</label>
      <select id="vi_tri" class="form-select form-control" required>
        <option value="">-- Chọn vị trí --</option>
        <option value="phong">Tài sản trong phòng</option>
        <option value="kho">Tài sản trong kho</option>
      </select>
    </div>

    {{-- 🔹 Nếu chọn "Kho" --}}
    <div class="vi-tri-kho d-none">
      <div class="mb-3">
        <label class="form-label">Chọn loại tài sản (trong kho)</label>
        <select id="loai_tai_san_kho" class="form-select form-control">
          <option value="">-- Chọn loại tài sản --</option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Chọn tài sản trong kho</label>
        <select name="tai_san_id" id="tai_san_kho" class="form-select form-control">
          <option value="">-- Chọn tài sản --</option>
        </select>
      </div>
    </div>

    {{-- 🔹 Nếu chọn "Phòng" --}}
    <div class="vi-tri-phong d-none">
      <div class="mb-3">
        <label class="form-label">Chọn phòng</label>
        <select id="phong_id" class="form-select form-control">
          <option value="">-- Chọn phòng --</option>
          @foreach ($phongs as $phong)
          <option value="{{ $phong->id }}">{{ $phong->ten_phong }}</option>
          @endforeach
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Chọn tài sản trong phòng</label>
        <select name="tai_san_id" id="tai_san_phong" class="form-select form-control">
          <option value="">-- Chọn tài sản --</option>
        </select>
      </div>
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
    const viTriSelect = $('#vi_tri');
    const khoSection = $('.vi-tri-kho');
    const phongSection = $('.vi-tri-phong');

    const loaiSelect = $('#loai_tai_san_kho');
    const taiSanKhoSelect = $('#tai_san_kho');
    const phongSelect = $('#phong_id');
    const taiSanPhongSelect = $('#tai_san_phong');

    // Ẩn/hiện theo vị trí
    viTriSelect.on('change', function() {
      const viTri = $(this).val();
      khoSection.addClass('d-none');
      phongSection.addClass('d-none');

      if (viTri === 'kho') {
        khoSection.removeClass('d-none');
        loadLoaiTaiSanKho();
      } else if (viTri === 'phong') {
        phongSection.removeClass('d-none');
      }
    });

    // --- Khi chọn loại tài sản (KHO)
    loaiSelect.on('change', function() {
      const loaiId = $(this).val();
      if (!loaiId) return;
      loadTaiSanKho(loaiId);
    });

    // --- Khi chọn phòng
    phongSelect.on('change', function() {
      const phongId = $(this).val();
      if (!phongId) return;
      loadTaiSanPhong(phongId);
    });

    // 🧩 Hàm load loại tài sản trong kho
    function loadLoaiTaiSanKho() {
      loaiSelect.html('<option>-- Đang tải loại tài sản... --</option>');
      $.get(`/lichbaotri/get-loai-tai-san`, function(data) {
        loaiSelect.html('<option value="">-- Chọn loại tài sản --</option>');
        data.forEach(item => {
          loaiSelect.append(`<option value="${item.id}">${item.ten_loai}</option>`);
        });
      });
    }

    // 🧩 Hàm load tài sản trong kho theo loại
    function loadTaiSanKho(loaiId) {
      taiSanKhoSelect.html('<option>-- Đang tải tài sản... --</option>');
      $.get(`/lichbaotri/get-tai-san-kho/${loaiId}`, function(data) {
        taiSanKhoSelect.html('<option value="">-- Chọn tài sản --</option>');
        data.forEach(item => {
          taiSanKhoSelect.append(`<option value="${item.id}">${item.ten_tai_san} (SL: ${item.so_luong})</option>`);
        });
      });
    }

    // 🧩 Hàm load tài sản trong phòng
    function loadTaiSanPhong(phongId) {
      taiSanPhongSelect.html('<option>-- Đang tải tài sản... --</option>');
      $.get(`/lichbaotri/get-tai-san-phong/${phongId}`, function(data) {
        taiSanPhongSelect.html('<option value="">-- Chọn tài sản --</option>');
        data.forEach(item => {
          taiSanPhongSelect.append(`<option value="${item.id}">[${item.ma_tai_san}] ${item.ten_tai_san}</option>`);
        });
      });
    }
  });
</script>
@endsection