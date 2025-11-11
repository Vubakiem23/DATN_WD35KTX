@extends('admin.layouts.admin')
@section('title', 'Thêm tài sản mới')
@section('content')

<style>
  .card {
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  }
  table input, table select, table textarea {
    border-radius: 8px;
  }
  .img-preview {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 6px;
  }
</style>

<div class="container mt-4">
  <h4 class="mb-3">➕ Thêm nhiều tài sản cho loại: {{ $loai->ten_loai }}</h4>

  <form action="{{ route('kho.store', $loai->id) }}" method="POST" enctype="multipart/form-data" class="card p-4">
    @csrf

    <table class="table table-bordered align-middle" id="assetTable">
      <thead class="table-light">
        <tr>
          <th>Tên tài sản</th>
          <th>Đơn vị</th>
          <th>Tình trạng</th>
          <th>Ghi chú</th>
          <th>Hình ảnh</th>
          <th>Xem trước</th>
          <th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><input type="text" name="ten_tai_san[]" class="form-control" value="{{ $loai->ten_loai }}" readonly></td>
          <td><input type="text" name="don_vi_tinh[]" class="form-control" placeholder="chiếc, bộ..."></td>
          <td>
            <select name="tinh_trang[]" class="form-select ">
              <option value="">--Chọn--</option>
              @foreach($tinhTrangOptions as $status)
                <option value="{{ $status }}">{{ $status }}</option>
              @endforeach
            </select>
          </td>
          <td><textarea name="ghi_chu[]" class="form-control" rows="1"></textarea></td>
          <td><input type="file" name="hinh_anh[]" class="form-control file-input" accept="image/*"></td>
          <td class="text-center"><img class="img-preview" /></td>
          <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm remove-row">🗑️</button>
          </td>
        </tr>
      </tbody>
    </table>

    <button type="button" id="addRow" class="btn btn-outline-primary mb-3">➕ Thêm dòng</button>

    <div class="text-end">
      <button type="submit" class="btn btn-primary">💾 Lưu tất cả</button>
    </div>
  </form>
</div>

<script>
  // Thêm dòng
  document.getElementById('addRow').addEventListener('click', function () {
    const tableBody = document.querySelector('#assetTable tbody');
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
      <td><input type="text" name="ten_tai_san[]" class="form-control" value="{{ $loai->ten_loai }}" readonly></td>
      <td><input type="text" name="don_vi_tinh[]" class="form-control" placeholder="chiếc, bộ..."></td>
      <td>
        <select name="tinh_trang[]" class="form-select">
          <option value="">--Chọn--</option>
          @foreach($tinhTrangOptions as $status)
            <option value="{{ $status }}">{{ $status }}</option>
          @endforeach
        </select>
      </td>
      <td><textarea name="ghi_chu[]" class="form-control" rows="1"></textarea></td>
      <td><input type="file" name="hinh_anh[]" class="form-control file-input" accept="image/*"></td>
      <td class="text-center"><img class="img-preview" /></td>
      <td class="text-center">
        <button type="button" class="btn btn-danger btn-sm remove-row">🗑️</button>
      </td>
    `;
    tableBody.appendChild(newRow);
  });

  // Xóa dòng
  document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-row')) {
      e.target.closest('tr').remove();
    }
  });

  // Xem trước ảnh
  document.addEventListener('change', function (e) {
    if (e.target.classList.contains('file-input')) {
      const file = e.target.files[0];
      const preview = e.target.closest('tr').querySelector('.img-preview');
      if (file) {
        const reader = new FileReader();
        reader.onload = (evt) => preview.src = evt.target.result;
        reader.readAsDataURL(file);
      } else {
        preview.src = "";
      }
    }
  });
</script>

@endsection
