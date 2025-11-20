@extends('admin.layouts.admin')

@php
  use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="container py-4 ">
  @php
    $viewMode = request()->get('view');
    $isDienNuocOnly = $viewMode === 'dien-nuoc';
    $isPhongOnly = $viewMode === 'phong';
    $soDien = max(0, ($hoaDon->so_dien_moi ?? 0) - ($hoaDon->so_dien_cu ?? 0));
    $soNuoc = max(0, ($hoaDon->so_nuoc_moi ?? 0) - ($hoaDon->so_nuoc_cu ?? 0));
    $tongDienNuoc = ($hoaDon->tien_dien ?? 0) + ($hoaDon->tien_nuoc ?? 0);
  @endphp
  <h3 class="mb-1">
    📄
    @if($isDienNuocOnly)
      Chi tiết hóa đơn điện · nước phòng {{ $hoaDon->phong->ten_phong }}
    @elseif($isPhongOnly)
      Chi tiết hóa đơn tiền phòng {{ $hoaDon->phong->ten_phong }}
    @else
      Chi tiết hóa đơn phòng {{ $hoaDon->phong->ten_phong }}
    @endif
  </h3>
  <div class="text-muted mb-3">Kiểm tra kĩ thông tin trước khi thanh toán</div>

  <style>
    .btn-dergin{display:inline-flex;align-items:center;justify-content:center;gap:.35rem;padding:.45rem 1rem;border-radius:999px;font-weight:600;font-size:.8rem;border:none;color:#fff;background:linear-gradient(135deg,#4e54c8 0%,#8f94fb 100%);box-shadow:0 6px 16px rgba(78,84,200,.22);transition:transform .2s ease,box-shadow .2s ease;text-decoration:none}
    .btn-dergin:hover{transform:translateY(-1px);box-shadow:0 10px 22px rgba(78,84,200,.32);color:#fff}
    .btn-dergin i{font-size:.85rem}
    .btn-dergin--muted{background:linear-gradient(135deg,#4f46e5 0%,#6366f1 100%)}
    .btn-dergin--info{background:linear-gradient(135deg,#0ea5e9 0%,#2563eb 100%)}
    .btn-dergin--danger{background:linear-gradient(135deg,#f43f5e 0%,#ef4444 100%)}
    .btn-dergin--success{background:linear-gradient(135deg,#10b981 0%,#22c55e 100%)}
    .invoice-actions{display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1rem}
    .table-invoice th{width:28%;color:#374151}
    .table-invoice td{width:72%}
    .total-row td{font-weight:800;color:#0f172a}
    .total-row td strong{font-size:1rem}
    .badge-soft-success{background:#e8fff3;color:#107154;border-radius:999px;padding:.35rem .6rem;font-weight:600}
    .badge-soft-danger{background:#fff1f2;color:#b91c1c;border-radius:999px;padding:.35rem .6rem;font-weight:600}
  </style>

  <div class="invoice-actions">
    <a href="{{ route('hoadon.index') }}" class="btn-dergin btn-dergin--muted" title="Quay lại">
      <i class="fa fa-arrow-left"></i><span>Quay lại</span>
    </a>
    @if(($hoaDon->trang_thai ?? '') !== 'Đã thanh toán')
      <a href="{{ route('hoadon.edit', $hoaDon->id) }}" class="btn-dergin" title="Sửa">
        <i class="fa fa-pencil"></i><span>Sửa</span>
      </a>
    @endif
  </div>

  <div class="card shadow-sm mb-4">
    <div class="card-body">
      @if($isDienNuocOnly)
        <table class="table table-bordered text-start table-invoice mb-0">
          <tr><th>Khu</th><td>{{ optional($hoaDon->phong->khu)->ten_khu ?? 'Không rõ khu' }}</td></tr>
          <tr><th>Tên Phòng</th><td>{{ optional($hoaDon->phong)->ten_phong ?? 'Không rõ' }}</td></tr>
          <tr><th>Tháng</th><td>{{ \Carbon\Carbon::parse($hoaDon->created_at)->format('m/Y') }}</td></tr>
          <tr><th>Loại Phòng</th><td>{{ optional($hoaDon->phong)->loai_phong ?? 'Không rõ' }}</td></tr>
          <tr><th>Điện cũ</th><td>{{ $hoaDon->so_dien_cu ?? 0 }}</td></tr>
          <tr><th>Điện mới</th><td>{{ $hoaDon->so_dien_moi ?? 0 }}</td></tr>
          <tr><th>Sản lượng điện</th><td>{{ $soDien }}</td></tr>
          <tr><th>Đơn giá điện</th><td>{{ number_format($hoaDon->don_gia_dien ?? 0, 0, ',', '.') }} VND</td></tr>
          <tr><th>Tiền điện</th><td>{{ number_format($hoaDon->tien_dien ?? 0, 0, ',', '.') }} VND</td></tr>
          <tr><th>Nước cũ</th><td>{{ $hoaDon->so_nuoc_cu ?? 0 }}</td></tr>
          <tr><th>Nước mới</th><td>{{ $hoaDon->so_nuoc_moi ?? 0 }}</td></tr>
          <tr><th>Sản lượng nước</th><td>{{ $soNuoc }}</td></tr>
          <tr><th>Đơn giá nước</th><td>{{ number_format($hoaDon->don_gia_nuoc ?? 0, 0, ',', '.') }} VND</td></tr>
          <tr><th>Tiền nước</th><td>{{ number_format($hoaDon->tien_nuoc ?? 0, 0, ',', '.') }} VND</td></tr>
          <tr><th>Ngày chốt</th><td>{{ $hoaDon->created_at->format('d/m/Y H:i') }}</td></tr>
          <tr><th>Ngày thanh toán</th><td>{{ $hoaDon->ngay_thanh_toan ? \Carbon\Carbon::parse($hoaDon->ngay_thanh_toan)->format('d/m/Y') : '-' }}</td></tr>
          <tr class="total-row"><th>Tổng tiền điện · nước</th><td><strong>{{ number_format($tongDienNuoc, 0, ',', '.') }} VND</strong></td></tr>
          <tr>
            <th>Trạng thái</th>
            <td>
              @if(($hoaDon->trang_thai ?? '') === 'Đã thanh toán')
                <span class="badge-soft-success">Đã thanh toán</span>
              @else
                <span class="badge-soft-danger">Chưa thanh toán</span>
              @endif
            </td>
          </tr>
        </table>
      @else
        <table class="table table-bordered text-start table-invoice mb-0">
          <tr><th>Khu</th><td>{{ optional($hoaDon->phong->khu)->ten_khu ?? 'Không rõ khu' }}</td></tr>
          <tr><th>Tên Phòng</th><td>{{ optional($hoaDon->phong)->ten_phong ?? 'Không rõ' }}</td></tr>
          <tr><th>Tháng</th><td>{{ \Carbon\Carbon::parse($hoaDon->created_at)->format('m/Y') }}</td></tr>
          <tr><th>Loại Phòng</th><td>{{ optional($hoaDon->phong)->loai_phong ?? 'Không rõ' }}</td></tr>
          <tr><th>Tính từ ngày</th><td>{{ $hoaDon->created_at ? $hoaDon->created_at->format('d/m/Y') : '-' }}</td></tr>
          <tr><th>Ngày chốt</th><td>{{ $hoaDon->created_at->format('d/m/Y H:i') }}</td></tr>
          <tr><th>Ngày thanh toán</th><td>{{ $hoaDon->ngay_thanh_toan ? \Carbon\Carbon::parse($hoaDon->ngay_thanh_toan)->format('d/m/Y') : '-' }}</td></tr>
          <tr class="total-row"><th>Thành tiền</th><td><strong>{{ number_format($hoaDon->thanh_tien, 0, ',', '.') }} VND</strong></td></tr>
          <tr>
            <th>Trạng thái</th>
            <td>
              @if(($hoaDon->trang_thai ?? '') === 'Đã thanh toán')
                <span class="badge-soft-success">Đã thanh toán</span>
              @else
                <span class="badge-soft-danger">Chưa thanh toán</span>
              @endif
            </td>
          </tr>
        </table>
      @endif
    </div>
  </div>

  @php
    $slotPaymentsMap = [];
    if ($hoaDon->relationLoaded('slotPayments')) {
      foreach ($hoaDon->slotPayments as $payment) {
        $slotPaymentsMap[$payment->slot_label] = $payment;
      }
    }

    $utilitiesPaymentsMap = [];
    if ($hoaDon->relationLoaded('utilitiesPayments')) {
      foreach ($hoaDon->utilitiesPayments as $payment) {
        $utilitiesPaymentsMap[$payment->slot_label] = $payment;
      }
    }
  @endphp

  @if(!$isDienNuocOnly)
  <div class="card shadow-sm mb-4">
    <div class="card-header fw-semibold text-uppercase">Chi tiết tiền phòng</div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-3">
          <div class="p-3 border rounded bg-light">
            <div class="text-muted text-uppercase small">Số slot tính phí</div>
            <div class="fs-4 fw-semibold">{{ $hoaDon->slot_billing_count ?? 0 }}</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="p-3 border rounded bg-light">
            <div class="text-muted text-uppercase small">Đơn giá mỗi slot</div>
            <div class="fs-4 fw-semibold">{{ number_format($hoaDon->slot_unit_price ?? 0, 0, ',', '.') }} VND</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="p-3 border rounded bg-light">
            <div class="text-muted text-uppercase small">Tiền phòng</div>
            <div class="fs-4 fw-semibold text-success">{{ number_format($hoaDon->tien_phong_slot ?? 0, 0, ',', '.') }} VND</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="p-3 border rounded bg-light">
            <div class="text-muted text-uppercase small">Trạng thái thanh toán</div>
            @php
              $paidSlots = $hoaDon->slotPayments ? $hoaDon->slotPayments->where('da_thanh_toan', true)->count() : 0;
              $totalSlots = $hoaDon->slotPayments ? $hoaDon->slotPayments->count() : ($hoaDon->slot_billing_count ?? 0);
            @endphp
            <div class="fs-4 fw-semibold {{ $paidSlots >= $totalSlots && $totalSlots > 0 ? 'text-success' : 'text-warning' }}">
              {{ $paidSlots }}/{{ $totalSlots }} người đã thanh toán
            </div>
            @if($paidSlots >= $totalSlots && $totalSlots > 0)
              <div class="small text-success mt-1"><i class="fa fa-check-circle"></i> Hoàn thành</div>
            @endif
          </div>
        </div>
      </div>

      @if(!empty($hoaDon->slot_breakdowns))
        <div class="table-responsive mt-4">
          <table class="table table-striped text-center mb-0">
            <thead class="table-light">
              <tr>
                <th>Slot</th>
                <th>Sinh viên</th>
                <th>Tiền phòng</th>
                <th>Trạng thái thanh toán</th>
                <th>Thao tác</th>
              </tr>
            </thead>
            <tbody>
              @foreach($hoaDon->slot_breakdowns as $slot)
                @php
                  $slotPayment = $slotPaymentsMap[$slot['label']] ?? null;
                  $slotStatus = $slotPayment
                    ? ($slotPayment->trang_thai ?? ($slotPayment->da_thanh_toan ? 'da_thanh_toan' : 'chua_thanh_toan'))
                    : 'chua_thanh_toan';
                  $statusMeta = match($slotStatus) {
                    'da_thanh_toan' => ['class' => 'badge bg-success', 'text' => 'Đã thanh toán', 'icon' => 'fa-check-circle'],
                    'cho_xac_nhan' => ['class' => 'badge bg-info text-dark', 'text' => 'Chờ xác nhận', 'icon' => 'fa-hourglass-half'],
                    default => ['class' => 'badge bg-warning text-dark', 'text' => 'Chưa thanh toán', 'icon' => 'fa-clock'],
                  };
                  $paymentMethodText = $slotPayment?->hinh_thuc_thanh_toan === 'chuyen_khoan' ? 'Chuyển khoản' : ($slotPayment?->hinh_thuc_thanh_toan === 'tien_mat' ? 'Tiền mặt' : null);
                  $clientRequestedText = ($slotPayment && $slotPayment->client_requested_at)
                    ? \Carbon\Carbon::parse($slotPayment->client_requested_at)->format('d/m/Y H:i')
                    : null;
                  $confirmedAtText = ($slotPayment && $slotPayment->ngay_thanh_toan)
                    ? \Carbon\Carbon::parse($slotPayment->ngay_thanh_toan)->format('d/m/Y H:i')
                    : null;
                  $transferImageUrl = ($slotPayment && $slotPayment->hinh_thuc_thanh_toan === 'chuyen_khoan' && $slotPayment->client_transfer_image_path)
                    ? Storage::disk('public')->url($slotPayment->client_transfer_image_path)
                    : null;
                @endphp
                <tr>
                  <td>{{ $slot['label'] }}</td>
                  <td>{{ $slot['sinh_vien'] }}</td>
                  <td class="fw-semibold">{{ number_format($slot['tien_phong'] ?? 0, 0, ',', '.') }} VND</td>
                  <td>
                    <span class="{{ $statusMeta['class'] }}">
                      <i class="fa {{ $statusMeta['icon'] }}"></i> {{ $statusMeta['text'] }}
                    </span>
                  </td>
                  <td class="d-flex flex-column gap-2 align-items-center">
                    @if($slotPayment && $slotStatus === 'cho_xac_nhan')
                      <button type="button"
                        class="btn btn-sm btn-outline-secondary w-100 slot-detail-btn"
                        data-slot-label="{{ $slot['label'] }}"
                        data-sinh-vien="{{ $slot['sinh_vien'] }}"
                        data-tien-phong="{{ number_format($slot['tien_phong'] ?? 0, 0, ',', '.') }} VND"
                        data-amount-label="Tiền phòng"
                        data-status-text="{{ $statusMeta['text'] }}"
                        data-status-icon="{{ $statusMeta['icon'] }}"
                        data-payment-method="{{ $slotPayment->hinh_thuc_thanh_toan ?? '' }}"
                        data-payment-method-text="{{ $paymentMethodText ?? '' }}"
                        data-client-requested-at="{{ $clientRequestedText ?? '' }}"
                        data-confirmed-at="{{ $confirmedAtText ?? '' }}"
                        data-client-note="{{ $slotPayment->client_ghi_chu ?? '' }}"
                        data-admin-note="{{ $slotPayment->ghi_chu ?? '' }}"
                        data-transfer-image="{{ $transferImageUrl ?? '' }}">
                        <i class="fa fa-info-circle"></i> Xem chi tiết
                      </button>
                      <button type="button"
                        class="btn btn-sm btn-success slot-direct-confirm-btn"
                        data-slot-payment-id="{{ $slotPayment->id }}"
                        data-slot-url="{{ route('hoadon.thanhtoanslot', ['hoaDonId' => $hoaDon->id, 'slotPaymentId' => $slotPayment->id]) }}"
                        data-slot-label="{{ $slot['label'] }}"
                        data-sinh-vien="{{ $slot['sinh_vien'] }}"
                        data-slot-payment-method="{{ $slotPayment->hinh_thuc_thanh_toan ?? 'chuyen_khoan' }}">
                        <i class="fa fa-check"></i> Xác nhận
                      </button>
                    @elseif($slotPayment && $slotStatus === 'da_thanh_toan')
                      <span class="text-muted small">Đã hoàn tất</span>
                    @elseif($slotPayment && $slotStatus !== 'da_thanh_toan')
                      <span class="text-muted small">Chưa thanh toán</span>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <p class="text-muted mt-3 mb-0">Chưa có phân bổ slot chi tiết cho tiền phòng.</p>
      @endif
    </div>
  </div>
  @endif

  @if(!$isPhongOnly && !empty($hoaDon->slot_breakdowns_dien_nuoc ?? $hoaDon->slot_breakdowns))
    <div class="card shadow-sm mb-4">
      <div class="card-header fw-semibold text-uppercase">Chi tiết tiền điện · nước</div>
      <div class="card-body">
        <div class="table-responsive">
          @php
            $utilitiesStatusMeta = $hoaDon->da_thanh_toan_dien_nuoc
              ? ['class' => 'badge bg-success', 'text' => 'Đã thanh toán', 'icon' => 'fa-check-circle']
              : ['class' => 'badge bg-warning text-dark', 'text' => 'Chưa thanh toán', 'icon' => 'fa-clock'];
          @endphp
          <table class="table table-striped text-center mb-0">
            <thead class="table-light">
              <tr>
                <th>Slot</th>
                <th>Sinh viên</th>
                <th>Tiền điện</th>
                <th>Tiền nước</th>
                <th>Tổng điện + nước</th>
                <th>Trạng thái thanh toán</th>
                <th>Thao tác</th>
              </tr>
            </thead>
            <tbody>
              @foreach(($hoaDon->slot_breakdowns_dien_nuoc ?? $hoaDon->slot_breakdowns) as $slot)
                @php
                  $utilitiesPayment = $utilitiesPaymentsMap[$slot['label']] ?? null;
                  $slotStatus = $utilitiesPayment
                    ? ($utilitiesPayment->da_thanh_toan ? 'da_thanh_toan' : ($utilitiesPayment->trang_thai ?? 'chua_thanh_toan'))
                    : ($hoaDon->sent_dien_nuoc_to_client ? 'chua_thanh_toan' : 'chua_thanh_toan');
                  $statusMeta = match($slotStatus) {
                    'da_thanh_toan' => ['class' => 'badge bg-success', 'text' => 'Đã thanh toán', 'icon' => 'fa-check-circle'],
                    'cho_xac_nhan' => ['class' => 'badge bg-info text-dark', 'text' => 'Chờ xác nhận', 'icon' => 'fa-hourglass-half'],
                    default => ['class' => 'badge bg-warning text-dark', 'text' => 'Chưa thanh toán', 'icon' => 'fa-clock'],
                  };
                  $paymentMethodText = match($utilitiesPayment?->hinh_thuc_thanh_toan) {
                    'chuyen_khoan' => 'Chuyển khoản',
                    'tien_mat' => 'Tiền mặt',
                    default => 'Chưa cập nhật',
                  };
                  $clientRequestedText = ($utilitiesPayment && $utilitiesPayment->client_requested_at)
                    ? \Carbon\Carbon::parse($utilitiesPayment->client_requested_at)->format('d/m/Y H:i')
                    : null;
                  $confirmedAtText = ($utilitiesPayment && $utilitiesPayment->ngay_thanh_toan)
                    ? \Carbon\Carbon::parse($utilitiesPayment->ngay_thanh_toan)->format('d/m/Y H:i')
                    : null;
                  $transferImageUrl = ($utilitiesPayment && $utilitiesPayment->hinh_thuc_thanh_toan === 'chuyen_khoan' && $utilitiesPayment->client_transfer_image_path)
                    ? Storage::disk('public')->url($utilitiesPayment->client_transfer_image_path)
                    : null;
                @endphp
                <tr>
                  <td>{{ $slot['label'] }}</td>
                  <td>{{ $slot['sinh_vien'] }}</td>
                  <td>{{ number_format($slot['tien_dien'] ?? 0, 0, ',', '.') }} VND</td>
                  <td>{{ number_format($slot['tien_nuoc'] ?? 0, 0, ',', '.') }} VND</td>
                  <td class="fw-semibold">
                    {{ number_format(($slot['tien_dien'] ?? 0) + ($slot['tien_nuoc'] ?? 0), 0, ',', '.') }} VND
                  </td>
                  <td>
                    <span class="{{ $statusMeta['class'] }}">
                      <i class="fa {{ $statusMeta['icon'] }}"></i> {{ $statusMeta['text'] }}
                    </span>
                    @if($hoaDon->da_thanh_toan_dien_nuoc && $hoaDon->ngay_thanh_toan_dien_nuoc)
                      <div class="small text-muted mt-1">
                        Xác nhận lúc {{ \Carbon\Carbon::parse($hoaDon->ngay_thanh_toan_dien_nuoc)->format('d/m/Y H:i') }}
                      </div>
                    @elseif(!$hoaDon->da_thanh_toan_dien_nuoc && $hoaDon->sent_dien_nuoc_to_client && !$utilitiesPayment)
                      <div class="small text-muted mt-1">Đang chờ thanh toán điện · nước</div>
                    @endif
                    @if($hoaDon->ghi_chu_thanh_toan_dien_nuoc)
                      <div class="small text-muted">Ghi chú: {{ $hoaDon->ghi_chu_thanh_toan_dien_nuoc }}</div>
                    @endif
                  </td>
                  <td>
                    @if(!$hoaDon->sent_dien_nuoc_to_client)
                      <span class="text-muted small">Chưa gửi sinh viên</span>
                    @elseif($utilitiesPayment && $slotStatus === 'cho_xac_nhan')
                      <div class="d-flex flex-column gap-2">
                        <button type="button"
                          class="btn btn-sm btn-outline-secondary slot-detail-btn"
                          data-slot-label="{{ $slot['label'] }}"
                          data-sinh-vien="{{ $slot['sinh_vien'] }}"
                          data-tien-phong="{{ number_format(($slot['tien_dien'] ?? 0) + ($slot['tien_nuoc'] ?? 0), 0, ',', '.') }} VND"
                          data-amount-label="Tiền điện + nước"
                          data-status-text="{{ $statusMeta['text'] }}"
                          data-payment-method-text="{{ $paymentMethodText }}"
                          data-client-requested-at="{{ $clientRequestedText ?? '' }}"
                          data-confirmed-at="{{ $confirmedAtText ?? '' }}"
                          data-client-note="{{ $utilitiesPayment->client_ghi_chu ?? '' }}"
                          data-admin-note="{{ $utilitiesPayment->ghi_chu ?? '' }}"
                          data-transfer-image="{{ $transferImageUrl ?? '' }}">
                          <i class="fa fa-info-circle"></i> Xem chi tiết
                        </button>
                        <button type="button"
                          class="btn btn-sm btn-success slot-direct-confirm-btn"
                          data-slot-payment-id="{{ $utilitiesPayment->id }}"
                          data-slot-url="{{ route('hoadon.thanhtoandiennuoc', ['hoaDonId' => $hoaDon->id, 'utilitiesPaymentId' => $utilitiesPayment->id]) }}"
                          data-slot-label="{{ $slot['label'] }}"
                          data-sinh-vien="{{ $slot['sinh_vien'] }}"
                          data-slot-payment-method="{{ $utilitiesPayment->hinh_thuc_thanh_toan ?? 'chuyen_khoan' }}">
                          <i class="fa fa-check"></i> Xác nhận nhanh
                        </button>
                      </div>
                    @elseif($utilitiesPayment && $slotStatus === 'chua_thanh_toan')
                      <span class="text-muted small">Chưa có yêu cầu</span>
                    @elseif($slotStatus === 'da_thanh_toan')
                      <span class="text-muted small">Đã hoàn tất</span>
                    @else
                      <span class="text-muted small">Không khả dụng</span>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr class="fw-semibold">
                <td colspan="2" class="text-end">Tổng cộng</td>
                <td>{{ number_format($hoaDon->tien_dien ?? 0, 0, ',', '.') }} VND</td>
                <td>{{ number_format($hoaDon->tien_nuoc ?? 0, 0, ',', '.') }} VND</td>
                <td>{{ number_format(($hoaDon->tien_dien ?? 0) + ($hoaDon->tien_nuoc ?? 0), 0, ',', '.') }} VND</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  @endif
</div>

<!-- Modal chi tiết slot -->
<div class="modal fade" id="slotDetailModal" tabindex="-1" aria-labelledby="slotDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-primary text-white border-0">
        <h5 class="modal-title fw-bold" id="slotDetailModalLabel">
          <i class="fa fa-info-circle me-2"></i>Chi tiết slot
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body p-4">
        <div class="row g-4">
          <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
              <div class="card-body p-4">
                <h6 class="text-primary fw-bold mb-3 d-flex align-items-center">
                  <i class="fa fa-info-circle me-2"></i>Thông tin chung
                </h6>
                <div class="info-item mb-3 pb-3 border-bottom">
                  <div class="d-flex align-items-center mb-1">
                    <i class="fa fa-tag text-muted me-2" style="width: 20px;"></i>
                    <strong class="text-muted small">Slot:</strong>
                  </div>
                  <div class="ms-4">
                    <span id="slotDetailSlotLabel" class="fw-semibold text-dark">-</span>
                  </div>
                </div>
                <div class="info-item mb-3 pb-3 border-bottom">
                  <div class="d-flex align-items-center mb-1">
                    <i class="fa fa-user text-muted me-2" style="width: 20px;"></i>
                    <strong class="text-muted small">Sinh viên:</strong>
                  </div>
                  <div class="ms-4">
                    <span id="slotDetailSinhVien" class="fw-semibold text-dark">-</span>
                  </div>
                </div>
                <div class="info-item mb-3 pb-3 border-bottom">
                  <div class="d-flex align-items-center mb-1">
                    <i class="fa fa-money text-muted me-2" style="width: 20px;"></i>
                    <strong class="text-muted small"><span id="slotDetailAmountLabel">Tiền phòng</span>:</strong>
                  </div>
                  <div class="ms-4">
                    <span id="slotDetailAmount" class="fw-bold text-success fs-6">-</span>
                  </div>
                </div>
                <div class="info-item">
                  <div class="d-flex align-items-center mb-1">
                    <i class="fa fa-check-circle text-muted me-2" style="width: 20px;"></i>
                    <strong class="text-muted small">Trạng thái:</strong>
                  </div>
                  <div class="ms-4">
                    <span id="slotDetailStatus" class="badge bg-info text-dark">-</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
              <div class="card-body p-4">
                <h6 class="text-success fw-bold mb-3 d-flex align-items-center">
                  <i class="fa fa-credit-card me-2"></i>Thanh toán
                </h6>
                <div class="info-item mb-3 pb-3 border-bottom">
                  <div class="d-flex align-items-center mb-1">
                    <i class="fa fa-credit-card text-muted me-2" style="width: 20px;"></i>
                    <strong class="text-muted small">Hình thức:</strong>
                  </div>
                  <div class="ms-4">
                    <span id="slotDetailMethod" class="fw-semibold text-dark">-</span>
                  </div>
                </div>
                <div class="info-item mb-3 pb-3 border-bottom">
                  <div class="d-flex align-items-center mb-1">
                    <i class="fa fa-clock-o text-muted me-2" style="width: 20px;"></i>
                    <strong class="text-muted small">SV gửi lúc:</strong>
                  </div>
                  <div class="ms-4">
                    <span id="slotDetailClientRequest" class="text-dark">-</span>
                  </div>
                </div>
                <div class="info-item mb-3 pb-3 border-bottom">
                  <div class="d-flex align-items-center mb-1">
                    <i class="fa fa-check-square text-muted me-2" style="width: 20px;"></i>
                    <strong class="text-muted small">BQL xác nhận:</strong>
                  </div>
                  <div class="ms-4">
                    <span id="slotDetailConfirmedAt" class="text-dark">-</span>
                  </div>
                </div>
                <div class="info-item mb-3 pb-3 border-bottom">
                  <div class="d-flex align-items-center mb-1">
                    <i class="fa fa-file-text text-muted me-2" style="width: 20px;"></i>
                    <strong class="text-muted small">Ghi chú SV:</strong>
                  </div>
                  <div class="ms-4">
                    <span id="slotDetailClientNote" class="text-dark">-</span>
                  </div>
                </div>
                <div class="info-item">
                  <div class="d-flex align-items-center mb-1">
                    <i class="fa fa-comments text-muted me-2" style="width: 20px;"></i>
                    <strong class="text-muted small">Ghi chú BQL:</strong>
                  </div>
                  <div class="ms-4">
                    <span id="slotDetailAdminNote" class="text-dark">-</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="mt-4 d-none" id="slotDetailImageWrapper">
          <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
              <h6 class="text-primary fw-bold mb-3 d-flex align-items-center">
                <i class="fa fa-picture-o me-2"></i>Ảnh chuyển khoản
              </h6>
              <div class="text-center mb-3">
                <a id="slotDetailImageLink" href="#" target="_blank" class="btn btn-outline-primary btn-sm">
                  <i class="fa fa-external-link me-1"></i> Mở ảnh trong tab mới
                </a>
              </div>
              <div class="text-center">
                <img id="slotDetailImage" src="" alt="Ảnh chuyển khoản" class="img-fluid rounded shadow" style="max-height: 400px; width: auto; border: 3px solid #e9ecef;">
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0 bg-light">
        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
          <i class="fa fa-close me-1"></i>Đóng
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal thanh toán slot -->
<div class="modal fade" id="slotPaymentModal" tabindex="-1" aria-labelledby="slotPaymentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="slotPaymentModalLabel">Thanh toán slot</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <strong>Slot:</strong> <span id="modalSlotLabel"></span><br>
          <strong>Sinh viên:</strong> <span id="modalSinhVien"></span>
        </div>
        <div class="mb-3">
          <label for="slotPaymentMethod" class="form-label">Hình thức thanh toán</label>
          <select id="slotPaymentMethod" class="form-select" required>
            <option value="">-- Chọn hình thức --</option>
            <option value="tien_mat">Tiền mặt</option>
            <option value="chuyen_khoan">Chuyển khoản</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="slotPaymentNote" class="form-label">Ghi chú</label>
          <textarea id="slotPaymentNote" class="form-control" rows="3" placeholder="Ghi chú thanh toán (tùy chọn)"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-success" id="confirmSlotPaymentBtn">Xác nhận thanh toán</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const slotPaymentModal = document.getElementById('slotPaymentModal');
  const confirmSlotPaymentBtn = document.getElementById('confirmSlotPaymentBtn');
  const slotPaymentMethodSelect = document.getElementById('slotPaymentMethod');
  const slotPaymentNoteInput = document.getElementById('slotPaymentNote');
  const slotDirectConfirmButtons = document.querySelectorAll('.slot-direct-confirm-btn');
  const slotDetailModalEl = document.getElementById('slotDetailModal');
  const slotDetailButtons = document.querySelectorAll('.slot-detail-btn');
  const slotDetailFields = {
    slotLabel: document.getElementById('slotDetailSlotLabel'),
    sinhVien: document.getElementById('slotDetailSinhVien'),
    amountLabel: document.getElementById('slotDetailAmountLabel'),
    amount: document.getElementById('slotDetailAmount'),
    status: document.getElementById('slotDetailStatus'),
    method: document.getElementById('slotDetailMethod'),
    clientRequest: document.getElementById('slotDetailClientRequest'),
    confirmedAt: document.getElementById('slotDetailConfirmedAt'),
    clientNote: document.getElementById('slotDetailClientNote'),
    adminNote: document.getElementById('slotDetailAdminNote'),
  };
  const slotDetailImageWrapper = document.getElementById('slotDetailImageWrapper');
  const slotDetailImage = document.getElementById('slotDetailImage');
  const slotDetailImageLink = document.getElementById('slotDetailImageLink');
  let currentSlotPaymentId = null;
  let currentSlotPaymentAction = 'admin_confirm';
  let currentSlotPaymentUrl = null;
  const slotPaymentDebugLabel = '[SlotPaymentModal][Admin]';
  const hoaDonId = {{ $hoaDon->id }};
  const csrfMeta = document.querySelector('meta[name="csrf-token"]');
  const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '{{ csrf_token() }}';

  const setSlotDetailText = (field, value, fallback = 'Chưa cập nhật') => {
    if (!field) return;
    const displayValue = value && value.trim() ? value : fallback;
    field.textContent = displayValue;
  };

  const renderSlotDetailModal = (button) => {
    if (!button || !slotDetailModalEl) return;

    setSlotDetailText(slotDetailFields.slotLabel, button.getAttribute('data-slot-label') || '-');
    setSlotDetailText(slotDetailFields.sinhVien, button.getAttribute('data-sinh-vien') || '-');
    setSlotDetailText(
      slotDetailFields.amountLabel,
      button.getAttribute('data-amount-label') || 'Tiền phòng',
      'Tiền phòng'
    );
    setSlotDetailText(slotDetailFields.amount, button.getAttribute('data-tien-phong') || '-');
    setSlotDetailText(slotDetailFields.status, button.getAttribute('data-status-text') || '-');
    setSlotDetailText(slotDetailFields.method, button.getAttribute('data-payment-method-text') || '');
    setSlotDetailText(slotDetailFields.clientRequest, button.getAttribute('data-client-requested-at') || '');
    setSlotDetailText(slotDetailFields.confirmedAt, button.getAttribute('data-confirmed-at') || '');
    setSlotDetailText(slotDetailFields.clientNote, button.getAttribute('data-client-note') || '');
    setSlotDetailText(slotDetailFields.adminNote, button.getAttribute('data-admin-note') || '');

    const transferImage = button.getAttribute('data-transfer-image');
    if (transferImage && slotDetailImageWrapper && slotDetailImage && slotDetailImageLink) {
      slotDetailImageWrapper.classList.remove('d-none');
      slotDetailImage.src = transferImage;
      slotDetailImage.alt = `Ảnh chuyển khoản ${button.getAttribute('data-slot-label') || ''}`;
      slotDetailImageLink.href = transferImage;
    } else if (slotDetailImageWrapper) {
      slotDetailImageWrapper.classList.add('d-none');
      if (slotDetailImage) slotDetailImage.removeAttribute('src');
      if (slotDetailImageLink) slotDetailImageLink.removeAttribute('href');
    }
  };

  const showSlotDetailModal = () => {
    if (!slotDetailModalEl) return;
    try {
      if (window.bootstrap?.Modal?.getOrCreateInstance) {
        window.bootstrap.Modal.getOrCreateInstance(slotDetailModalEl).show();
        return;
      }
    } catch (err) {
      console.warn('[SlotDetailModal]', 'Bootstrap show failed', err);
    }

    if (window.jQuery && typeof window.jQuery.fn.modal === 'function') {
      window.jQuery(slotDetailModalEl).modal('show');
      return;
    }

    slotDetailModalEl.classList.add('show');
    slotDetailModalEl.style.display = 'block';
  };

  const handleModalShow = (event) => {
    const button = event.relatedTarget || event.target;
    if (!button) {
      console.warn(slotPaymentDebugLabel, 'Missing trigger button on show event');
      return;
    }

    currentSlotPaymentId = button.getAttribute('data-slot-payment-id');
    currentSlotPaymentUrl = button.getAttribute('data-slot-url');
    const slotLabel = button.getAttribute('data-slot-label');
    const sinhVien = button.getAttribute('data-sinh-vien');
    currentSlotPaymentAction = button.getAttribute('data-action') || 'admin_confirm';

    console.debug(slotPaymentDebugLabel, 'Open modal', {
      slotPaymentId: currentSlotPaymentId,
      targetUrl: currentSlotPaymentUrl,
      action: currentSlotPaymentAction,
      slotLabel,
      sinhVien,
    });

    document.getElementById('modalSlotLabel').textContent = slotLabel || 'Không xác định';
    document.getElementById('modalSinhVien').textContent = sinhVien || 'Không xác định';
    
    // Reset form
    if (slotPaymentMethodSelect) slotPaymentMethodSelect.value = '';
    if (slotPaymentNoteInput) slotPaymentNoteInput.value = '';
  };

  const hideModal = () => {
    try {
      if (window.bootstrap?.Modal?.getInstance) {
        const instance = window.bootstrap.Modal.getInstance(slotPaymentModal) || new window.bootstrap.Modal(slotPaymentModal);
        instance.hide();
        return;
      }
    } catch (err) {
      console.warn(slotPaymentDebugLabel, 'Bootstrap 5 modal hide failed', err);
    }

    if (window.jQuery && typeof window.jQuery.fn.modal === 'function') {
      window.jQuery(slotPaymentModal).modal('hide');
      return;
    }

    slotPaymentModal?.classList.remove('show');
  };

  const submitSlotPayment = ({
    slotPaymentId,
    targetUrl,
    paymentMethod,
    note = '',
    action = 'admin_confirm',
    successMessage = '✅ Đã cập nhật trạng thái slot!',
    onSuccess,
  }) => {
    if (!paymentMethod) {
      alert('⚠️ Vui lòng chọn hình thức thanh toán!');
      return;
    }

    if (!slotPaymentId) {
      alert('⚠️ Lỗi: Không xác định được slot thanh toán!');
      return;
    }

    const resolvedUrl = targetUrl || `/admin/hoadon/${hoaDonId}/slot-payment/${slotPaymentId}`;

    console.debug(slotPaymentDebugLabel, 'Submit request', {
      targetUrl: resolvedUrl,
      slotPaymentId,
      action,
      paymentMethod,
    });

    fetch(resolvedUrl, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        action,
        hinh_thuc_thanh_toan: paymentMethod,
        ghi_chu: note
      })
    })
    .then(async res => {
      const rawText = await res.text();
      let data = null;

      if (rawText) {
        try {
          data = JSON.parse(rawText);
        } catch (parseErr) {
          console.error(slotPaymentDebugLabel, 'Response is not JSON', rawText);
        }
      }

      if (!res.ok || !data || !data.success) {
        const message = data?.message || rawText || `HTTP ${res.status}`;
        throw new Error(message);
      }

      return data;
    })
    .then(data => {
      console.debug(slotPaymentDebugLabel, 'Success', data);
      if (typeof onSuccess === 'function') {
        onSuccess(data);
      }
      alert(successMessage);
      setTimeout(() => location.reload(), 500);
    })
    .catch(err => {
      console.error(slotPaymentDebugLabel, 'Slot payment failed', err);
      alert('❌ Không thể gửi yêu cầu: ' + (err?.message || 'Không xác định'));
    });
  };

  // Khi mở modal, lấy thông tin slot
  if (slotPaymentModal) {
    slotPaymentModal.addEventListener?.('show.bs.modal', handleModalShow);

    if (window.jQuery) {
      window.jQuery(slotPaymentModal).on('show.bs.modal', function(e) {
        handleModalShow(e);
      });
    }
  }

  // Xử lý thanh toán slot bằng modal
  if (confirmSlotPaymentBtn) {
    confirmSlotPaymentBtn.addEventListener('click', function() {
      const paymentMethod = slotPaymentMethodSelect ? slotPaymentMethodSelect.value : '';
      const note = slotPaymentNoteInput ? slotPaymentNoteInput.value : '';

      submitSlotPayment({
        slotPaymentId: currentSlotPaymentId,
        targetUrl: currentSlotPaymentUrl,
        paymentMethod,
        note,
        action: currentSlotPaymentAction,
        onSuccess: hideModal,
      });
    });
  }

  // Xử lý nút xác nhận nhanh (không mở modal)
  if (slotDirectConfirmButtons?.length) {
    slotDirectConfirmButtons.forEach(button => {
      button.addEventListener('click', () => {
        const slotPaymentId = button.getAttribute('data-slot-payment-id');
        const targetUrl = button.getAttribute('data-slot-url');
        const slotLabel = button.getAttribute('data-slot-label') || 'Không xác định';
        const sinhVien = button.getAttribute('data-sinh-vien') || 'Không rõ';
        const paymentMethod = button.getAttribute('data-slot-payment-method') || 'chuyen_khoan';

        const confirmMessage = `Xác nhận đã nhận tiền slot ${slotLabel} (${sinhVien})?`;
        if (!window.confirm(confirmMessage)) {
          return;
        }

        submitSlotPayment({
          slotPaymentId,
          targetUrl,
          paymentMethod,
          note: 'Xác nhận nhanh bởi BQL',
          action: 'admin_confirm',
          successMessage: '✅ Đã xác nhận slot!',
        });
      });
    });
  }

  if (slotDetailButtons?.length) {
    slotDetailButtons.forEach(button => {
      button.addEventListener('click', () => {
        renderSlotDetailModal(button);
        showSlotDetailModal();
      });
    });
  }
});
</script>
@endsection
