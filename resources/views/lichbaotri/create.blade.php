@extends('admin.layouts.admin')

@section('title', 'Lên lịch bảo trì')

@section('content')
<style>
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
  .btn-secondary {
    border-radius: 8px;
    padding: 10px 18px;
  }
  .section-title {
    font-weight: 600;
    color: #2563eb;
    border-left: 4px solid #2563eb;
    padding-left: 10px;
    margin-bottom: 15px;
  }
  .asset-info-box {
    background: #f8fafc;
    border: 1px dashed #2563eb;
    font-size: 13px;
    padding: 5px 8px;
    border-radius: 8px;
  }
  .asset-info-box span {
    display: block;
    line-height: 1.3rem;
  }
</style>


<div class="container mt-4">

  @if ($taiSan)
  {{-- ✅ Từ nút "Bảo trì" --}}
  <h4 class="page-title mb-3">🛠️ Lên lịch bảo trì tài sản</h4>

  <form action="{{ route('lichbaotri.store') }}" method="POST" enctype="multipart/form-data" class="p-4 card">
    @csrf

    <input type="hidden" name="tai_san_id[]" value="{{ $taiSan->id }}">

 <h6 class="section-title">Thông tin tài sản</h6>
<div class="d-flex gap-3 align-items-start mb-3">

  {{-- Ảnh tài sản --}}
 <div>
    @if (!empty($taiSan->khoTaiSan->hinh_anh))
      <img src="{{ Storage::url('kho/' . $taiSan->khoTaiSan->hinh_anh) }}"
           alt="Ảnh tài sản"
           style="width:100px;height:100px;object-fit:cover;border-radius:6px;">
    @else
      <div style="width:100px;height:100px;background:#e5e7eb;border-radius:6px;display:flex;align-items:center;justify-content:center;">
        Không có ảnh
      </div>
    @endif
</div>


  {{-- Thông tin chi tiết --}}
  <div>
    <strong>Tên:</strong> {{ $taiSan->khoTaiSan->ten_tai_san }} <br>

    <strong>Mã:</strong> {{ $taiSan->khoTaiSan->ma_tai_san }} <br>

    <strong>Phòng:</strong> {{ $taiSan->phong->ten_phong ?? 'Trong kho' }} <br>


    {{-- ✅ Người đang sử dụng từ Slot --}}
    <strong>Sinh viên sử dụng:</strong>
    @php
      $slot = $taiSan->slots->first();
    @endphp
    {{ $slot && $slot->sinhVien ? $slot->sinhVien->ho_ten : 'Không có' }} <br>

    <strong>Mã Slot:</strong>
    {{ $slot ? $slot->ma_slot : '-' }}
  </div>

