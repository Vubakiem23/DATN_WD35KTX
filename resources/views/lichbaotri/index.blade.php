@extends('admin.layouts.admin')

@section('title', 'Danh sách lịch bảo trì')

@section('content')
<div class="container mt-4">
  <h4 class="mb-3">🛠️ Danh sách lịch bảo trì</h4>

  {{-- 🟢 Thông báo --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <a href="{{ route('lichbaotri.create') }}" class="btn btn-success mb-3">➕ Lên lịch mới</a>

  <table class="table table-bordered table-striped align-middle table-hover">
    <thead class="table-light">
      <tr class="text-center">
        <th>#</th>
        <th>Ảnh minh chứng</th>
        <th>Tài sản</th>
        <th>Vị trí</th>
        <th>Ngày bảo trì</th>
        <th>Ngày hoàn thành</th>
        <th>Trạng thái</th>
        <th>Mô tả</th>
        <th style="width: 250px;">Hành động</th>
      </tr>
    </thead>

    <tbody>
      @forelse($lich as $index => $l)
      <tr class="{{ $l->trang_thai == 'Hoàn thành' ? 'table-success' : '' }}">
        {{-- STT --}}
        <td class="text-center">
          {{ ($lich instanceof \Illuminate\Pagination\LengthAwarePaginator) ? $lich->firstItem() + $index : $index + 1 }}
        </td>

        {{-- Ảnh minh chứng --}}
        <td class="text-center">
          @if($l->trang_thai == 'Hoàn thành')
              {{-- Ảnh sau bảo trì --}}
              @if($l->hinh_anh)
                  <img src="{{ asset('uploads/lichbaotri/'.$l->hinh_anh) }}" 
                       alt="Ảnh sau bảo trì"
                       style="width:70px;height:70px;object-fit:cover;border-radius:8px;border:1px solid #ddd;">
              @else
                  <div class="bg-light text-muted d-flex align-items-center justify-content-center border rounded"
                       style="width:70px;height:70px;">-</div>
              @endif
          @else
              {{-- Ảnh trước bảo trì --}}
              @if($l->hinh_anh_truoc)
                  <img src="{{ asset('uploads/lichbaotri/'.$l->hinh_anh_truoc) }}" 
                       alt="Ảnh trước bảo trì"
                       style="width:70px;height:70px;object-fit:cover;border-radius:8px;border:1px solid #ddd;">
              @else
                  <div class="bg-light text-muted d-flex align-items-center justify-content-center border rounded"
                       style="width:70px;height:70px;">-</div>
              @endif
          @endif
        </td>

        {{-- Tài sản và vị trí --}}
        <td>{{ $l->taiSan->ten_tai_san ?? $l->khoTaiSan->ten_tai_san ?? 'Không xác định' }}</td>
        <td>{{ $l->taiSan->phong->ten_phong ?? ($l->khoTaiSan ? 'Kho' : '-') }}</td>

        {{-- Ngày --}}
        <td class="text-center">{{ $l->ngay_bao_tri }}</td>
        <td class="text-center">{{ $l->ngay_hoan_thanh ?? '-' }}</td>

        {{-- Trạng thái --}}
        <td class="text-center">
          <span class="badge 
            @if($l->trang_thai == 'Hoàn thành') bg-success
            @elseif($l->trang_thai == 'Đang bảo trì') bg-warning text-dark
            @else bg-secondary @endif">
            {{ $l->trang_thai }}
          </span>
        </td>

        <td>{{ $l->mo_ta ?? '-' }}</td>

        {{-- Hành động --}}
        <td class="text-center">
          {{-- 👁️ Xem chi tiết --}}
          <button type="button"
                  class="btn btn-info btn-sm mb-1 text-white btn-xem"
                  data-bs-toggle="modal"
                  data-bs-target="#xemChiTietModal"
                  data-id="{{ $l->id }}">
             Xem
          </button>

          {{-- ✏️ Sửa --}}
          <a href="{{ route('lichbaotri.edit', $l->id) }}" class="btn btn-warning btn-sm mb-1"> Sửa</a>

          {{-- 🗑️ Xóa --}}
          <form action="{{ route('lichbaotri.destroy', $l->id) }}" method="POST" class="d-inline mb-1"
                onsubmit="return confirm('Bạn có chắc muốn xóa lịch này không?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm"> Xóa</button>
          </form>

       
          @if($l->trang_thai != 'Hoàn thành')
            <button type="button" 
                    class="btn btn-success btn-sm mb-1 btn-hoan-thanh"
                    data-bs-toggle="modal" 
                    data-bs-target="#hoanThanhModal" 
                    data-id="{{ $l->id }}">
               Hoàn thành
            </button>
          @endif
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="10" class="text-center text-muted">Không có lịch bảo trì nào</td>
      </tr>
      @endforelse
    </tbody>
  </table>

  {{-- Phân trang --}}
  <div class="d-flex justify-content-center mt-3">
    {{ $lich->appends(request()->query())->links('pagination::bootstrap-5') }}
  </div>
</div>

{{-- ✅ Modal Hoàn thành --}}
<div class="modal fade" id="hoanThanhModal" tabindex="-1" aria-labelledby="hoanThanhLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="hoanThanhLabel">✅ Cập nhật hoàn thành bảo trì</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <form id="hoanThanhForm" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <input type="hidden" name="id" id="lich_id">

          <div class="mb-3">
            <label for="ngay_hoan_thanh" class="form-label">Ngày hoàn thành</label>
            <input type="date" name="ngay_hoan_thanh" id="ngay_hoan_thanh" class="form-control" required>
          </div>

          <div class="mb-3">
            <label for="hinh_anh_sau" class="form-label">Ảnh sau bảo trì</label>
            <input type="file" name="hinh_anh_sau" id="hinh_anh_sau" class="form-control" accept="image/*">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
          <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- 👁️ Modal Xem Chi Tiết --}}
<div class="modal fade" id="xemChiTietModal" tabindex="-1" aria-labelledby="xemChiTietLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="xemChiTietLabel">👁️ Chi tiết bảo trì</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body" id="chiTietContent">
        <div class="text-center text-muted py-3">Đang tải dữ liệu...</div>
      </div>
    </div>
  </div>
</div>

{{-- 🧩 Script --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // 🟢 Modal Hoàn thành
    const modalHoanThanh = document.getElementById('hoanThanhModal');
    modalHoanThanh.addEventListener('show.bs.modal', function(event) {
      const button = event.relatedTarget;
      const id = button.getAttribute('data-id');
      const form = document.getElementById('hoanThanhForm');
      form.action = "{{ route('lichbaotri.hoanthanh.submit', ':id') }}".replace(':id', id);
      document.getElementById('lich_id').value = id;
    });

    // 🔵 Modal Xem chi tiết
    const xemModal = document.getElementById('xemChiTietModal');
    xemModal.addEventListener('show.bs.modal', function(event) {
      const button = event.relatedTarget;
      const id = button.getAttribute('data-id');
      const contentDiv = document.getElementById('chiTietContent');
      contentDiv.innerHTML = '<div class="text-center text-muted py-3">Đang tải dữ liệu...</div>';

      fetch(`/admin/lichbaotri/show/${id}`)
        .then(response => response.text())
        .then(html => contentDiv.innerHTML = html)
        .catch(() => contentDiv.innerHTML = '<div class="text-danger text-center">Lỗi tải dữ liệu</div>');
    });
  });
</script>
@endsection
