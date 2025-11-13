@extends('admin.layouts.admin')
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap 5 JS Bundle (gồm Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@section('content')
<div class="container py-4">

    <h2 class="mb-4">Danh sách hóa đơn</h2>
    <div class="row text-center mb-4">
  <div class="col-md-3">
    <div class="card bg-light shadow-sm">
      <div class="card-body">
        <h5 class="card-title" style="color: #0d47a1;">Tổng hóa đơn</h5>
        <p class="card-text fs-4" style="color: #0d47a1;">{{ $tongHoaDon }}</p>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card bg-light shadow-sm">
      <div class="card-body">
        <h5 class="card-title" style="color: #0d47a1;">Tổng tiền</h5>
        <p class="card-text fs-4" style="color: #0d47a1;">{{ number_format($tongTien, 0, ',', '.') }} ₫</p>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card bg-light shadow-sm">
      <div class="card-body">
        <h5 class="card-title" style="color: #0d47a1;" >Đã thanh toán</h5>
        <p class="card-text fs-5" style="color: #0d47a1;">{{ $tongDaThanhToan }} ({{ number_format($tienDaThanhToan, 0, ',', '.') }} ₫)</p>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card bg-light shadow-sm">
      <div class="card-body">
        <h5 class="card-title" style="color: #0d47a1;">Chưa thanh toán</h5>
        <p class="card-text fs-5" style="color: #0d47a1;">{{ $tongChuaThanhToan }} ({{ number_format($tienChuaThanhToan, 0, ',', '.') }} ₫)</p>
      </div>
    </div>
  </div>
</div>


    <style>
        .room-actions{display:flex;gap:.5rem;flex-wrap:wrap}
        .room-actions .btn-dergin{min-width:92px}
        .room-actions .btn-dergin span{line-height:1;white-space:nowrap}
        .btn-dergin{display:inline-flex;align-items:center;justify-content:center;gap:.35rem;padding:.4rem .9rem;border-radius:999px;font-weight:600;font-size:.72rem;border:none;color:#fff;background:linear-gradient(135deg,#4e54c8 0%,#8f94fb 100%);box-shadow:0 6px 16px rgba(78,84,200,.22);transition:transform .2s ease,box-shadow .2s ease;text-decoration:none}
        .btn-dergin:hover{transform:translateY(-1px);box-shadow:0 10px 22px rgba(78,84,200,.32);color:#fff}
        .btn-dergin i{font-size:.8rem}
        .btn-dergin--muted{background:linear-gradient(135deg,#4f46e5 0%,#6366f1 100%)}
        .btn-dergin--info{background:linear-gradient(135deg,#0ea5e9 0%,#2563eb 100%)}
        .btn-dergin--danger{background:linear-gradient(135deg,#f43f5e 0%,#ef4444 100%)}
    </style>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex flex-wrap align-items-center gap-3">
  {{-- Nhập từ Excel --}}
  <form action="{{ route('hoadon.import') }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-2">
    @csrf
    <input type="file" name="file" class="form-control" required style="width: auto;">
    <button type="submit" class="btn btn-dergin btn-dergin--info" title="Nhập Excel">
      <i class="fa fa-download"></i><span>Nhập Excel</span>
    </button>
  </form>

  {{-- Xuất Excel --}}
  <form method="GET" action="{{ route('hoadon.export') }}" class="d-flex align-items-center">
    <input type="hidden" name="trang_thai" value="{{ request('trang_thai') }}">
    <button type="submit" class="btn btn-dergin" title="Xuất Excel">
      <i class="fa fa-upload"></i><span>Xuất Excel</span>
    </button>
  </form>

  {{-- Lịch sử --}}
  <div class="d-flex align-items-center" style="margin-bottom: +15px;">
    <a href="{{ route('hoadon.lichsu') }}" class="btn btn-dergin btn-dergin--muted" title="Lịch sử">
      <i class="fa fa-history"></i><span>Lịch sử</span>
    </a>
  </div>

  {{-- Bộ lọc --}}
  <div class="d-flex align-items-center" style="margin-bottom: +15px;">
    <button type="button" class="btn btn-dergin btn-dergin--info" title="Bộ lọc" data-bs-toggle="modal" data-bs-target="#filterModal">
      <i class="fa fa-filter"></i><span>Lọc</span>
    </button>
  </div>

  {{-- Gửi email hàng loạt --}}
  <form method="POST" action="{{ route('hoadon.guiemailhangloat') }}" class="d-flex align-items-center">
    @csrf
    <button type="submit" class="btn btn-dergin btn-dergin--info" title="Gửi hóa đơn" onclick="return confirm('Gửi email cho tất cả sinh viên chưa thanh toán?')">
      <i class="fa fa-envelope"></i><span>Gửi hóa đơn</span>
    </button>
  </form>
</div>



 
      <div class="table-responsive">
      <table class="table table-hover mb-0 room-table text-center align-middle">
    <thead>
        <tr class="text-center" style="color: #1a1a1a;">
            <th>Khu</th>
            <th>Phòng</th>
            <th>Loại phòng</th>
            <th>Thành tiền</th>
            <th>Trạng thái</th>
            <th>🔧 Thao Tác</th>
        </tr>
    </thead>
    <tbody>
        @foreach($hoaDons as $hoaDon)
            <tr>
                <td style="color:#555555;">{{ optional($hoaDon->phong->khu)->ten_khu ?? 'Không rõ khu' }}</td>
                <td style="color:#555555;">{{ optional($hoaDon->phong)->ten_phong ?? 'Không rõ' }}</td>
                <td style="color:#555555;">{{ optional($hoaDon->phong)->loai_phong ?? 'Không rõ' }}</td>
                <td style="color:#555555;">{{ number_format($hoaDon->thanh_tien, 0, ',', '.') }}</td>
                <td>
                    @if($hoaDon->trang_thai === 'Đã thanh toán')
                        <div class="d-inline-flex align-items-center px-3 py-1 rounded-pill" style="background-color:#d4edda; color:#2e7d32;">
                            <i class="fa fa-check-circle me-2"></i> Đã thanh toán
                        </div>
                    @else
                        <div class="d-inline-flex align-items-center px-3 py-1 rounded-pill" style="background-color:#fff3cd; color:#d32f2f;">
                            <i class="fa fa-clock me-2"></i> Chưa thanh toán
                        </div>
                    @endif
                </td>
                <td class="text-center">
  <div class="dropdown position-relative">
    <button class="btn btn-circle bg-primary text-white" type="button"
        id="actionDropdown{{ $hoaDon->id }}"
        data-bs-toggle="dropdown"
        aria-expanded="false"
        title="Thao tác">
  <i class="fa fa-cog"></i>
</button>



    <ul class="dropdown-menu custom-dropdown" aria-labelledby="actionDropdown{{ $hoaDon->id }}">
      <li>
        <a class="dropdown-item d-flex align-items-center" href="{{ route('hoadon.show', $hoaDon->id) }}">
          👁️ <span class="ms-2">Xem chi tiết</span>
        </a>
      </li>
      <li>
        <a class="dropdown-item d-flex align-items-center" href="{{ route('hoadon.edit', $hoaDon->id) }}">
          ✏️ <span class="ms-2">Sửa</span>
        </a>
      </li>
      <li>
        <button type="button" class="dropdown-item d-flex align-items-center"
                data-bs-toggle="modal" data-bs-target="#paymentModal"
                data-id="{{ $hoaDon->id }}">
          📄 <span class="ms-2">Thanh toán</span>
        </button>
      </li>
      <li>
        <form action="{{ route('hoadon.destroy', $hoaDon->id) }}" method="POST"
              onsubmit="return confirm('Bạn có chắc muốn xóa hóa đơn này không?')">
          @csrf
          @method('DELETE')
          <button class="dropdown-item d-flex align-items-center text-danger" type="submit">
            🗑️ <span class="ms-2">Xóa</span>
          </button>
        </form>
      </li>
    </ul>
  </div>
</td>

            </tr>
        @endforeach
    </tbody>
</table>

<style>
.custom-dropdown {
  position: absolute !important;
  top: 50% !important;
  transform: translateY(-50%) !important;
  left : auto !important;
  right: 60% !important;
  min-width: 160px;
  z-index: 9999;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  background-color: #fff;
  display: none;
}

.dropdown.show .custom-dropdown {
  display: block !important;
}

.custom-dropdown .dropdown-item {
  padding: 8px 12px;
  font-size: 14px;
  white-space: nowrap;
}
</style>
<style>
/* Nút tròn xoe */
.btn-circle {
  width: 30px;
  height: 30px;
  padding: 0;
  border: none;
  border-radius: 50%;          /* bo tròn xoe */
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 15px;             /* kích thước icon */
  box-shadow: 0 2px 6px rgba(0,0,0,0.12);
  transition: transform 0.15s ease, background-color 0.2s ease, box-shadow 0.2s ease;
}

/* Hiệu ứng hover */
.btn-circle:hover {
  background-color: #0b5ed7;   /* đậm hơn bg-primary */
  box-shadow: 0 4px 12px rgba(0,0,0,0.16);
  transform: translateY(-1px);
}

/* Tùy chọn: icon xoay nhẹ khi mở dropdown */
.dropdown.show .btn-circle i {
  animation: spin 0.5s ease;
}
@keyframes spin {
  from { transform: rotate(0deg); }
  to   { transform: rotate(90deg); }
}
</style>





    

</div>


@push('styles')
                <style>
                    .room-table-wrapper {
                        background: #fff;
                        border-radius: 14px;
                        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
                        padding: 1.25rem;
                    }

                    .room-table {
                        margin-bottom: 0;
                        border-collapse: separate;
                        border-spacing: 0 12px;
                    }

                    .room-table thead th {
                        font-size: .78rem;
                        text-transform: uppercase;
                        letter-spacing: .05em;
                        color: #6c757d;
                        border: none;
                        padding-bottom: .75rem;
                    }

                    .room-table tbody tr {
                        background: #f9fafc;
                        border-radius: 16px;
                        transition: transform .2s ease, box-shadow .2s ease;
                    }

                    .room-table tbody tr:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
                    }

                    .room-table tbody td {
                        border: none;
                        vertical-align: middle;
                        padding: 1rem .95rem;
                    }

                    .room-table tbody tr td:first-child {
                        border-top-left-radius: 16px;
                        border-bottom-left-radius: 16px;
                    }

                    .room-table tbody tr td:last-child {
                        border-top-right-radius: 16px;
                        border-bottom-right-radius: 16px;
                    }

                    .room-actions {
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        gap: .4rem;
                        flex-wrap: wrap;
                    }

                    .room-actions form {
                        display: contents !important;
                        /* ✅ giúp nút trong flex vẫn submit được */
                    }

                    .btn-dergin {
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        gap: .35rem;
                        padding: .4rem .9rem;
                        border-radius: 999px;
                        font-weight: 600;
                        font-size: .72rem;
                        border: none;
                        color: #fff;
                        background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
                        box-shadow: 0 6px 16px rgba(78, 84, 200, .22);
                        transition: transform .2s ease, box-shadow .2s ease;
                    }

                    .btn-dergin:hover {
                        transform: translateY(-1px);
                        box-shadow: 0 10px 22px rgba(78, 84, 200, .32);
                        color: #fff;
                    }

                    .btn-dergin--muted {
                        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%)
                    }

                    .btn-dergin--info {
                        background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%)
                    }

                    .btn-dergin--success {
                        background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%)
                    }

                    .btn-dergin--danger {
                        background: linear-gradient(135deg, #f43f5e 0%, #ef4444 100%)
                    }

                    .badge-soft-success {
                        background: rgba(34, 197, 94, .15);
                        color: #16a34a;
                    }

                    .badge-soft-warning {
                        background: rgba(251, 191, 36, .15);
                        color: #ca8a04;
                    }

                    .badge-soft-secondary {
                        background: rgba(107, 114, 128, .15);
                        color: #374151;
                    }

                    .text-truncate {
                        overflow: hidden;
                        text-overflow: ellipsis;
                        white-space: nowrap;
                        display: block;
                    }
                </style>
            @endpush

            @push('styles')
                <style>
                    .room-table-wrapper {
                        background: #fff;
                        border-radius: 14px;
                        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
                        padding: 1.25rem;
                    }

                    .room-table {
                        margin-bottom: 0;
                        border-collapse: separate;
                        border-spacing: 0 12px;
                    }

                    .room-table thead th {
                        font-size: .78rem;
                        text-transform: uppercase;
                        letter-spacing: .05em;
                        color: #6c757d;
                        border: none;
                        padding-bottom: .75rem;
                    }

                    .room-table tbody tr {
                        background: #f9fafc;
                        border-radius: 16px;
                        transition: transform .2s ease, box-shadow .2s ease;
                    }

                    .room-table tbody tr:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
                    }

                    .room-table tbody td {
                        border: none;
                        vertical-align: middle;
                        padding: 1rem .95rem;
                    }

                    .room-table tbody tr td:first-child {
                        border-top-left-radius: 16px;
                        border-bottom-left-radius: 16px;
                    }

                    .room-table tbody tr td:last-child {
                        border-top-right-radius: 16px;
                        border-bottom-right-radius: 16px;
                    }

                    .room-actions {
                        display: flex;
                        flex-wrap: nowrap;
                        justify-content: center;
                        gap: .4rem;
                        white-space: nowrap;
                    }

                    .btn-dergin {
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        gap: .35rem;
                        padding: .4rem .9rem;
                        border-radius: 999px;
                        font-weight: 600;
                        font-size: .72rem;
                        border: none;
                        color: #fff;
                        background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
                        box-shadow: 0 6px 16px rgba(78, 84, 200, .22);
                        transition: transform .2s ease, box-shadow .2s ease;
                    }

                    .btn-dergin:hover {
                        transform: translateY(-1px);
                        box-shadow: 0 10px 22px rgba(78, 84, 200, .32);
                        color: #fff;
                    }

                    .btn-dergin--muted {
                        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%)
                    }

                    .btn-dergin--info {
                        background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%)
                    }

                    .btn-dergin--success {
                        background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%)
                    }

                    .btn-dergin--danger {
                        background: linear-gradient(135deg, #f43f5e 0%, #ef4444 100%)
                    }

                    .badge-soft-success {
                        background: rgba(34, 197, 94, .15);
                        color: #16a34a;
                    }

                    .badge-soft-warning {
                        background: rgba(251, 191, 36, .15);
                        color: #ca8a04;
                    }

                    .badge-soft-secondary {
                        background: rgba(107, 114, 128, .15);
                        color: #374151;
                    }
                </style>
            @endpush



{{-- xử lí thanh toán --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  const paymentMethodSelect = document.getElementById('paymentMethod');
  const bankInfo = document.getElementById('bankInfo');
  const confirmBtn = document.getElementById('confirmPaymentBtn');
  const paymentModal = document.getElementById('paymentModal');
  const quickUpdateModal = document.getElementById('quickUpdateModal');
  const quickUpdateBtn = document.getElementById('quickUpdateBtn');

  // Hiển thị thông tin chuyển khoản nếu chọn "chuyen_khoan"
  function toggleBankInfo() {
    const method = paymentMethodSelect?.value;
    if (bankInfo) {
      bankInfo.style.display = method === 'chuyen_khoan' ? 'block' : 'none';
    }
  }

  // Gắn sự kiện thay đổi hình thức
  if (paymentMethodSelect) {
    paymentMethodSelect.addEventListener('change', toggleBankInfo);
  }

  // Gắn ID hóa đơn vào nút khi mở modal
  if (paymentModal) {
    paymentModal.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      const hoaDonId = button?.getAttribute('data-id');
      if (confirmBtn && hoaDonId) {
        confirmBtn.setAttribute('data-id', hoaDonId);
      }
    });
  }

  // Điền dữ liệu vào modal sửa nhanh
  if (quickUpdateModal) {
    quickUpdateModal.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      const id = button?.getAttribute('data-id') || '';
      const url = button?.getAttribute('data-url') || '';
      const dien = button?.getAttribute('data-dien') || '';
      const nuoc = button?.getAttribute('data-nuoc') || '';
      quickUpdateModal.querySelector('input[name="don_gia_dien"]').value = dien;
      quickUpdateModal.querySelector('input[name="don_gia_nuoc"]').value = nuoc;
      if (quickUpdateBtn) {
        quickUpdateBtn.setAttribute('data-id', id);
        quickUpdateBtn.setAttribute('data-url', url);
      }
    });
  }

  // Gửi yêu cầu xác nhận thanh toán
  if (confirmBtn) {
    confirmBtn.addEventListener('click', function () {
      const hoaDonId = this?.getAttribute('data-id');
      const hinhThuc = paymentMethodSelect?.value || '';
      const ghiChu = document.querySelector('textarea[name="ghi_chu_thanh_toan"]')?.value || '';

      if (!hoaDonId || !hinhThuc) {
        alert('⚠️ Vui lòng chọn hình thức thanh toán!');
        return;
      }

      fetch(`/hoadon/thanhtoan/${hoaDonId}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          hinh_thuc_thanh_toan: hinhThuc,
          ghi_chu_thanh_toan: ghiChu
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert('✅ Thanh toán thành công!');
          const modalInstance = new bootstrap.Modal(paymentModal);
          modalInstance.hide();

          setTimeout(() => location.reload(), 500);
        } else {
          alert('❌ Có lỗi xảy ra!');
        }
      })
      .catch(err => {
        console.error('Lỗi gửi yêu cầu:', err);
        alert('❌ Không thể gửi yêu cầu. Vui lòng thử lại!');
      });
    });
  }

  // Gửi cập nhật nhanh giá điện/nước
  if (quickUpdateBtn) {
    quickUpdateBtn.addEventListener('click', function () {
      const url = this.getAttribute('data-url');
      const dien = quickUpdateModal.querySelector('input[name="don_gia_dien"]').value;
      const nuoc = quickUpdateModal.querySelector('input[name="don_gia_nuoc"]').value;
      if (!url) { alert('Không xác định được URL cập nhật.'); return; }
      fetch(url, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ don_gia_dien: dien, don_gia_nuoc: nuoc })
      })
      .then(r => r.json())
      .then(data => {
        if (data?.success) {
          alert('✅ Đã cập nhật giá điện/nước.');
          const modal = bootstrap.Modal.getInstance(quickUpdateModal);
          modal?.hide();
          setTimeout(() => location.reload(), 300);
        } else {
          alert('❌ Cập nhật thất bại.');
        }
      })
      .catch(() => alert('❌ Lỗi kết nối, vui lòng thử lại.'));
    });
  }
});
</script>








@endsection
<!-- CSRF token trong <head> -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="paymentModalLabel">Chọn phương thức thanh toán</h5>
        <button type="button" class="btn btn-light border-0 fs-5" data-bs-dismiss="modal" aria-label="Đóng">❌</button>
      </div>
      <div class="modal-body">
        <select id="paymentMethod" class="form-select">
          <option value="">-- Chọn hình thức --</option>
          <option value="tien_mat">Tiền mặt</option>
          <option value="chuyen_khoan">Chuyển khoản</option>
        </select>

        <div id="bankInfo" style="display: none; margin-top: 15px;">
          <div class="row">
            <div class="col-md-7">
              <p><strong>Tên tài khoản:</strong> Nguyễn Quang Thắng</p>
              <p><strong>Số tài khoản:</strong> T1209666</p>
              <p><strong>Ngân hàng thụ hưởng:</strong> Techcombank - Chi nhánh Hà Nội</p>
            </div>
            <div class="col-md-5 text-center">
              <img src="{{ asset('images/ma1qr.jpg') }}" alt="QR chuyển khoản" class="img-fluid rounded border" style="max-width: 100px;">
              <p class="mt-2 text-muted" style="font-size: 0.85rem;">Quét mã để chuyển khoản</p>
            </div>
          </div>
        </div>

        <div class="mb-3 mt-3">
          <label for="ghi_chu" class="form-label">Ghi chú thanh toán</label>
          <textarea name="ghi_chu_thanh_toan" class="form-control" rows="3" placeholder="Vui lòng nhập tên phòng-khu..." required></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-success" id="confirmPaymentBtn" data-id="">Xác nhận thanh toán</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal cập nhật nhanh giá điện/nước -->
<div class="modal fade" id="quickUpdateModal" tabindex="-1" aria-labelledby="quickUpdateLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="quickUpdateLabel">Sửa nhanh giá điện / nước</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Đơn giá điện (đ/kWh)</label>
            <input type="number" name="don_gia_dien" class="form-control" min="0" step="100">
          </div>
          <div class="col-md-6">
            <label class="form-label">Đơn giá nước (đ/m³)</label>
            <input type="number" name="don_gia_nuoc" class="form-control" min="0" step="100">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
        <button type="button" id="quickUpdateBtn" class="btn btn-success">Lưu</button>
      </div>
    </div>
  </div>
</div>

{{-- modal bộ lọc --}}
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="filterModalLabel">Bộ lọc hóa đơn</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body">
        <form method="GET" action="{{ route('hoadon.index') }}" class="d-flex flex-wrap gap-3 align-items-end">
          <div>
            <label for="from_date" class="form-label">Từ ngày</label>
            <input type="date" name="from_date" id="from_date" class="form-control" value="{{ request('from_date') }}">
          </div>
          <div>
            <label for="to_date" class="form-label">Đến ngày</label>
            <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request('to_date') }}">
          </div>
          <div>
            <label for="gia_phong_min" class="form-label">Giá phòng từ</label>
            <input type="number" name="gia_phong_min" id="gia_phong_min" class="form-control" value="{{ request('gia_phong_min') }}" placeholder="VD: 1000000">
          </div>
          <div>
            <label for="gia_phong_max" class="form-label">Giá phòng đến</label>
            <input type="number" name="gia_phong_max" id="gia_phong_max" class="form-control" value="{{ request('gia_phong_max') }}" placeholder="VD: 2000000">
          </div>
          <div>
            <label for="khu" class="form-label">Khu</label>
            <select name="khu" id="khu" class="form-select">
              <option value="">-- Tất cả --</option>
              @foreach(['A','B','C','D','E','F'] as $khu)
                <option value="{{ $khu }}" {{ request('khu') == $khu ? 'selected' : '' }}>Khu {{ $khu }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label for="phong_id" class="form-label">Phòng</label>
            <select name="phong_id" id="phong_id" class="form-select">
              <option value="">-- Tất cả --</option>
              @foreach($dsPhongs as $phong)
                <option value="{{ $phong->id }}" {{ request('phong_id') == $phong->id ? 'selected' : '' }}>
                  {{ $phong->ten_phong }} (ID: {{ $phong->id }})
                </option>
              @endforeach
            </select>
          </div>
          <div>
            <label for="trang_thai" class="form-label">Trạng thái</label>
            <select name="trang_thai" id="trang_thai" class="form-select">
              <option value="">-- Tất cả --</option>
              <option value="da_thanh_toan" {{ request('trang_thai') == 'da_thanh_toan' ? 'selected' : '' }}>Đã thanh toán</option>
              <option value="chua_thanh_toan" {{ request('trang_thai') == 'chua_thanh_toan' ? 'selected' : '' }}>Chưa thanh toán</option>
            </select>
          </div>
          <div class="mt-4">
            <button type="submit" class="btn btn-primary">Lọc</button>
            <a href="{{ route('hoadon.index') }}" class="btn btn-secondary ms-2">Đặt lại</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
