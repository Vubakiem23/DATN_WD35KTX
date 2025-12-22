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
    border-radius: 18px;
    box-shadow: 0 12px 32px rgba(15, 23, 42, 0.1);
  }

  .form-control,
  .form-select {
    border-radius: 12px;
    border: 1px solid #d1d5db;
    padding: 10px 14px;
    transition: 0.2s ease;
    font-size: 0.92rem;
  }

  .form-control:focus,
  .form-select:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.18);
  }

  .btn-primary {
    background: linear-gradient(135deg, #2563eb, #4f46e5);
    border: none;
    border-radius: 12px;
    padding: 10px 22px;
    font-weight: 600;
  }

  .btn-secondary {
    border-radius: 12px;
    padding: 10px 22px;
  }

  .section-title {
    font-weight: 600;
    color: #2563eb;
    border-left: 4px solid #2563eb;
    padding-left: 10px;
    margin-bottom: 18px;
  }

  .asset-info-box {
    background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
    border: 1px solid #c7d2fe;
    font-size: 12px;
    padding: 12px 14px;
    border-radius: 10px;
    line-height: 1.5;
    min-height: 90px;
  }

  .asset-info-box span {
    display: block;
    font-weight: 600;
    color: #1e40af;
    font-size: 13px;
    margin-bottom: 4px;
  }

  .asset-info-box small {
    display: block;
    color: #64748b;
    font-weight: 400;
    font-size: 11px;
    line-height: 1.6;
  }

  .asset-table-wrapper {
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  }

  .asset-table {
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    table-layout: fixed;
  }

  .asset-table thead th {
    background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
    font-size: 0.75rem;
    text-transform: uppercase;
    color: #fff;
    letter-spacing: .05em;
    border-bottom: none;
    padding: 14px 12px;
    font-weight: 600;
    text-align: center;
  }

  .asset-table thead th:first-child {
    border-radius: 16px 0 0 0;
  }

  .asset-table thead th:last-child {
    border-radius: 0 16px 0 0;
  }

  /* Cố định độ rộng các cột */
  .asset-table thead th:nth-child(1) { width: 22%; } /* Tài sản */
  .asset-table thead th:nth-child(2) { width: 23%; } /* Thông tin */
  .asset-table thead th:nth-child(3) { width: 25%; } /* Mô tả */
  .asset-table thead th:nth-child(4) { width: 22%; } /* Ảnh */
  .asset-table thead th:nth-child(5) { width: 8%; }  /* Xóa */

  .asset-table tbody tr {
    transition: all .2s ease;
    border-bottom: 1px solid #f1f5f9;
  }

  .asset-table tbody tr:hover {
    background: linear-gradient(90deg, #f0f9ff 0%, #fff 100%);
  }

  .asset-table tbody tr:last-child {
    border-bottom: none;
  }

  .asset-table tbody td {
    padding: 16px 12px;
    vertical-align: middle;
  }

  .asset-table tbody td .form-select {
    font-size: 12px;
    padding: 10px 12px;
    border-radius: 10px;
  }

  .asset-table textarea {
    min-height: 90px;
    resize: vertical;
    font-size: 12px;
    border-radius: 10px;
  }

  .asset-table input[type="file"] {
    font-size: 11px;
    padding: 8px 10px;
    border-radius: 10px;
  }

  .asset-table .del {
    border-radius: 10px;
    padding: 8px 14px;
    font-size: 14px;
    transition: all 0.2s ease;
  }

  .asset-table .del:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
  }

  .empty-state {
    text-align: center;
    background: linear-gradient(135deg, #fafbfc 0%, #f1f5f9 100%);
  }

  .empty-state td {
    padding: 40px 20px !important;
  }

  .empty-state .text-muted {
    color: #94a3b8 !important;
    font-size: 14px;
  }

  #addRow {
    border-radius: 12px;
    padding: 12px 28px;
    font-weight: 600;
    font-size: 14px;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    border: none;
    color: #fff;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
  }

  #addRow:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
    background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
  }

  @media (max-width: 992px) {
    .asset-table-wrapper {
      border: none;
      box-shadow: none;
    }

    .asset-table thead {
      display: none;
    }

    .asset-table tbody tr {
      display: block;
      margin-bottom: 18px;
      border-radius: 14px;
      border: 1px solid #e2e8f0;
      box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
      overflow: hidden;
    }

    .asset-table tbody td {
      display: flex;
      align-items: center;
      padding: 12px 16px;
      border-bottom: 1px solid #e2e8f0;
    }

    .asset-table tbody td::before {
      content: attr(data-title);
      flex: 0 0 120px;
      font-weight: 600;
      color: #475569;
      font-size: 0.85rem;
      margin-right: 12px;
    }

    .asset-table tbody td:last-child {
      border-bottom: none;
      justify-content: flex-end;
    }

    .asset-table textarea {
      width: 100%;
    }
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
    <div class="asset-table-wrapper mb-3">
      <div class="table-responsive">
        <table class="table asset-table align-middle" id="assetTable">
          <thead>
            <tr>
              <th>Tài sản</th>
              <th>Thông tin</th>
              <th>Mô tả</th>
              <th>Ảnh trước bảo trì</th>
              <th>#</th>
            </tr>
          </thead>
          <tbody>
            <tr class="empty-state">
              <td colspan="5">
                <div class="text-muted">
                  <i class="fa fa-inbox" style="font-size: 32px; margin-bottom: 10px; display: block; opacity: 0.5;"></i>
                  Chưa có tài sản nào trong danh sách.<br>
                  <small>Vui lòng chọn vị trí và nhấn <strong>+ Thêm tài sản</strong></small>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="text-center mb-4">
      <button type="button" id="addRow" class="btn">
        <i class="fa fa-plus me-2"></i> Thêm tài sản
      </button>
    </div>

    <h6 class="section-title">Ngày bảo trì</h6>
    <input type="date" name="ngay_bao_tri" class="form-control" required>

    <div class="text-end mt-3">
      <button type="submit" class="btn btn-primary me-2">💾 Lưu</button>
      <a href="{{ route('lichbaotri.index') }}" class="btn btn-secondary">↩️ Quay lại</a>
    </div>
  </form>
  @endif
</div>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

@if(!$taiSan)
<script>
  $(function() {
    const body = $('#assetTable tbody');
    let assets = [];

    $('#vi_tri').on('change', function() {
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

    function removeEmptyState() {
      body.find('.empty-state').remove();
    }

    function showEmptyState() {
      if (body.children().length) return;
      body.append(`
      <tr class="empty-state">
        <td colspan="5">
          <div class="text-muted">
            <i class="fa fa-inbox" style="font-size: 32px; margin-bottom: 10px; display: block; opacity: 0.5;"></i>
            Chưa có tài sản nào trong danh sách.<br>
            <small>Vui lòng chọn vị trí và nhấn <strong>+ Thêm tài sản</strong></small>
          </div>
        </td>
      </tr>
    `);
    }

    function addRow() {
      removeEmptyState();
      body.append(`
      <tr>
        <td data-title="Tài sản">
          <select name="tai_san_id[]" class="form-select asset-select" required>
            ${assets.map(a => `<option value="${a.id}">${a.ten_tai_san} [${a.ma_tai_san}]</option>`).join('')}
          </select>
        </td>
        <td data-title="Thông tin" class="asset-info">
          <div class="asset-info-box">
            <span class="ts-name">Tên tài sản</span>
            <small class="ts-ma"><i class="fa fa-barcode me-1"></i>Mã: -</small>
            <small class="ts-user"><i class="fa fa-user me-1"></i>SV sử dụng: -</small>
            <small class="ts-slot"><i class="fa fa-cube me-1"></i>Slot: -</small>
          </div>
        </td>
        <td data-title="Mô tả">
          <textarea name="mo_ta[]" class="form-control" rows="3" placeholder="Mô tả công việc cần bảo trì..."></textarea>
        </td>
        <td data-title="Ảnh trước bảo trì">
          <input type="file" name="hinh_anh[]" class="form-control" accept="image/*">
        </td>
        <td data-title="Thao tác" class="text-center">
          <button type="button" class="btn btn-danger btn-sm del"><i class="fa fa-times"></i></button>
        </td>
      </tr>
    `);

      $('.asset-select').last().trigger('change');
    }

    body.on('change', '.asset-select', function() {
      const id = $(this).val();
      const data = assets.find(a => a.id == id);

      const box = $(this).closest('tr').find('.asset-info-box');
      box.find('.ts-name').text(data?.ten_tai_san ?? 'Tên tài sản');
      box.find('.ts-ma').text(`Mã: ${data?.ma_tai_san ?? '-'}`);
      box.find('.ts-user').text(`Sinh viên sử dụng: ${data?.nguoi_su_dung ?? 'Chưa phân bổ'}`);
      box.find('.ts-slot').text(`Slot: ${data?.ma_slot ?? '-'}`);
    });

    body.on('click', '.del', function() {
      $(this).closest('tr').remove();
      showEmptyState();
    });

    function loadLoai() {
      $.get(`/admin/lichbaotri/get-loai-tai-san`, d => {
        assets = [];
        $('#loai_tai_san_kho').html(`
        <option value="">-- Chọn loại tài sản --</option>
        ${d.map(i => `<option value="${i.id}">${i.ten_loai}</option>`).join('')}
          `);
      });
      showEmptyState();
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
      showEmptyState();
    }
  });
</script>
@endif

@endsection