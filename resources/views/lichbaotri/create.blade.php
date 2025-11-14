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
    background: #f1f5f9;
    border: 1px solid #cbd5f5;
    font-size: 13px;
    padding: 10px 12px;
    border-radius: 12px;
    line-height: 1.4;
  }

  .asset-info-box span {
    display: block;
    font-weight: 500;
    color: #1f2937;
  }

  .asset-info-box small {
    display: block;
    color: #64748b;
    font-weight: 400;
  }

  .asset-table-wrapper {
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    overflow: hidden;
    background: #fff;
  }

  .asset-table {
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
  }

  .asset-table thead th {
    background: #f8fafc;
    font-size: 0.78rem;
    text-transform: uppercase;
    color: #64748b;
    letter-spacing: .03em;
    border-bottom: 1px solid #e2e8f0;
    padding: 16px 14px;
  }

  .asset-table tbody tr {
    transition: background .2s ease;
  }

  .asset-table tbody tr:hover {
    background: #f9fbff;
  }

  .asset-table tbody td {
    padding: 14px 14px;
    vertical-align: middle;
  }

  .asset-table textarea {
    min-height: 80px;
    resize: vertical;
  }

  .asset-table .del {
    border-radius: 12px;
    padding-inline: 14px;
  }

  .empty-state {
    text-align: center;
  }

  #addRow {
    border-radius: 999px;
    padding: 10px 22px;
    font-weight: 600;
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
              <th style="min-width:170px;">Tài sản</th>
              <th style="min-width:210px;">Thông tin</th>
              <th style="min-width:220px;">Mô tả</th>
              <th style="min-width:180px;">Ảnh trước bảo trì</th>
              <th style="width:80px;">#</th>
            </tr>
          </thead>
          <tbody>
            <tr class="empty-state">
              <td colspan="5" class="py-4">
                <div class="text-muted">
                  Chưa có tài sản nào trong danh sách. Vui lòng chọn vị trí và nhấn <strong>Thêm tài sản</strong>.
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <button type="button" id="addRow" class="btn btn-outline-primary mb-4">
      ➕ Thêm tài sản
    </button>

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
        <td colspan="5" class="py-4 text-muted">
          Chưa có tài sản nào trong danh sách. Vui lòng chọn vị trí và nhấn <strong>Thêm tài sản</strong>.
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
            <small class="ts-ma">Mã: -</small>
            <small class="ts-user">Sinh viên sử dụng: -</small>
            <small class="ts-slot">Slot: -</small>
          </div>
        </td>
        <td data-title="Mô tả">
          <textarea name="mo_ta[]" class="form-control" rows="2" placeholder="Mô tả ngắn gọn công việc cần bảo trì..."></textarea>
        </td>
        <td data-title="Ảnh trước bảo trì">
          <input type="file" name="hinh_anh[]" class="form-control" accept="image/*">
        </td>
        <td data-title="Thao tác" class="text-center">
          <button type="button" class="btn btn-danger btn-sm del">✖</button>
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