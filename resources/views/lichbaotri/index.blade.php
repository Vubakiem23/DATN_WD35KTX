@extends('admin.layouts.admin')

@section('title', 'Danh sách lịch bảo trì')

@section('content')
<div class="container mt-4">
  @push('styles')
  <style>
    .lich-actions .btn-action {
      width: 40px;
      height: 36px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 10px;
      font-size: 14px;
      margin-right: 3px;
    }

    /* 🎨 Form lọc */
    .filter-card {
      background: #f8f9fa;
      border: 1px solid #ddd;
      border-radius: 12px;
      padding: 15px 20px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }

    .filter-card label {
      font-weight: 600;
      color: #333;
    }

    .filter-btns .btn {
      height: 42px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .filter-btns i {
      margin-right: 5px;
    }
  </style>
  @endpush

  <h4 class="mb-3">🛠️ Danh sách lịch bảo trì</h4>

  {{-- 🟢 Thông báo --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  {{-- 🎯 Bộ lọc --}}
  <div class="filter-card mb-4">
    <form method="GET" action="{{ route('lichbaotri.index') }}" class="row g-3 align-items-end">
      <div class="col-md-3">
        <label class="form-label"><i class="fa fa-circle-check text-primary"></i> Trạng thái</label>
        <select name="trang_thai" class="form-select form-control">
          <option value="">-- Tất cả --</option>
          <option value="Chờ bảo trì" {{ request('trang_thai') == 'Chờ bảo trì' ? 'selected' : '' }}>Chờ bảo trì</option>
          <option value="Đang bảo trì" {{ request('trang_thai') == 'Đang bảo trì' ? 'selected' : '' }}>Đang bảo trì</option>
          <option value="Hoàn thành" {{ request('trang_thai') == 'Hoàn thành' ? 'selected' : '' }}>Hoàn thành</option>
        </select>
      </div>

      <div class="col-md-3">
        <label class="form-label"><i class="fa fa-calendar text-primary"></i> Ngày bảo trì</label>
        <input type="date" name="ngay_bao_tri" value="{{ request('ngay_bao_tri') }}" class="form-control">
      </div>

      <div class="col-md-3">
        <label class="form-label"><i class="fa fa-location-dot text-primary"></i> Vị trí</label>
        <select name="vi_tri" class="form-select form-control">
          <option value="">-- Tất cả --</option>
          <option value="phong" {{ request('vi_tri') == 'phong' ? 'selected' : '' }}>Phòng</option>
          <option value="kho" {{ request('vi_tri') == 'kho' ? 'selected' : '' }}>Kho</option>
        </select>
      </div>

      <div class="col-md-3 d-flex gap-2 filter-btns">
        <button type="submit" class="btn btn-success flex-fill">
          <i class="fa fa-filter"></i> Lọc
        </button>
        <a href="{{ route('lichbaotri.index') }}" class="btn btn-outline-secondary flex-fill">
          <i class="fa fa-rotate-left"></i> Đặt lại
        </a>
      </div>
    </form>
  </div>

  {{-- ➕ Nút thêm mới --}}
  <a href="{{ route('lichbaotri.create') }}" class="btn btn-primary mb-3">
    <i class="fa fa-plus-circle"></i> Lên lịch mới
  </a>

  {{-- 🧾 Bảng danh sách --}}
  <div class="card">
    <div class="card-body p-0">
      <table class="table mb-0 align-middle table-striped table-hover">
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
            <th class="text-end" style="width: 200px;">Hành động</th>
          </tr>
        </thead>

        <tbody>
          @forelse($lich as $index => $l)
          <tr class="{{ $l->trang_thai == 'Hoàn thành' ? 'table-success' : '' }}">
            <td class="text-center">
              {{ ($lich instanceof \Illuminate\Pagination\LengthAwarePaginator) ? $lich->firstItem() + $index : $index + 1 }}
            </td>

            {{-- Ảnh minh chứng --}}
            <td class="text-center">
              @if($l->trang_thai == 'Hoàn thành')
                  @if($l->hinh_anh)
                      <img src="{{ asset('uploads/lichbaotri/'.$l->hinh_anh) }}" 
                           alt="Ảnh sau bảo trì"
                           style="width:70px;height:70px;object-fit:cover;border-radius:8px;border:1px solid #ddd;">
                  @else
                      <div class="bg-light text-muted d-flex align-items-center justify-content-center border rounded"
                           style="width:70px;height:70px;">-</div>
                  @endif
              @else
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

            <td>{{ $l->taiSan->ten_tai_san ?? $l->khoTaiSan->ten_tai_san ?? 'Không xác định' }}</td>
            <td>{{ $l->taiSan->phong->ten_phong ?? ($l->khoTaiSan ? 'Kho' : '-') }}</td>
            <td class="text-center">{{ $l->ngay_bao_tri }}</td>
            <td class="text-center">{{ $l->ngay_hoan_thanh ?? '-' }}</td>

            <td class="text-center">
              <span class="badge 
                @if($l->trang_thai == 'Hoàn thành') bg-success
                @elseif($l->trang_thai == 'Đang bảo trì') bg-warning text-dark
                @else bg-secondary @endif">
                {{ $l->trang_thai }}
              </span>
            </td>

            <td>{{ $l->mo_ta ?? '-' }}</td>

            {{-- 🔧 Hành động --}}
            <td class="text-end lich-actions">
              <button type="button"
                      class="btn btn-outline-info btn-action"
                      title="Xem chi tiết"
                      data-bs-toggle="modal"
                      data-bs-target="#xemChiTietModal"
                      data-id="{{ $l->id }}">
                <i class="fa fa-eye"></i>
              </button>

              <a href="{{ route('lichbaotri.edit', $l->id) }}" 
                 class="btn btn-outline-primary btn-action"
                 title="Sửa">
                <i class="fa fa-pencil"></i>
              </a>

              <form action="{{ route('lichbaotri.destroy', $l->id) }}" 
                    method="POST" 
                    class="d-inline"
                    onsubmit="return confirm('Bạn có chắc muốn xóa lịch này không?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-action" title="Xóa">
                  <i class="fa fa-trash"></i>
                </button>
              </form>

              @if($l->trang_thai != 'Hoàn thành')
              <button type="button"
                      class="btn btn-outline-success btn-action"
                      title="Hoàn thành"
                      data-bs-toggle="modal" 
                      data-bs-target="#hoanThanhModal" 
                      data-id="{{ $l->id }}">
                <i class="fa fa-check"></i>
              </button>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="10" class="text-center text-muted p-4">Không có lịch bảo trì nào</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

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

      fetch(`/lichbaotri/show/${id}`)
        .then(response => response.text())
        .then(html => contentDiv.innerHTML = html)
        .catch(() => contentDiv.innerHTML = '<div class="text-danger text-center">Lỗi tải dữ liệu</div>');
    });
  });
</script>
@endsection