</div>


    <h6 class="section-title">Mô tả bảo trì</h6>
    <textarea name="mo_ta[]" class="form-control" rows="3"></textarea>

    <h6 class="section-title mt-3">Ảnh trước bảo trì</h6>
    <input type="file" name="hinh_anh[]" class="form-control" accept="image/*">

    <h6 class="section-title mt-3">Ngày bảo trì</h6>
    <input type="date" name="ngay_bao_tri" class="form-control" required>

    <div class="text-end mt-3">
      <button type="submit" class="btn btn-primary me-2">💾 Lưu lịch bảo trì</button>
      <a href="{{ route('lichbaotri.index') }}" class="btn btn-secondary">↩️ Quay lại</a>
    </div>
  </form>

  @else
  {{-- ✅ Form bảo trì nhiều tài sản --}}
  <h4 class="page-title mb-3">🛠️ Lên lịch bảo trì nhiều tài sản</h4>

  @if($errors->any())
  <div class="alert alert-danger shadow-sm">
    <ul class="mb-0">
      @foreach($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
  @endif

  <form action="{{ route('lichbaotri.store') }}" method="POST" enctype="multipart/form-data" class="p-4 card">
    @csrf

    <h6 class="section-title">Chọn vị trí tài sản</h6>
    <div class="mb-3">
      <select id="vi_tri" class="form-select" required>
        <option value="">-- Chọn vị trí --</option>
        <option value="phong">Trong phòng</option>
        <option value="kho">Trong kho</option>
      </select>
    </div>

    <div class="vi-tri-phong d-none mb-4">
      <select id="phong_id" class="form-select">
        <option value="">-- Chọn phòng --</option>
        @foreach ($phongs as $phong)
          <option value="{{ $phong->id }}">{{ $phong->ten_phong }}</option>
        @endforeach
      </select>
    </div>

    <div class="vi-tri-kho d-none mb-4">
      <select id="loai_tai_san_kho" class="form-select">
        <option value="">-- Chọn loại tài sản --</option>
      </select>
    </div>

    <h6 class="section-title">Danh sách tài sản</h6>
    <table class="table table-bordered align-middle" id="assetTable">
      <thead>
        <tr>
          <th>Tài sản</th>
          <th>Thông tin</th>
          <th>Mô tả</th>
          <th>Ảnh</th>
          <th>#</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>

    <button type="button" id="addRow" class="btn btn-outline-primary mb-3">
      ➕ Thêm tài sản
    </button>

    <h6 class="section-title">Ngày bảo trì</h6>
    <input type="date" name="ngay_bao_tri" class="form-control" required>

    <div class="text-end">
      <button type="submit" class="btn btn-primary me-2">💾 Lưu</button>
      <a href="{{ route('lichbaotri.index') }}" class="btn btn-secondary">↩️ Quay lại</a>
    </div>
  </form>
  @endif
</div>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

@if(!$taiSan)
<script>
$(function () {
  const body = $('#assetTable tbody');
  let assets = [];

  $('#vi_tri').on('change', function () {
    body.html('');
    const showPhong = $(this).val() === 'phong';
    const showKho = $(this).val() === 'kho';

    $('.vi-tri-phong').toggleClass('d-none', !showPhong);
    $('.vi-tri-kho').toggleClass('d-none', !showKho);

    if (showKho) loadLoai();
  });

  $('#phong_id').on('change', e => loadPhong(e.target.value));
  $('#loai_tai_san_kho').on('change', e => loadKho(e.target.value));
  
  $('#addRow').on('click', () => {
    if (!assets.length) return alert("Hãy chọn tài sản trước!");
    addRow();
  });

  function addRow() {
    body.append(`
      <tr>
        <td>
          <select name="tai_san_id[]" class="form-select asset-select" required>
            ${assets.map(a => `<option value="${a.id}">${a.ten_tai_san} [${a.ma_tai_san}]</option>`).join('')}
          </select>
        </td>
        <td class="asset-info">
          <div class="asset-info-box">
            <span class="ts-ma">Mã: -</span>
            <span class="ts-user">SV: -</span>
            <span class="ts-slot">Slot: -</span>
          </div>
        </td>
        <td><textarea name="mo_ta[]" class="form-control" rows="2"></textarea></td>
        <td><input type="file" name="hinh_anh[]" class="form-control"></td>
        <td><button type="button" class="btn btn-danger btn-sm del">✖</button></td>
      </tr>
    `);

    $('.asset-select').last().trigger('change');
  }

  body.on('change', '.asset-select', function () {
    const id = $(this).val();
    const data = assets.find(a => a.id == id);

    const box = $(this).closest('tr').find('.asset-info-box');
    box.find('.ts-ma').text(`Mã: ${data?.ma_tai_san ?? '-'}`);
    box.find('.ts-user').text(`SV: ${data?.nguoi_su_dung ?? 'Chung'}`);
    box.find('.ts-slot').text(`Slot: ${data?.ma_slot ?? '-'}`);
  });

  body.on('click', '.del', function () {
    $(this).closest('tr').remove();
  });

  function loadLoai() {
    $.get(`/admin/lichbaotri/get-loai-tai-san`, d => {
      assets = [];
      $('#loai_tai_san_kho').html(`
        <option value="">-- Chọn loại tài sản --</option>
        ${d.map(i => `<option value="${i.id}">${i.ten_loai}</option>`).join('')}
      `);
    });
  }

  function loadPhong(id) {
    $.get(`/admin/lichbaotri/get-tai-san-phong/${id}`, d => {
      assets = d;
      body.html('');
    });
  }

  function loadKho(id) {
    $.get(`/admin/lichbaotri/get-tai-san-kho/${id}`, d => {
      assets = d;
      body.html('');
    });
  }
});
</script>
@endif

@endsection
