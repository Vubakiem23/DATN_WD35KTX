@extends('admin.layouts.admin')
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap 5 JS Bundle (gồm Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



@section('content')

<div class="container-fluid py-4">

  <div class="mb-4">
    <h3 class="page-title mb-1"><i class="fa fa-bed me-2"></i> Quản lý tiền phòng</h3>
    <p class="text-muted mb-0">Theo dõi giá trị tiền phòng theo từng phòng và slot.</p>
  </div>
    <style>
      .room-actions {
        display: flex;
        gap: .5rem;
        flex-wrap: wrap
      }

      .room-actions .btn-dergin {
        min-width: 92px
      }

      .room-actions .btn-dergin span {
        line-height: 1;
        white-space: nowrap
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
        text-decoration: none
      }

      .btn-dergin:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(78, 84, 200, .32);
        color: #fff
      }

      .btn-dergin i {
        font-size: .8rem
      }

      .btn-dergin--muted {
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%)
      }

      .btn-dergin--info {
        background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%)
      }

      .btn-dergin--danger {
        background: linear-gradient(135deg, #f43f5e 0%, #ef4444 100%)
      }

      .page-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937
      }

      /* Thanh công cụ phía trên bảng (Chọn tệp + các nút) */
      .admin-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.75rem;
        margin-top: 1rem;
        margin-bottom: 0.75rem;
      }

      .admin-toolbar form {
        margin: 0;
      }

      .admin-toolbar input[type="file"] {
        max-width: 260px;
      }

      @media (max-width: 768px) {
        .admin-toolbar {
          align-items: stretch;
        }

        .admin-toolbar input[type="file"] {
          max-width: 100%;
        }
      }
    </style>


    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="admin-toolbar">
      {{-- Nhập từ Excel --}}
      <form action="{{ route('hoadon.import') }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-2">
        @csrf
        <input type="hidden" name="invoice_type" value="{{ \App\Models\HoaDon::LOAI_TIEN_PHONG }}">
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
      <a href="{{ route('hoadon.lichsu_tienphong') }}" class="btn btn-dergin btn-dergin--muted" title="Lịch sử tiền phòng">
        <i class="fa fa-bed"></i><span>Lịch Sử</span>
      </a>

      {{-- Bộ lọc (nút lớn giống các nút khác) --}}
      <button type="button"
              class="btn btn-dergin btn-dergin--info"
              id="openFilterModalBtn"
              title="Bộ lọc">
        <i class="fa fa-filter mr-1"></i><span>Bộ lọc</span>
      </button>

      {{-- Gửi email hàng loạt --}}
      <form method="POST" action="{{ route('hoadon.guiemailhangloat') }}" class="d-flex align-items-center">
        @csrf
        <button type="submit" class="btn btn-dergin btn-dergin--info" title="Gửi hóa đơn" onclick="return confirm('Gửi email cho tất cả sinh viên chưa thanh toán?')">
          <i class="fa fa-envelope"></i><span>Gửi hóa đơn</span>
        </button>
      </form>
    </div>



    <div class="room-table-wrapper">
      <div class="table-responsive">
        <table class="table table-hover mb-0 room-table text-center align-middle">
          <thead>
            <tr class="text-center" style="color: #1a1a1a;">
              <th>Khu</th>
              <th>Phòng</th>
              <th>Loại phòng</th>
              <th>Slot tính phí</th>
              <th>Đơn giá/slot</th>
              <th>Tiền phòng</th>
              <th>Trạng thái</th>
              <th>Thao Tác</th>
            </tr>
          </thead>
          <tbody>
            @foreach($hoaDons as $hoaDon)
            <tr>
              <td style="color:#555555;">
                {{ optional($hoaDon->phong->khu)->ten_khu ?? 'Không rõ khu' }}
              </td>
              <td style="color:#555555;">
                {{ optional($hoaDon->phong)->ten_phong ?? 'Không rõ' }}
              </td>
              <td style="color:#555555;">
                {{ optional($hoaDon->phong)->loai_phong ?? 'Không rõ' }}
              </td>
              <td style="color:#555555;">
                {{ $hoaDon->slot_billing_count ?? 0 }}
              </td>
              <td style="color:#555555;">
                {{ number_format($hoaDon->slot_unit_price ?? 0, 0, ',', '.') }} VND
              </td>
              <td style="color:#555555;" class="fw-semibold">
                {{ number_format($hoaDon->tien_phong_slot ?? 0, 0, ',', '.') }} VND
              </td>
              <td>
                <div class="text-uppercase small text-muted fw-semibold mb-1">Tiền phòng</div>
                @php
                  $soSlotDaThanhToan = $hoaDon->so_slot_da_thanh_toan ?? 0;
                  $tongSoSlot = $hoaDon->tong_so_slot ?? 0;
                  $isFullyPaid = $hoaDon->da_thanh_toan ?? false;
                @endphp
                <div class="d-inline-flex align-items-center px-3 py-1 rounded-pill"
                  style="background-color: {{ $isFullyPaid ? '#d4edda' : '#fff3cd' }}; color: {{ $isFullyPaid ? '#2e7d32' : '#d32f2f' }};">
                  <i class="fa {{ $isFullyPaid ? 'fa-check-circle me-2' : 'fa-clock me-2' }}"></i>
                  @if($tongSoSlot > 0)
                    {{ $soSlotDaThanhToan }}/{{ $hoaDon->slot_billing_count  }} slots đã thanh toán
                  @else
                    {{ $isFullyPaid ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                  @endif
                </div>
                <div class="mt-2">
                  @if($hoaDon->sent_to_client)
                  <span class="badge bg-success-subtle text-success border border-success-subtle">
                    <i class="fa fa-paper-plane me-1"></i> Đã gửi sinh viên
                  </span>
                  @else
                  <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                    <i class="fa fa-hourglass-half me-1"></i> Chưa gửi sinh viên
                  </span>
                  @endif
                </div>
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

                  <ul class="dropdown-menu custom-dropdown"
                    aria-labelledby="actionDropdown{{ $hoaDon->id }}">
                    <li>
                      <a class="dropdown-item d-flex align-items-center"
                        href="{{ route('hoadon.show', ['id' => $hoaDon->id, 'view' => 'phong']) }}">
                        🛏️ <span class="ms-2">Chi tiết tiền phòng</span>
                      </a>
                    </li>
                    <li>
                      @if(!$hoaDon->sent_to_client)
                      <form method="POST"
                        action="{{ route('hoadon.sendToClient', $hoaDon->id) }}"
                        onsubmit="return confirm('Gửi hóa đơn TIỀN PHÒNG tới sinh viên phòng {{ optional($hoaDon->phong)->ten_phong }}?')">
                        @csrf
                        <input type="hidden" name="type" value="tien-phong">
                        <button class="dropdown-item d-flex align-items-center" type="submit">
                          🛏️ <span class="ms-2">Gửi tiền phòng</span>
                        </button>
                      </form>
                      @else
                      <span class="dropdown-item text-success d-flex align-items-center">
                        ✅ <span class="ms-2">Đã gửi tiền phòng</span>
                      </span>
                      @endif
                    </li>

                    <li>
                      <form action="{{ route('hoadon.destroy', $hoaDon->id) }}"
                        method="POST"
                        onsubmit="return confirm('Bạn có chắc muốn xóa hóa đơn này không?')">
                        @csrf
                        @method('DELETE')
                        <button class="dropdown-item d-flex align-items-center text-danger"
                          type="submit">
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
      </div>
    </div>
  </div>

  {{-- CSS tuỳ chỉnh --}}

  @push('styles')
  <style>
    /* Ẩn footer */
    footer {
      display: none !important;
    }

    /* Tiêu đề */
    .page-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: #1f2937
    }

    /* Bảng */
    .room-table-wrapper {
      background: #fff;
      border-radius: 14px;
      box-shadow: 0 10px 30px rgba(15, 23, 42, .06);
      padding: 1.25rem
    }

    .room-table {
      margin-bottom: 0;
      border-collapse: separate;
      border-spacing: 0 12px
    }

    .room-table thead th {
      font-size: .78rem;
      text-transform: uppercase;
      letter-spacing: .05em;
      color: #6c757d;
      border: none;
      padding-bottom: .75rem
    }

    /* Tránh dropdown bị che */
    .room-table-wrapper,
    .room-table-wrapper .table-responsive,
    .room-table tbody tr,
    .room-table tbody td,
    .action-cell {
      overflow: visible !important;
    }

    .room-table-wrapper .table-responsive {
      overflow: visible !important;
    }

    .room-table tbody tr {
      background: #f9fafc;
      border-radius: 16px;
      transition: transform .2s ease, box-shadow .2s ease;
      position: relative;
      z-index: 1;
    }

    .room-table tbody tr.active-menu {
      z-index: 10;
    }

    .room-table tbody tr:hover {
      /* transform: translateY(-2px); */
      box-shadow: 0 12px 30px rgba(15, 23, 42, .08)
    }

    .room-table tbody td {
      border: none;
      vertical-align: middle;
      padding: 1rem .95rem
    }

    .room-table tbody tr td:first-child {
      border-top-left-radius: 16px;
      border-bottom-left-radius: 16px
    }

    .room-table tbody tr td:last-child {
      border-top-right-radius: 16px;
      border-bottom-right-radius: 16px
    }

    /* Tránh dropdown bị cắt */
    .room-table-wrapper,
    .room-table-wrapper .table-responsive,
    .room-table tbody tr,
    .room-table tbody td,
    .action-cell {
      overflow: visible !important;
    }

    .room-table tbody tr {
      position: relative;
      z-index: 1;
    }

    .room-table tbody tr.active-menu {
      z-index: 9999;
    }

    /* Nút thao tác */
    .action-menu .dropdown-menu,
    .custom-dropdown {
      z-index: 5000;
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
      text-decoration: none
    }

    .btn-dergin:hover {
      transform: translateY(-1px);
      box-shadow: 0 10px 22px rgba(78, 84, 200, .32);
      color: #fff
    }

    .btn-dergin i {
      font-size: .8rem
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

    /* Dropdown + nút tròn */
    .custom-dropdown {
      position: absolute !important;
      top: 100% !important;
      left: auto !important;
      right: 0 !important;
      transform: none !important;
      margin-top: 6px;
      min-width: 180px;
      z-index: 5000;
      border-radius: 10px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
      background-color: #fff;
      display: none;
      overflow: visible;
    }

    .dropdown.show .custom-dropdown {
      display: block !important;
    }

    .custom-dropdown .dropdown-item {
      padding: 8px 12px;
      font-size: 14px;
      white-space: nowrap
    }

    .btn-circle {
      width: 30px;
      height: 30px;
      padding: 0;
      border: none;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 15px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
      transition: transform 0.15s ease, background-color 0.2s ease, box-shadow 0.2s ease
    }

    .btn-circle:hover {
      background-color: #0b5ed7;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.16);
      transform: translateY(-1px)
    }

    .dropdown.show .btn-circle i {
      animation: spin 0.5s ease
    }

    @keyframes spin {
      from {
        transform: rotate(0deg)
      }

      to {
        transform: rotate(90deg)
      }
    }

    /* Badge */
    .badge-soft-success {
      background: rgba(34, 197, 94, .15);
      color: #16a34a
    }

    .badge-soft-warning {
      background: rgba(251, 191, 36, .15);
      color: #ca8a04
    }

    .badge-soft-secondary {
      background: rgba(107, 114, 128, .15);
      color: #374151
    }

    .text-truncate {
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      display: block
    }
  </style>
  @endpush

  {{-- xử lí thanh toán --}}
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Giữ dropdown không bị che: nâng z-index của hàng khi mở dropdown
      document.querySelectorAll('.dropdown').forEach(function(dd) {
        dd.addEventListener('show.bs.dropdown', function() {
          const row = dd.closest('tr');
          if (row) row.classList.add('active-menu');
        });
        dd.addEventListener('hide.bs.dropdown', function() {
          const row = dd.closest('tr');
          if (row) row.classList.remove('active-menu');
        });
      });

      const paymentMethodSelect = document.getElementById('paymentMethod');
      const invoiceTypeSelect = document.getElementById('invoiceType');
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
        paymentModal.addEventListener('show.bs.modal', function(event) {
          const button = event.relatedTarget;
          const hoaDonId = button?.getAttribute('data-id');
          const invoiceType = button?.getAttribute('data-invoice-type') || 'tien-phong';
          if (confirmBtn && hoaDonId) {
            confirmBtn.setAttribute('data-id', hoaDonId);
          }
          if (invoiceTypeSelect) {
            invoiceTypeSelect.value = invoiceType;
          }
        });
      }

      // Điền dữ liệu vào modal sửa nhanh
      if (quickUpdateModal) {
        quickUpdateModal.addEventListener('show.bs.modal', function(event) {
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
        confirmBtn.addEventListener('click', function() {
          const hoaDonId = this?.getAttribute('data-id');
          const invoiceType = invoiceTypeSelect?.value || '';
          const hinhThuc = paymentMethodSelect?.value || '';
          const ghiChu = document.querySelector('textarea[name="ghi_chu_thanh_toan"]')?.value || '';

          if (!hoaDonId || !hinhThuc || !invoiceType) {
            alert('⚠️ Vui lòng chọn loại hóa đơn và hình thức thanh toán!');
            return;
          }

          fetch(`/hoadon/thanhtoan/${hoaDonId}`, {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({
                type: invoiceType,
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

    });
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const quickUpdateModal = document.getElementById('quickUpdateModal');
      const quickUpdateBtn = document.getElementById('quickUpdateBtn');

      // Điền dữ liệu vào modal khi mở
      if (quickUpdateModal) {
        quickUpdateModal.addEventListener('show.bs.modal', function(event) {
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

      // Gửi cập nhật nhanh giá điện/nước
      if (quickUpdateBtn) {
        quickUpdateBtn.addEventListener('click', async function() {
          const url = this.getAttribute('data-url');
          const dien = quickUpdateModal.querySelector('input[name="don_gia_dien"]').value;
          const nuoc = quickUpdateModal.querySelector('input[name="don_gia_nuoc"]').value;

          if (!url) {
            alert('❌ Không xác định được URL cập nhật.');
            return;
          }

          try {
            const response = await fetch(url, {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({
                don_gia_dien: dien,
                don_gia_nuoc: nuoc
              })
            });

            const data = await response.json();

            if (data.success) {
              alert('✅ Đã cập nhật giá điện/nước.');

              // Đóng modal an toàn với Bootstrap 5.3.0
              const modalInstance = new bootstrap.Modal(quickUpdateModal);
              modalInstance.hide();

              setTimeout(() => location.reload(), 300);
            } else {
              alert('❌ Cập nhật thất bại từ phía server.');
            }
          } catch (error) {
            console.error('Lỗi cập nhật nhanh:', error);
            alert('❌ Không thể xử lý phản hồi từ server.');
          }
        });
      }
    });
  </script>



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
          <div class="mb-3">
            <label for="invoiceType" class="form-label">Loại hóa đơn</label>
            <select id="invoiceType" class="form-select">
              <option value="tien-phong">Tiền phòng</option>
              <option value="dien-nuoc">Điện · nước</option>
            </select>
          </div>
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
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="quickUpdateLabel">Cập nhật giá điện/nước</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Đơn giá điện</label>
            <input type="number" name="don_gia_dien" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Đơn giá nước</label>
            <input type="number" name="don_gia_nuoc" class="form-control" required>
          </div>
          <button type="button" class="btn btn-primary" id="quickUpdateBtn">Cập nhật</button>
        </div>
      </div>
    </div>
  </div>




{{-- MODAL BỘ LỌC --}}
<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Bộ lọc hóa đơn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <form method="GET" action="{{ route('hoadon.index') }}" id="filterForm">
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="small text-muted">Từ ngày</label>
                                <input type="date" name="from_date" value="{{ request('from_date') }}"
                                    class="form-control">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="small text-muted">Đến ngày</label>
                                <input type="date" name="to_date" value="{{ request('to_date') }}"
                                    class="form-control">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="small text-muted">Khu</label>
                                <select name="khu" class="form-control">
                                    <option value="">-- Tất cả --</option>
                                    @foreach(['A','B','C','D','E','F'] as $khu)
                                        <option value="{{ $khu }}" @selected(request('khu') == $khu)>
                                            Khu {{ $khu }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="small text-muted">Phòng</label>
                                <select name="phong_id" class="form-control">
                                    <option value="">-- Tất cả --</option>
                                    @foreach($dsPhongs as $phong)
                                        <option value="{{ $phong->id }}" @selected(request('phong_id') == $phong->id)>
                                            {{ $phong->ten_phong }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="small text-muted">Trạng thái</label>
                                <select name="trang_thai" class="form-control">
                                    <option value="">-- Tất cả --</option>
                                    <option value="da_thanh_toan" @selected(request('trang_thai') == 'da_thanh_toan')>Đã thanh toán</option>
                                    <option value="chua_thanh_toan" @selected(request('trang_thai') == 'chua_thanh_toan')>Chưa thanh toán</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="{{ route('hoadon.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
                    <button type="submit" class="btn btn-primary">Áp dụng</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Mở modal bộ lọc hóa đơn (chạy được cho cả Bootstrap 4 và 5)
        (function() {
            document.addEventListener('DOMContentLoaded', function() {
                var btn = document.getElementById('openFilterModalBtn');
                if (!btn) return;

                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var modalEl = document.getElementById('filterModal');
                    if (!modalEl) return;

                    try {
                        if (window.bootstrap && bootstrap.Modal) {
                            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                            modal.show();
                        } else if (window.$ && $('#filterModal').modal) {
                            $('#filterModal').modal('show');
                        }
                    } catch (err) {
                        if (window.$ && $('#filterModal').modal) {
                            $('#filterModal').modal('show');
                        }
                    }
                });
            });
        })();
    </script>
    <script>
        // Đảm bảo dropdown thao tác không bị che
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.dropdown').forEach(function (dd) {
                dd.addEventListener('show.bs.dropdown', function () {
                    const tr = dd.closest('tr');
                    if (tr) {
                        tr.classList.add('active-menu');
                        tr.style.zIndex = 9999;
                    }
                });
                dd.addEventListener('hidden.bs.dropdown', function () {
                    const tr = dd.closest('tr');
                    if (tr) {
                        tr.classList.remove('active-menu');
                        tr.style.zIndex = '';
                    }
                });
            });
        });
    </script>
@endpush
@endsection