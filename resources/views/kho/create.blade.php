@extends('admin.layouts.admin')
@section('title', 'Thêm tài sản mới')
@section('content')

<style>
  .card {
    border-radius: 18px;
    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
    border: none;
  }

  .bulk-title {
    font-weight: 700;
    color: #1f2937;
  }

  .table-wrapper {
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    overflow: hidden;
  }

  .bulk-table {
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
  }

  .bulk-table thead th {
    background: #f8fafc;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: #64748b;
    font-size: .78rem;
    padding: 16px 14px;
    border-bottom: 1px solid #e2e8f0;
  }

  .bulk-table tbody td {
    padding: 14px;
    vertical-align: middle;
  }

  .bulk-table tbody tr:hover {
    background: #f9fbff;
  }

  .bulk-table input,
  .bulk-table select,
  .bulk-table textarea {
    border-radius: 12px;
    border: 1px solid #d1d5db;
    font-size: .92rem;
  }

  .bulk-table textarea {
    min-height: 70px;
    resize: vertical;
  }

  .img-preview {
    width: 64px;
    height: 64px;
    object-fit: cover;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
  }

  #addRow {
    border-radius: 999px;
    padding: 10px 22px;
    font-weight: 600;
  }

  .btn-primary {
    background: linear-gradient(135deg, #2563eb, #4f46e5);
    border: none;
    border-radius: 12px;
    padding: 10px 22px;
    font-weight: 600;
  }

  .btn-danger.btn-sm {
    border-radius: 10px;
    padding-inline: 16px;
  }

  .empty-state {
    text-align: center;
    color: #94a3b8;
    font-style: italic;
  }

  @media (max-width: 992px) {
    .table-wrapper {
      border: none;
      box-shadow: none;
    }

    .bulk-table thead {
      display: none;
    }

    .bulk-table tbody tr {
      display: block;
      margin-bottom: 20px;
      border: 1px solid #e2e8f0;
      border-radius: 16px;
      box-shadow: 0 10px 26px rgba(15, 23, 42, 0.12);
      overflow: hidden;
    }

    .bulk-table tbody td {
      display: flex;
      align-items: center;
      padding: 12px 16px;
      border-bottom: 1px solid #e2e8f0;
    }

    .bulk-table tbody td:last-child {
      border-bottom: none;
      justify-content: flex-end;
    }

    .bulk-table tbody td::before {
      content: attr(data-title);
      flex: 0 0 130px;
      font-weight: 600;
      color: #475569;
      font-size: .85rem;
      margin-right: 12px;
    }
  }
</style>

<div class="container mt-4">
  <h4 class="mb-3 bulk-title">➕ Thêm nhiều tài sản cho loại: {{ $loai->ten_loai }}</h4>

  <form action="{{ route('kho.store', $loai->id) }}" method="POST" enctype="multipart/form-data" class="card p-4">
    @csrf
    <div class="table-wrapper mb-4">
      <div class="table-responsive">
        <table class="table bulk-table align-middle" id="assetTable">
          <thead>
            <tr>
              <th style="min-width:180px;">Tên tài sản</th>
              <th style="min-width:140px;">Đơn vị</th>
              <th style="min-width:160px;">Tình trạng</th>
              <th style="min-width:220px;">Ghi chú</th>
              <th style="min-width:200px;">Hình ảnh</th>
              <th style="width:120px;">Xem trước</th>
              <th style="width:90px;">Thao tác</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>

    <button type="button" id="addRow" class="btn btn-outline-primary mb-3">➕ Thêm dòng</button>

    <div class="text-end">
      <button type="submit" class="btn btn-primary">💾 Lưu tất cả</button>
    </div>
  </form>
</div>

<script>
  const tbody = document.querySelector('#assetTable tbody');
  const addRowBtn = document.getElementById('addRow');
  const tenMacDinh = @json($loai->ten_loai);
  const tinhTrangOptions = @json($tinhTrangOptions);

  function buildOptions() {
    return ['<option value="">--Chọn--</option>', ...tinhTrangOptions.map(status => `<option value="${status}">${status}</option>`)].join('');
  }

  function removeEmptyState() {
    const placeholder = tbody.querySelector('.empty-state');
    if (placeholder) placeholder.remove();
  }

  function showEmptyState() {
    if (tbody.children.length) return;
    const row = document.createElement('tr');
    row.className = 'empty-state';
    row.innerHTML = `
      <td colspan="7" class="py-4">
        Chưa có dòng nào. Nhấn <strong>Thêm dòng</strong> để bổ sung tài sản.
      </td>
    `;
    tbody.appendChild(row);
  }

  function createRow() {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td data-title="Tên tài sản">
        <input type="text" name="ten_tai_san[]" class="form-control" value="${tenMacDinh}" readonly>
      </td>
      <td data-title="Đơn vị">
        <input type="text" name="don_vi_tinh[]" class="form-control" placeholder="chiếc, bộ...">
      </td>
      <td data-title="Tình trạng">
        <select name="tinh_trang[]" class="form-select">
          ${buildOptions()}
        </select>
      </td>
      <td data-title="Ghi chú">
        <textarea name="ghi_chu[]" class="form-control" rows="1" placeholder="Ghi chú thêm (không bắt buộc)"></textarea>
      </td>
      <td data-title="Hình ảnh">
        <input type="file" name="hinh_anh[]" class="form-control file-input" accept="image/*">
      </td>
      <td data-title="Xem trước" class="text-center">
        <img class="img-preview" alt="Xem trước">
      </td>
      <td data-title="Thao tác" class="text-center">
        <button type="button" class="btn btn-danger btn-sm remove-row" title="Xóa dòng">
          ✖
        </button>
      </td>
    `;
    return tr;
  }

  addRowBtn.addEventListener('click', () => {
    removeEmptyState();
    tbody.appendChild(createRow());
  });

  document.addEventListener('click', e => {
    if (e.target.closest('.remove-row')) {
      const row = e.target.closest('tr');
      row.remove();
      showEmptyState();
    }
  });

  document.addEventListener('change', e => {
    if (e.target.classList.contains('file-input')) {
      const file = e.target.files[0];
      const preview = e.target.closest('tr').querySelector('.img-preview');
      if (file) {
        const reader = new FileReader();
        reader.onload = evt => preview.src = evt.target.result;
        reader.readAsDataURL(file);
      } else {
        preview.src = '';
      }
    }
  });

  removeEmptyState();
  tbody.appendChild(createRow());
</script>

@endsection
