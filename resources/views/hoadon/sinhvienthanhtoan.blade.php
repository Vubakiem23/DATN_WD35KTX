@extends('admin.layouts.admin')

@php
  use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="container-fluid py-4">
  <div class="row mb-4">
    <div class="col-md-12">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h3 class="mb-1">📋 Yêu cầu xác nhận thanh toán</h3>
          <p class="text-muted mb-0">Danh sách các yêu cầu thanh toán đang chờ xác nhận từ quản lý phòng</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Tabs chuyển đổi giữa tiền phòng và điện nước -->
  <div class="card shadow-sm mb-4">
    <div class="card-header">
      <ul class="nav nav-tabs card-header-tabs" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" id="tien-phong-tab" data-bs-toggle="tab" href="#tien-phong" role="tab" aria-controls="tien-phong" aria-selected="true">
            <i class="fa fa-home me-2"></i>Tiền phòng ({{ $slotPayments->count() }})
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="dien-nuoc-tab" data-bs-toggle="tab" href="#dien-nuoc" role="tab" aria-controls="dien-nuoc" aria-selected="false">
            <i class="fa fa-bolt me-2"></i>Điện · Nước ({{ $utilitiesPayments->count() }})
          </a>
        </li>
      </ul>
    </div>

    <div class="card-body">
      <div class="tab-content">
        <!-- TAB 1: TIỀN PHÒNG (SLOT) -->
        <div class="tab-pane fade show active" id="tien-phong" role="tabpanel" aria-labelledby="tien-phong-tab">
          @if($slotPayments->isEmpty())
            <div class="alert alert-info" role="alert">
              <i class="fa fa-info-circle me-2"></i>
              <strong>Không có yêu cầu</strong> — Bạn chưa gửi yêu cầu thanh toán tiền phòng nào.
            </div>
          @else
            <div class="table-responsive">
              <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th style="width: 15%;">Hóa đơn</th>
                    <th style="width: 12%;">Slot</th>
                    <th style="width: 15%;">Tiền phòng</th>
                    <th style="width: 15%;">Hình thức</th>
                    <th style="width: 18%;">Ngày gửi</th>
                    <th style="width: 13%;">Trạng thái</th>
                    <th style="width: 12%;">Thao tác</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($slotPayments as $payment)
                    @php
                      $statusClass = match($payment->trang_thai ?? 'chua_thanh_toan') {
                        'cho_xac_nhan' => 'badge bg-info text-dark',
                        'da_thanh_toan' => 'badge bg-success',
                        default => 'badge bg-warning text-dark',
                      };
                      $statusText = match($payment->trang_thai ?? 'chua_thanh_toan') {
                        'cho_xac_nhan' => 'Chờ xác nhận',
                        'da_thanh_toan' => 'Đã xác nhận',
                        default => 'Chưa thanh toán',
                      };
                      $methodText = match($payment->hinh_thuc_thanh_toan ?? '') {
                        'chuyen_khoan' => '🏦 Chuyển khoản',
                        'tien_mat' => '💵 Tiền mặt',
                        default => '-',
                      };
                    @endphp
                    <tr>
                      <td>
                        <a href="{{ route('hoadon.show', $payment->hoaDon->id) }}" target="_blank" class="text-primary fw-semibold">
                          HĐ #{{ $payment->hoaDon->id }}
                        </a>
                      </td>
                      <td>
                        <span class="badge bg-secondary">{{ $payment->slot_label }}</span>
                      </td>
                      <td>
                        <span class="fw-semibold text-success">
                          {{ number_format($payment->hoaDon->tien_phong_slot ?? 0, 0, ',', '.') }} VND
                        </span>
                      </td>
                      <td>{{ $methodText }}</td>
                      <td>
                        {{ $payment->client_requested_at ? \Carbon\Carbon::parse($payment->client_requested_at)->format('d/m/Y H:i') : '-' }}
                      </td>
                      <td>
                        <span class="{{ $statusClass }}">{{ $statusText }}</span>
                      </td>
                      <td>
                        <button type="button"
                          class="btn btn-sm btn-outline-primary payment-detail-btn"
                          data-bs-toggle="modal"
                          data-bs-target="#slotPaymentDetailModal"
                          data-invoice-id="{{ $payment->hoaDon->id }}"
                          data-slot-label="{{ $payment->slot_label }}"
                          data-room="{{ optional($payment->hoaDon->phong)->ten_phong ?? 'N/A' }}"
                          data-amount="{{ number_format($payment->hoaDon->tien_phong_slot ?? 0, 0, ',', '.') }} VND"
                          data-method="{{ $methodText }}"
                          data-requested-at="{{ $payment->client_requested_at ? \Carbon\Carbon::parse($payment->client_requested_at)->format('d/m/Y H:i') : '-' }}"
                          data-confirmed-at="{{ $payment->ngay_thanh_toan ? \Carbon\Carbon::parse($payment->ngay_thanh_toan)->format('d/m/Y H:i') : '-' }}"
                          data-status="{{ $statusText }}"
                          data-note="{{ $payment->client_ghi_chu ?? '' }}"
                          data-admin-note="{{ $payment->ghi_chu ?? '' }}"
                          data-image="{{ $payment->hinh_thuc_thanh_toan === 'chuyen_khoan' && $payment->client_transfer_image_path ? Storage::disk('public')->url($payment->client_transfer_image_path) : '' }}">
                          <i class="fa fa-eye me-1"></i> Chi tiết
                        </button>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>

        <!-- TAB 2: ĐIỆN · NƯỚC -->
        <div class="tab-pane fade" id="dien-nuoc" role="tabpanel" aria-labelledby="dien-nuoc-tab">
          @if($utilitiesPayments->isEmpty())
            <div class="alert alert-info" role="alert">
              <i class="fa fa-info-circle me-2"></i>
              <strong>Không có yêu cầu</strong> — Bạn chưa gửi yêu cầu thanh toán điện · nước nào.
            </div>
          @else
            <div class="table-responsive">
              <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th style="width: 12%;">Hóa đơn</th>
                    <th style="width: 12%;">Slot</th>
                    <th style="width: 10%;">Tiền điện</th>
                    <th style="width: 10%;">Tiền nước</th>
                    <th style="width: 10%;">Tổng cộng</th>
                    <th style="width: 15%;">Hình thức</th>
                    <th style="width: 15%;">Ngày gửi</th>
                    <th style="width: 10%;">Trạng thái</th>
                    <th style="width: 6%;">Thao tác</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($utilitiesPayments as $payment)
                    @php
                      $statusClass = match($payment->trang_thai ?? 'chua_thanh_toan') {
                        'cho_xac_nhan' => 'badge bg-info text-dark',
                        'da_thanh_toan' => 'badge bg-success',
                        default => 'badge bg-warning text-dark',
                      };
                      $statusText = match($payment->trang_thai ?? 'chua_thanh_toan') {
                        'cho_xac_nhan' => 'Chờ xác nhận',
                        'da_thanh_toan' => 'Đã xác nhận',
                        default => 'Chưa thanh toán',
                      };
                      $methodText = match($payment->hinh_thuc_thanh_toan ?? '') {
                        'chuyen_khoan' => '🏦 Chuyển khoản',
                        'tien_mat' => '💵 Tiền mặt',
                        default => '-',
                      };
                      $totalAmount = ($payment->tien_dien ?? 0) + ($payment->tien_nuoc ?? 0);
                    @endphp
                    <tr>
                      <td>
                        <a href="{{ route('hoadon.show', $payment->hoaDon->id) }}" target="_blank" class="text-primary fw-semibold">
                          HĐ #{{ $payment->hoaDon->id }}
                        </a>
                      </td>
                      <td>
                        <span class="badge bg-secondary">{{ $payment->slot_label }}</span>
                      </td>
                      <td>
                        <span class="text-danger fw-semibold">
                          {{ number_format($payment->tien_dien ?? 0, 0, ',', '.') }} VND
                        </span>
                      </td>
                      <td>
                        <span class="text-info fw-semibold">
                          {{ number_format($payment->tien_nuoc ?? 0, 0, ',', '.') }} VND
                        </span>
                      </td>
                      <td>
                        <span class="fw-bold">
                          {{ number_format($totalAmount, 0, ',', '.') }} VND
                        </span>
                      </td>
                      <td>{{ $methodText }}</td>
                      <td>
                        {{ $payment->client_requested_at ? \Carbon\Carbon::parse($payment->client_requested_at)->format('d/m/Y H:i') : '-' }}
                      </td>
                      <td>
                        <span class="{{ $statusClass }}">{{ $statusText }}</span>
                      </td>
                      <td>
                        <button type="button"
                          class="btn btn-sm btn-outline-primary payment-detail-btn"
                          data-bs-toggle="modal"
                          data-bs-target="#utilitiesPaymentDetailModal"
                          data-invoice-id="{{ $payment->hoaDon->id }}"
                          data-slot-label="{{ $payment->slot_label }}"
                          data-room="{{ optional($payment->hoaDon->phong)->ten_phong ?? 'N/A' }}"
                          data-tien-dien="{{ number_format($payment->tien_dien ?? 0, 0, ',', '.') }} VND"
                          data-tien-nuoc="{{ number_format($payment->tien_nuoc ?? 0, 0, ',', '.') }} VND"
                          data-total="{{ number_format($totalAmount, 0, ',', '.') }} VND"
                          data-method="{{ $methodText }}"
                          data-requested-at="{{ $payment->client_requested_at ? \Carbon\Carbon::parse($payment->client_requested_at)->format('d/m/Y H:i') : '-' }}"
                          data-confirmed-at="{{ $payment->ngay_thanh_toan ? \Carbon\Carbon::parse($payment->ngay_thanh_toan)->format('d/m/Y H:i') : '-' }}"
                          data-status="{{ $statusText }}"
                          data-note="{{ $payment->client_ghi_chu ?? '' }}"
                          data-admin-note="{{ $payment->ghi_chu ?? '' }}"
                          data-image="{{ $payment->hinh_thuc_thanh_toan === 'chuyen_khoan' && $payment->client_transfer_image_path ? Storage::disk('public')->url($payment->client_transfer_image_path) : '' }}">
                          <i class="fa fa-eye me-1"></i> Chi tiết
                        </button>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Chi tiết Tiền phòng -->
<div class="modal fade" id="slotPaymentDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-primary text-white border-0">
        <h5 class="modal-title fw-bold">
          <i class="fa fa-receipt me-2"></i>Chi tiết yêu cầu thanh toán tiền phòng
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body p-4">
        <div class="row g-3">
          <div class="col-md-6">
            <div class="card border-0 bg-light">
              <div class="card-body">
                <h6 class="text-primary fw-bold mb-3">📄 Thông tin chung</h6>
                <div class="mb-3">
                  <small class="text-muted d-block">Hóa đơn</small>
                  <span id="slotDetailInvoiceId" class="fw-semibold">-</span>
                </div>
                <div class="mb-3">
                  <small class="text-muted d-block">Phòng</small>
                  <span id="slotDetailRoom" class="fw-semibold">-</span>
                </div>
                <div class="mb-3">
                  <small class="text-muted d-block">Slot</small>
                  <span id="slotDetailLabel" class="badge bg-secondary">-</span>
                </div>
                <div>
                  <small class="text-muted d-block">Tiền phòng</small>
                  <span id="slotDetailAmount" class="fw-bold text-success fs-5">-</span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card border-0 bg-light">
              <div class="card-body">
                <h6 class="text-success fw-bold mb-3">💳 Thanh toán</h6>
                <div class="mb-3">
                  <small class="text-muted d-block">Hình thức</small>
                  <span id="slotDetailMethod" class="fw-semibold">-</span>
                </div>
                <div class="mb-3">
                  <small class="text-muted d-block">Ngày gửi yêu cầu</small>
                  <span id="slotDetailRequestedAt" class="fw-semibold">-</span>
                </div>
                <div class="mb-3">
                  <small class="text-muted d-block">Ngày xác nhận</small>
                  <span id="slotDetailConfirmedAt" class="fw-semibold">-</span>
                </div>
                <div>
                  <small class="text-muted d-block">Trạng thái</small>
                  <span id="slotDetailStatus" class="badge bg-info text-dark">-</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="mt-4">
          <h6 class="fw-bold mb-2">📝 Ghi chú của bạn</h6>
          <div class="card border-0 bg-light">
            <div class="card-body">
              <p id="slotDetailNote" class="mb-0 text-muted">-</p>
            </div>
          </div>
        </div>
        <div class="mt-4" id="slotImageWrapper" style="display: none;">
          <h6 class="fw-bold mb-2">🖼️ Ảnh chuyển khoản</h6>
          <div class="text-center">
            <img id="slotDetailImage" src="" alt="Ảnh chuyển khoản" class="img-fluid rounded shadow" style="max-height: 300px;">
          </div>
        </div>
      </div>
      <div class="modal-footer border-0 bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="fa fa-times me-1"></i>Đóng
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Chi tiết Điện · Nước -->
<div class="modal fade" id="utilitiesPaymentDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-success text-white border-0">
        <h5 class="modal-title fw-bold">
          <i class="fa fa-leaf me-2"></i>Chi tiết yêu cầu thanh toán điện · nước
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body p-4">
        <div class="row g-3">
          <div class="col-md-6">
            <div class="card border-0 bg-light">
              <div class="card-body">
                <h6 class="text-primary fw-bold mb-3">📄 Thông tin chung</h6>
                <div class="mb-3">
                  <small class="text-muted d-block">Hóa đơn</small>
                  <span id="utilitiesDetailInvoiceId" class="fw-semibold">-</span>
                </div>
                <div class="mb-3">
                  <small class="text-muted d-block">Phòng</small>
                  <span id="utilitiesDetailRoom" class="fw-semibold">-</span>
                </div>
                <div class="mb-3">
                  <small class="text-muted d-block">Slot</small>
                  <span id="utilitiesDetailLabel" class="badge bg-secondary">-</span>
                </div>
                <div>
                  <small class="text-muted d-block">Tổng cộng</small>
                  <span id="utilitiesDetailTotal" class="fw-bold text-success fs-5">-</span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card border-0 bg-light">
              <div class="card-body">
                <h6 class="text-warning fw-bold mb-3">⚡ Chi tiết tiền</h6>
                <div class="mb-3">
                  <small class="text-muted d-block">Tiền điện</small>
                  <span id="utilitiesDetailTienDien" class="fw-semibold text-danger">-</span>
                </div>
                <div class="mb-3">
                  <small class="text-muted d-block">Tiền nước</small>
                  <span id="utilitiesDetailTienNuoc" class="fw-semibold text-info">-</span>
                </div>
                <div>
                  <small class="text-muted d-block">Hình thức</small>
                  <span id="utilitiesDetailMethod" class="fw-semibold">-</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row g-3 mt-2">
          <div class="col-md-6">
            <h6 class="fw-bold mb-2">📅 Thời gian</h6>
            <div class="card border-0 bg-light">
              <div class="card-body">
                <div class="mb-3">
                  <small class="text-muted d-block">Ngày gửi yêu cầu</small>
                  <span id="utilitiesDetailRequestedAt" class="fw-semibold">-</span>
                </div>
                <div>
                  <small class="text-muted d-block">Ngày xác nhận</small>
                  <span id="utilitiesDetailConfirmedAt" class="fw-semibold">-</span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <h6 class="fw-bold mb-2">✔️ Trạng thái</h6>
            <div class="card border-0 bg-light">
              <div class="card-body">
                <span id="utilitiesDetailStatus" class="badge bg-success fs-6">-</span>
              </div>
            </div>
          </div>
        </div>
        <div class="mt-4">
          <h6 class="fw-bold mb-2">📝 Ghi chú của bạn</h6>
          <div class="card border-0 bg-light">
            <div class="card-body">
              <p id="utilitiesDetailNote" class="mb-0 text-muted">-</p>
            </div>
          </div>
        </div>
        <div class="mt-4" id="utilitiesImageWrapper" style="display: none;">
          <h6 class="fw-bold mb-2">🖼️ Ảnh chuyển khoản</h6>
          <div class="text-center">
            <img id="utilitiesDetailImage" src="" alt="Ảnh chuyển khoản" class="img-fluid rounded shadow" style="max-height: 300px;">
          </div>
        </div>
      </div>
      <div class="modal-footer border-0 bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="fa fa-times me-1"></i>Đóng
        </button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const slotModal = document.getElementById('slotPaymentDetailModal');
  const utilitiesModal = document.getElementById('utilitiesPaymentDetailModal');

  // Xử lý modal tiền phòng
  if (slotModal) {
    slotModal.addEventListener('show.bs.modal', function(e) {
      const button = e.relatedTarget;
      if (!button) return;

      document.getElementById('slotDetailInvoiceId').textContent = 'HĐ #' + button.getAttribute('data-invoice-id');
      document.getElementById('slotDetailRoom').textContent = button.getAttribute('data-room') || '-';
      document.getElementById('slotDetailLabel').textContent = button.getAttribute('data-slot-label') || '-';
      document.getElementById('slotDetailAmount').textContent = button.getAttribute('data-amount') || '-';
      document.getElementById('slotDetailMethod').textContent = button.getAttribute('data-method') || '-';
      document.getElementById('slotDetailRequestedAt').textContent = button.getAttribute('data-requested-at') || '-';
      document.getElementById('slotDetailConfirmedAt').textContent = button.getAttribute('data-confirmed-at') || '-';
      document.getElementById('slotDetailStatus').textContent = button.getAttribute('data-status') || '-';
      document.getElementById('slotDetailNote').textContent = button.getAttribute('data-note') || '(Không có ghi chú)';

      const imageUrl = button.getAttribute('data-image') || '';
      const imageWrapper = document.getElementById('slotImageWrapper');
      if (imageUrl && imageUrl.trim() !== '') {
        imageWrapper.style.display = 'block';
        document.getElementById('slotDetailImage').src = imageUrl;
      } else {
        imageWrapper.style.display = 'none';
      }
    });
  }

  // Xử lý modal điện nước
  if (utilitiesModal) {
    utilitiesModal.addEventListener('show.bs.modal', function(e) {
      const button = e.relatedTarget;
      if (!button) return;

      document.getElementById('utilitiesDetailInvoiceId').textContent = 'HĐ #' + button.getAttribute('data-invoice-id');
      document.getElementById('utilitiesDetailRoom').textContent = button.getAttribute('data-room') || '-';
      document.getElementById('utilitiesDetailLabel').textContent = button.getAttribute('data-slot-label') || '-';
      document.getElementById('utilitiesDetailTienDien').textContent = button.getAttribute('data-tien-dien') || '-';
      document.getElementById('utilitiesDetailTienNuoc').textContent = button.getAttribute('data-tien-nuoc') || '-';
      document.getElementById('utilitiesDetailTotal').textContent = button.getAttribute('data-total') || '-';
      document.getElementById('utilitiesDetailMethod').textContent = button.getAttribute('data-method') || '-';
      document.getElementById('utilitiesDetailRequestedAt').textContent = button.getAttribute('data-requested-at') || '-';
      document.getElementById('utilitiesDetailConfirmedAt').textContent = button.getAttribute('data-confirmed-at') || '-';
      document.getElementById('utilitiesDetailStatus').textContent = button.getAttribute('data-status') || '-';
      document.getElementById('utilitiesDetailNote').textContent = button.getAttribute('data-note') || '(Không có ghi chú)';

      const imageUrl = button.getAttribute('data-image') || '';
      const imageWrapper = document.getElementById('utilitiesImageWrapper');
      if (imageUrl && imageUrl.trim() !== '') {
        imageWrapper.style.display = 'block';
        document.getElementById('utilitiesDetailImage').src = imageUrl;
      } else {
        imageWrapper.style.display = 'none';
      }
    });
  }
});
</script>
@endpush

@endsection
