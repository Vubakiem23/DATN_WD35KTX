@extends('client.layouts.app')

@section('title', 'Hóa đơn')

@php
  use Illuminate\Support\Facades\Storage;
@endphp

@push('styles')
<style>
/* Header */
.page-header-dark {
    background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
    border-radius: 15px;
    box-shadow: 0 6px 20px rgba(26, 35, 126, 0.4);
    margin-bottom: 30px;
    overflow: hidden;
}

.page-header-dark h4 {
    font-size: 20px;
    letter-spacing: 0.5px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

/* Card Styles */
.card {
    border: none;
    border-radius: 15px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15) !important;
}

.card-header {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
    border-radius: 15px 15px 0 0 !important;
    padding: 20px 25px;
    box-shadow: 0 2px 10px rgba(23, 162, 184, 0.2);
    color: #fff;
}

.card-header h5, .card-header .fw-semibold {
    font-size: 18px;
    font-weight: 700;
    letter-spacing: 0.3px;
    color: #fff;
}

.card-body {
    padding: 30px;
    background: #ffffff;
}

/* Bill Summary */
.bill-hero {
    background: #fefefe;
}

.bill-summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
    gap: 1rem;
}

.bill-summary__item {
    border: 1px solid #eef2f7;
    border-radius: 16px;
    padding: 1.25rem;
    background: linear-gradient(135deg, #f9fbff 0%, #f0f4ff 100%);
    min-height: 120px;
    transition: all 0.3s ease;
}

.bill-summary__item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
}

.bill-summary__label {
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-size: 0.75rem;
    color: #6c757d;
}

.bill-summary__value {
    font-size: 1.5rem;
    font-weight: 600;
    margin: 0;
}

.bill-highlight {
    background: #f3f6ff;
    border: 1px dashed #c8d7ff;
    border-radius: 16px;
    padding: 1rem 1.25rem;
}

/* Status Pills */
.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.85rem;
    border-radius: 999px;
    font-weight: 600;
}

.status-pill.success {
    background: linear-gradient(135deg, #e8f8ef 0%, #d4edda 100%);
    color: #1a7f4b;
}

.status-pill.warning {
    background: linear-gradient(135deg, #fff6e5 0%, #ffeeba 100%);
    color: #c68a04;
}

.bill-note {
    font-weight: 500;
}

/* Table Styles - Giống trang bảo trì */
.table {
    margin-bottom: 0;
    border-radius: 0;
}

.table thead {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.table th {
    font-weight: 700;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #495057;
    padding: 16px 12px;
    border-bottom: 2px solid #dee2e6;
}

.table td {
    font-size: 0.9rem;
    vertical-align: middle;
    padding: 16px 12px;
    border-bottom: 1px solid #f0f0f0;
}

.table tbody tr {
    transition: all 0.3s ease;
}

.table tbody tr:hover {
    background: linear-gradient(90deg, rgba(23, 162, 184, 0.03), transparent);
    transform: translateX(3px);
}

.table tbody tr:last-child td {
    border-bottom: none;
}

.table-modern thead th {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 2px solid #dee2e6;
    text-transform: uppercase;
    font-size: 0.875rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    color: #495057;
    padding: 16px 12px;
}

.table-modern tbody tr {
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.3s ease;
}

.table-modern tbody tr:hover {
    background: linear-gradient(90deg, rgba(23, 162, 184, 0.03), transparent);
    transform: translateX(3px);
}

.table-modern tbody tr:last-child {
    border-bottom: none;
}

/* Badge Styles */
.badge {
    padding: 8px 16px;
    font-size: 0.8rem;
    font-weight: 600;
    border-radius: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    letter-spacing: 0.3px;
}

.badge.bg-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
}

.badge.bg-warning {
    background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%) !important;
    color: #212529 !important;
}

.badge.bg-info {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
}

.badge.bg-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%) !important;
}

/* Button Styles */
.btn {
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.btn-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
    border: none;
}

.btn-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
}

/* Responsive */
@media (max-width: 991px) {
    .page-header-dark {
        margin-bottom: 20px;
        border-radius: 12px;
    }

    .page-header-dark h4 {
        font-size: 16px;
    }

    .card-body {
        padding: 20px;
    }
}

@media (max-width: 768px) {
    .card-header h5, .card-header .fw-semibold {
        font-size: 16px;
    }

    .table th,
    .table td {
        font-size: 0.8rem;
        padding: 12px 8px;
    }

    .table th {
        font-size: 0.75rem;
    }

    .badge {
        padding: 6px 12px;
        font-size: 0.75rem;
    }
}
</style>
@endpush

@section('content')
<!-- Header màu xanh teal -->
<div class="page-header-dark mb-4">
    <div class="d-flex justify-content-center align-items-center py-4 px-4">
        <h4 class="mb-0 text-white fw-bold">
            @if($tab === 'tien-phong')
                <i class="fas fa-home me-2"></i>
                Hóa đơn tiền phòng
            @else
                <i class="fas fa-bolt me-2"></i>
                Hóa đơn điện nước
            @endif
        </h4>
    </div>
</div>

<div class="container py-4">
  <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
    <div class="d-flex justify-content-end mb-3">
    @if($tab === 'tien-phong')
        <a href="{{ route('client.hoadon.lichsu.tienphong') }}" 
           class="btn btn-sm btn-primary">
            <i class="fa fa-history"></i> Lịch sử tiền phòng
        </a>
    @elseif($tab === 'dien-nuoc')
        <a href="{{ route('client.hoadon.lichsu.diennuoc') }}" 
           class="btn btn-sm btn-primary">
            <i class="fa fa-history"></i> Lịch sử điện · nước
        </a>
    @endif
</div>
    <div>
      <h2 class="fw-bold mb-1">Hóa đơn ký túc xá</h2>
      <p class="text-muted mb-0">
        @if($phong)
          Phòng {{ $phong->ten_phong }} · Khu {{ optional($phong->khu)->ten_khu }}
        @else
          Bạn chưa được gán phòng
        @endif
      </p>
    </div>

    @if($hoaDons->isNotEmpty())
    <form method="GET" action="{{ url()->current() }}" class="d-flex gap-2">
      <input type="hidden" name="tab" value="{{ $tab }}">
      <select class="form-select" name="hoa_don_id" onchange="this.form.submit()">
        @foreach($hoaDons as $hoaDon)
          <option value="{{ $hoaDon->id }}" {{ optional($selectedHoaDon)->id === $hoaDon->id ? 'selected' : '' }}>
            Tháng {{ \Carbon\Carbon::parse($hoaDon->created_at)->format('m/Y') }}
          </option>
        @endforeach
      </select>
    </form>
    @endif
  </div>

  @if(isset($message))
    <div class="alert alert-info">{{ $message }}</div>
  @endif

  @if(!$phong)
    <div class="alert alert-warning mb-0">
      Bạn chưa được gán phòng. Liên hệ ban quản lý để được cấp phòng trước khi xem hóa đơn.
    </div>
  @elseif(!$selectedHoaDon)
    <div class="alert alert-info mb-0">
      Hiện chưa có hóa đơn nào được gửi cho phòng của bạn.
    </div>
  @else
    @php
      $studentRentBreakdown = $selectedHoaDon->student_breakdown ?? null;
      $studentUtilitiesBreakdown = $selectedHoaDon->student_utilities_breakdown ?? null;
      $studentRentPayment = $selectedHoaDon->student_payment ?? null;
      $studentUtilitiesPayment = $selectedHoaDon->student_utilities_payment ?? null;
      $showPersonalUtilities = $tab === 'dien-nuoc';
      $showPersonalRent = $tab === 'tien-phong';
      $activeBreakdown = $showPersonalUtilities ? $studentUtilitiesBreakdown : $studentRentBreakdown;
      $studentBreakdown = $activeBreakdown;
      $activePayment = $showPersonalUtilities ? $studentUtilitiesPayment : $studentRentPayment;

      if ($showPersonalUtilities) {
        $clientsTienDien = $activeBreakdown['tien_dien'] ?? 0;
        $clientsTienNuoc = $activeBreakdown['tien_nuoc'] ?? 0;
        $clientsTienPhong = 0;
      } elseif ($showPersonalRent) {
        $clientsTienDien = 0;
        $clientsTienNuoc = 0;
        $clientsTienPhong = $activeBreakdown['tien_phong'] ?? 0;
      } else {
        $clientsTienDien = $selectedHoaDon->tien_dien ?? 0;
        $clientsTienNuoc = $selectedHoaDon->tien_nuoc ?? 0;
        $clientsTienPhong = $selectedHoaDon->tien_phong_slot ?? 0;
      }

      $slotInfoLabel = optional($activePayment)->slot_label ?? ($activeBreakdown['label'] ?? null);
      $slotStudentName = $activeBreakdown['sinh_vien']
        ?? optional(optional($activePayment)->sinhVien)->ho_ten
        ?? optional($activePayment)->sinh_vien_ten;
      $studentSlotLabel = $slotInfoLabel;
      $shouldShowOnlyStudentSlot = $showPersonalUtilities || $showPersonalRent;
      $clientsTongCong = $clientsTienDien + $clientsTienNuoc + $clientsTienPhong;
      $sentAt = $tab === 'tien-phong'
        ? $selectedHoaDon->sent_to_client_at
        : $selectedHoaDon->sent_dien_nuoc_at;
      $nextPaymentDate = optional($sentAt, function ($date) {
        return $date->copy()->addMonthNoOverflow();
      });
      $utilitiesPaymentsMap = collect($selectedHoaDon->utilitiesPaymentsMap ?? []);
    @endphp

    @php
      $rentStatusMeta = $selectedHoaDon->da_thanh_toan
        ? ['class' => 'status-pill success', 'label' => $selectedHoaDon->trang_thai ?? 'Đã thanh toán']
        : ['class' => 'status-pill warning', 'label' => $selectedHoaDon->trang_thai ?? 'Chưa thanh toán'];
      $utilitiesStatusMeta = $selectedHoaDon->da_thanh_toan_dien_nuoc
        ? ['class' => 'status-pill success', 'label' => 'Đã thanh toán']
        : ['class' => 'status-pill warning', 'label' => 'Chưa thanh toán'];
      $studentSlotStatusMeta = null;
      if ($shouldShowOnlyStudentSlot && $activePayment) {
        $studentSlotPayment = $activePayment;
        $studentSlotStatus = $studentSlotPayment->trang_thai
          ?? ($studentSlotPayment->da_thanh_toan ? 'da_thanh_toan' : 'chua_thanh_toan');
        $studentSlotStatusMeta = match($studentSlotStatus) {
          'da_thanh_toan' => ['class' => 'status-pill success', 'label' => 'Đã thanh toán'],
          'cho_xac_nhan' => ['class' => 'status-pill warning', 'label' => 'Chờ xác nhận'],
          default => ['class' => 'status-pill warning', 'label' => 'Chưa thanh toán'],
        };
      }
      $headlineStatusMeta = $tab === 'tien-phong'
        ? ($studentSlotStatusMeta ?? $rentStatusMeta)
        : ($studentSlotStatusMeta ?? $utilitiesStatusMeta);
      $monthLabel = optional($selectedHoaDon->created_at)->format('m/Y');
    @endphp

    <div class="card bill-hero shadow-sm mb-4 border-0">
      <div class="card-body">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-3">
          <div>
            <p class="text-muted fw-semibold text-uppercase small mb-1">Thông tin hóa đơn</p>
            <h3 class="fw-bold mb-1">Hóa đơn tháng {{ $monthLabel }}</h3>
            <div class="d-flex flex-wrap gap-2">
              <span class="badge rounded-pill bg-light text-dark">
                Phòng {{ $phong->ten_phong }} · Khu {{ optional($phong->khu)->ten_khu }}
              </span>
              <span class="badge rounded-pill bg-primary-subtle text-primary">
                {{ $shouldShowOnlyStudentSlot ? 'Slot cá nhân' : ($selectedHoaDon->slot_billing_count ?? 0).' slot' }}
              </span>
            </div>
          </div>
          <div class="text-lg-end">
            <p class="text-muted small mb-1">Trạng thái</p>
            <span class="{{ $headlineStatusMeta['class'] }}">
              {{ $headlineStatusMeta['label'] }}
            </span>
            <div class="small text-muted mt-2">
              {{ $tab === 'tien-phong'
                ? ($selectedHoaDon->sent_to_client_at ? 'Gửi SV: '.$selectedHoaDon->sent_to_client_at->format('d/m/Y H:i') : 'Chưa gửi SV')
                : ($selectedHoaDon->sent_dien_nuoc_at ? 'Gửi SV: '.$selectedHoaDon->sent_dien_nuoc_at->format('d/m/Y H:i') : 'Chưa gửi SV')
              }}
            </div>
            <div class="small mt-2 {{ $nextPaymentDate ? 'text-primary fw-semibold' : 'text-muted' }}">
              @if($nextPaymentDate)
                Hạn thanh toán tiếp theo: {{ $nextPaymentDate->format('d/m/Y') }}
              @else
                Chưa có lịch thanh toán tiếp theo
              @endif
            </div>
          </div>
        </div>

        <div class="bill-summary-grid mt-4">
          <div class="bill-summary__item">
            <p class="bill-summary__label">Tháng</p>
            <p class="bill-summary__value">{{ $monthLabel }}</p>
          </div>
          @if($shouldShowOnlyStudentSlot)
            <div class="bill-summary__item">
              <p class="bill-summary__label">Slot</p>
              <p class="bill-summary__value mb-1">{{ $slotInfoLabel ?? '—' }}</p>
              <p class="text-muted small mb-0">{{ $slotStudentName ?? 'Chưa xác định' }}</p>
            </div>
          @else
            <div class="bill-summary__item">
              <p class="bill-summary__label">Số slot tính phí</p>
              <p class="bill-summary__value">{{ $selectedHoaDon->slot_billing_count ?? 0 }}</p>
          </div>
          @endif

          @if($tab === 'dien-nuoc')
            <div class="bill-summary__item">
              <p class="bill-summary__label">Tiền điện</p>
              <p class="bill-summary__value text-danger">{{ number_format($clientsTienDien, 0, ',', '.') }} VND</p>
              @if($selectedHoaDon && ($selectedHoaDon->so_dien_cu !== null || $selectedHoaDon->so_dien_moi !== null))
                <div class="small text-muted mt-1">
                  <span>Điện cũ: {{ number_format($selectedHoaDon->so_dien_cu ?? 0, 0, ',', '.') }}</span>
                  <span class="mx-1">|</span>
                  <span>Điện mới: {{ number_format($selectedHoaDon->so_dien_moi ?? 0, 0, ',', '.') }}</span>
                </div>
              @endif
              </div>
            <div class="bill-summary__item">
              <p class="bill-summary__label">Tiền nước</p>
              <p class="bill-summary__value text-primary">{{ number_format($clientsTienNuoc, 0, ',', '.') }} VND</p>
              @if($selectedHoaDon && ($selectedHoaDon->so_nuoc_cu !== null || $selectedHoaDon->so_nuoc_moi !== null))
                <div class="small text-muted mt-1">
                  <span>Nước cũ: {{ number_format($selectedHoaDon->so_nuoc_cu ?? 0, 0, ',', '.') }}</span>
                  <span class="mx-1">|</span>
                  <span>Nước mới: {{ number_format($selectedHoaDon->so_nuoc_moi ?? 0, 0, ',', '.') }}</span>
                </div>
              @endif
                </div>
          @elseif($tab === 'tien-phong')
            <div class="bill-summary__item">
              <p class="bill-summary__label">Tiền phòng</p>
              <p class="bill-summary__value text-success">{{ number_format($clientsTienPhong, 0, ',', '.') }} VND</p>
                </div>
              @else
            <div class="bill-summary__item">
              <p class="bill-summary__label">Tiền điện</p>
              <p class="bill-summary__value text-danger">{{ number_format($clientsTienDien, 0, ',', '.') }} VND</p>
                @if($selectedHoaDon && ($selectedHoaDon->so_dien_cu !== null || $selectedHoaDon->so_dien_moi !== null))
                <div class="small text-muted mt-1">
                  <span>Điện cũ: {{ number_format($selectedHoaDon->so_dien_cu ?? 0, 0, ',', '.') }}</span>
                  <span class="mx-1">|</span>
                  <span>Điện mới: {{ number_format($selectedHoaDon->so_dien_moi ?? 0, 0, ',', '.') }}</span>
                </div>
              @endif
                </div>
            <div class="bill-summary__item">
              <p class="bill-summary__label">Tiền nước</p>
              <p class="bill-summary__value text-primary">{{ number_format($clientsTienNuoc, 0, ',', '.') }} VND</p>
              @if($selectedHoaDon && ($selectedHoaDon->so_nuoc_cu !== null || $selectedHoaDon->so_nuoc_moi !== null))
                <div class="small text-muted mt-1">
                  <span>Nước cũ: {{ number_format($selectedHoaDon->so_nuoc_cu ?? 0, 0, ',', '.') }}</span>
                  <span class="mx-1">|</span>
                  <span>Nước mới: {{ number_format($selectedHoaDon->so_nuoc_moi ?? 0, 0, ',', '.') }}</span>
                </div>
              @endif
                </div>
            <div class="bill-summary__item">
              <p class="bill-summary__label">Tiền phòng</p>
              <p class="bill-summary__value text-success">{{ number_format($clientsTienPhong, 0, ',', '.') }} VND</p>
            </div>
          @endif

          <div class="bill-summary__item">
            <p class="bill-summary__label">Tổng cộng</p>
            <p class="bill-summary__value">{{ number_format($clientsTongCong, 0, ',', '.') }} VND</p>
          </div>
              </div>

        <div class="bill-highlight mt-4">
              @if($tab === 'tien-phong')
          @if($selectedHoaDon->student_payment && $selectedHoaDon->student_payment->client_ghi_chu)
            <p class="text-muted small mb-0">Ghi chú: {{ $selectedHoaDon->student_payment->client_ghi_chu }}</p>
                    @else
            <p class="text-muted mb-0">Chưa có ghi chú.</p>
                  @endif
                @else
            <p class="text-muted mb-0">
              Chi phí điện · nước được tính chung cho phòng. Hãy phối hợp với trưởng phòng để hoàn tất việc nộp phí.
            </p>
              @endif
        </div>
      </div>
    </div>

    @if($tab === 'tien-phong')
      <div class="card shadow-sm mb-4">
        <div class="card-header fw-semibold text-uppercase">Chi tiết tiền phòng</div>
        <div class="card-body">
          @if(!empty($selectedHoaDon->slot_breakdowns))
            <div class="table-responsive">
              <table class="table table-striped table-modern text-center align-middle">
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
                  @php $hasVisibleRows = false; @endphp
                  @foreach($selectedHoaDon->slot_breakdowns as $slot)
                    @php
                      $slotPayment = $selectedHoaDon->slotPaymentsMap[$slot['label']] ?? null;
                      $isMine = $slotPayment && $slotPayment->sinh_vien_id === optional($sinhVien)->id;
                      $slotStatus = $slotPayment
                        ? ($slotPayment->trang_thai ?? ($slotPayment->da_thanh_toan ? 'da_thanh_toan' : 'chua_thanh_toan'))
                        : 'chua_thanh_toan';
                      $statusMeta = match($slotStatus) {
                        'da_thanh_toan' => ['class' => 'badge bg-success', 'text' => 'Đã thanh toán', 'icon' => 'fa-check-circle'],
                        'cho_xac_nhan' => ['class' => 'badge bg-info text-dark', 'text' => 'Chờ xác nhận', 'icon' => 'fa-hourglass-half'],
                        default => ['class' => 'badge bg-warning text-dark', 'text' => 'Chưa thanh toán', 'icon' => 'fa-clock'],
                      };
                    @endphp
                    @if(!$isMine)
                      @continue
                    @endif
                    @php
                      $hasVisibleRows = true;
                      $paymentMethodText = $slotPayment?->hinh_thuc_thanh_toan === 'chuyen_khoan'
                        ? 'Chuyển khoản'
                        : ($slotPayment?->hinh_thuc_thanh_toan === 'tien_mat' ? 'Tiền mặt' : null);
                      $clientRequestedText = $slotPayment?->client_requested_at
                        ? \Carbon\Carbon::parse($slotPayment->client_requested_at)->format('d/m/Y H:i')
                        : null;
                      $confirmedAtText = $slotPayment?->ngay_thanh_toan
                        ? \Carbon\Carbon::parse($slotPayment->ngay_thanh_toan)->format('d/m/Y H:i')
                        : null;
                      $transferImageUrl = ($slotPayment && $slotPayment->client_transfer_image_path)
                        ? \Illuminate\Support\Facades\Storage::url($slotPayment->client_transfer_image_path)
                        : null;
                    @endphp
                    @php
                      $paymentMethodText = $slotPayment?->hinh_thuc_thanh_toan === 'chuyen_khoan'
                        ? 'Chuyển khoản'
                        : ($slotPayment?->hinh_thuc_thanh_toan === 'tien_mat' ? 'Tiền mặt' : null);
                      $clientRequestedText = $slotPayment?->client_requested_at
                        ? \Carbon\Carbon::parse($slotPayment->client_requested_at)->format('d/m/Y H:i')
                        : null;
                      $confirmedAtText = $slotPayment?->ngay_thanh_toan
                        ? \Carbon\Carbon::parse($slotPayment->ngay_thanh_toan)->format('d/m/Y H:i')
                        : null;
                      $transferImageUrl = ($slotPayment && $slotPayment->client_transfer_image_path)
                        ? \Illuminate\Support\Facades\Storage::url($slotPayment->client_transfer_image_path)
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
                        @if($slotPayment && $slotPayment->client_requested_at && $slotStatus === 'cho_xac_nhan')
                          <div class="small text-muted mt-1">
                            Gửi lúc {{ \Carbon\Carbon::parse($slotPayment->client_requested_at)->format('d/m/Y H:i') }}
                          </div>
                        @endif
                        @if($slotPayment && $slotPayment->client_ghi_chu)
                          <div class="small text-muted mt-1">
                            Ghi chú: {{ $slotPayment->client_ghi_chu }}
                          </div>
                        @endif
                      </td>
                      <td>
                        @if($slotPayment && $isMine)
                          @if($slotStatus === 'chua_thanh_toan')
                            <button type="button"
                              class="btn btn-sm btn-primary"
                              data-bs-toggle="modal"
                              data-bs-target="#slotPaymentModal"
                              data-slot-payment-id="{{ $slotPayment->id }}"
                              data-slot-url="{{ route('client.hoadon.slotpayment', ['hoaDonId' => $selectedHoaDon->id, 'slotPaymentId' => $slotPayment->id]) }}"
                              data-slot-label="{{ $slot['label'] }}"
                              data-sinh-vien="{{ $slot['sinh_vien'] }}"
                              data-action="student_submit">
                              <i class="fa fa-money-bill"></i> Thanh toán
                            </button>
                          @elseif($slotStatus === 'cho_xac_nhan')
                            <div class="d-flex flex-column gap-2">
                              <button type="button"
                                class="btn btn-sm btn-outline-secondary slot-detail-btn"
                                data-slot-label="{{ $slot['label'] }}"
                                data-sinh-vien="{{ $slot['sinh_vien'] }}"
                                data-amount-label="Tiền phòng"
                                data-amount="{{ number_format($slot['tien_phong'] ?? 0, 0, ',', '.') }} VND"
                                data-status-text="{{ $statusMeta['text'] }}"
                                data-payment-method-text="{{ $paymentMethodText ?? '' }}"
                                data-client-requested-at="{{ $clientRequestedText ?? '' }}"
                                data-confirmed-at="{{ $confirmedAtText ?? '' }}"
                                data-client-note="{{ $slotPayment->client_ghi_chu ?? '' }}"
                                data-admin-note="{{ $slotPayment->ghi_chu ?? '' }}"
                                data-transfer-image="{{ $transferImageUrl ?? '' }}">
                                <i class="fa fa-info-circle"></i> Xem chi tiết
                              </button>
                            <span class="text-muted small">Đang chờ xác nhận</span>
                            </div>
                          @elseif($slotStatus === 'da_thanh_toan')
                            <button type="button"
                              class="btn btn-sm btn-outline-secondary slot-detail-btn"
                              data-slot-label="{{ $slot['label'] }}"
                              data-sinh-vien="{{ $slot['sinh_vien'] }}"
                              data-amount-label="Tiền phòng"
                              data-amount="{{ number_format($slot['tien_phong'] ?? 0, 0, ',', '.') }} VND"
                              data-status-text="{{ $statusMeta['text'] }}"
                              data-payment-method-text="{{ $paymentMethodText ?? '' }}"
                              data-client-requested-at="{{ $clientRequestedText ?? '' }}"
                              data-confirmed-at="{{ $confirmedAtText ?? '' }}"
                              data-client-note="{{ $slotPayment->client_ghi_chu ?? '' }}"
                              data-admin-note="{{ $slotPayment->ghi_chu ?? '' }}"
                              data-transfer-image="{{ $transferImageUrl ?? '' }}">
                              <i class="fa fa-info-circle"></i> Xem chi tiết
                            </button>
                          @else
                            <span class="text-muted small">Đã hoàn tất</span>
                          @endif
                        @elseif($slotPayment && $slotStatus === 'da_thanh_toan')
                          <button type="button"
                            class="btn btn-sm btn-outline-secondary slot-detail-btn"
                            data-slot-label="{{ $slot['label'] }}"
                            data-sinh-vien="{{ $slot['sinh_vien'] }}"
                            data-amount-label="Tiền phòng"
                            data-amount="{{ number_format($slot['tien_phong'] ?? 0, 0, ',', '.') }} VND"
                            data-status-text="{{ $statusMeta['text'] }}"
                            data-payment-method-text="{{ $paymentMethodText ?? '' }}"
                            data-client-requested-at="{{ $clientRequestedText ?? '' }}"
                            data-confirmed-at="{{ $confirmedAtText ?? '' }}"
                            data-client-note="{{ $slotPayment->client_ghi_chu ?? '' }}"
                            data-admin-note="{{ $slotPayment->ghi_chu ?? '' }}"
                            data-transfer-image="{{ $transferImageUrl ?? '' }}">
                            <i class="fa fa-info-circle"></i> Xem chi tiết
                          </button>
                        @else
                          <span class="text-muted small">Không khả dụng</span>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                  @if(!$hasVisibleRows)
                    <tr>
                      <td colspan="5" class="text-muted">Không tìm thấy dữ liệu slot của bạn.</td>
                    </tr>
                  @endif
                </tbody>
              </table>
            </div>
          @else
            <p class="text-muted mb-0">Chưa có dữ liệu slot cho tiền phòng.</p>
          @endif
        </div>
      </div>
    @else
      <div class="card shadow-sm mb-4">
        <div class="card-header fw-semibold text-uppercase">Chi tiết tiền điện · nước đã chia đều cho từng slot</div>
        <div class="card-body">
          @if(!empty($selectedHoaDon->slot_breakdowns))
            <div class="table-responsive">
              <table class="table table-striped table-modern text-center align-middle">
                <thead class="table-light">
                  <tr>
                    <th>Slot</th>
                    <th>Sinh viên</th>
                    <th>Tiền điện</th>
                    <th>Tiền nước</th>
                    <th>Tổng</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                  </tr>
                </thead>
                <tbody>
                  @php
                    $allBreakdowns = collect($selectedHoaDon->slot_breakdowns);
                    $studentRows = $studentSlotLabel
                      ? $allBreakdowns->where('label', $studentSlotLabel)
                      : collect([$studentBreakdown])->filter();
                    $sentUtilitiesAt = $selectedHoaDon->sent_dien_nuoc_at
                      ? \Carbon\Carbon::parse($selectedHoaDon->sent_dien_nuoc_at)->format('d/m/Y H:i')
                      : null;
                    $confirmedUtilitiesAt = $selectedHoaDon->ngay_thanh_toan_dien_nuoc
                      ? \Carbon\Carbon::parse($selectedHoaDon->ngay_thanh_toan_dien_nuoc)->format('d/m/Y H:i')
                      : null;
                  @endphp
                  @forelse($studentRows as $slot)
                    @php
                      $tongSlot = ($slot['tien_dien'] ?? 0) + ($slot['tien_nuoc'] ?? 0);
                      $utilitiesPayment = $utilitiesPaymentsMap->get($slot['label']) ?? null;
                      $slotStatus = $utilitiesPayment
                        ? ($utilitiesPayment->da_thanh_toan ? 'da_thanh_toan' : ($utilitiesPayment->trang_thai ?? 'chua_thanh_toan'))
                        : ($selectedHoaDon->sent_dien_nuoc_to_client ? 'chua_thanh_toan' : 'chua_thanh_toan');
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
                      $isMine = $utilitiesPayment && optional($sinhVien)->id === $utilitiesPayment->sinh_vien_id;
                    @endphp
                    <tr>
                      <td>{{ $slot['label'] }}</td>
                      <td>{{ $slot['sinh_vien'] }}</td>
                      <td>{{ number_format($slot['tien_dien'] ?? 0, 0, ',', '.') }} VND</td>
                      <td>{{ number_format($slot['tien_nuoc'] ?? 0, 0, ',', '.') }} VND</td>
                      <td class="fw-semibold">{{ number_format($tongSlot, 0, ',', '.') }} VND</td>
                      <td>
                        <span class="{{ $statusMeta['class'] }}">
                          <i class="fa {{ $statusMeta['icon'] }}"></i> {{ $statusMeta['text'] }}
                        </span>
                        @if($selectedHoaDon->da_thanh_toan_dien_nuoc && $selectedHoaDon->ngay_thanh_toan_dien_nuoc)
                          <div class="small text-muted mt-1">
                            Hóa đơn điện · nước xác nhận {{ \Carbon\Carbon::parse($selectedHoaDon->ngay_thanh_toan_dien_nuoc)->format('d/m/Y H:i') }}
                          </div>
                        @elseif(!$selectedHoaDon->sent_dien_nuoc_to_client)
                          <div class="small text-muted mt-1">Hóa đơn chưa gửi tới sinh viên</div>
                        @else
                          
                        @endif
                      </td>
                      <td>
                        @if(!$selectedHoaDon->sent_dien_nuoc_to_client)
                          <span class="text-muted small">Chưa mở thanh toán</span>
                        @elseif($utilitiesPayment && $isMine && $slotStatus === 'chua_thanh_toan')
                            <button type="button"
                              class="btn btn-sm btn-primary"
                              data-bs-toggle="modal"
                              data-bs-target="#slotPaymentModal"
                            data-slot-payment-id="{{ $utilitiesPayment->id }}"
                            data-slot-url="{{ route('client.hoadon.utilitiespayment', ['hoaDonId' => $selectedHoaDon->id, 'utilitiesPaymentId' => $utilitiesPayment->id]) }}"
                              data-slot-label="{{ $slot['label'] }}"
                              data-sinh-vien="{{ $slot['sinh_vien'] }}"
                              data-action="student_submit">
                              <i class="fa fa-money-bill"></i> Thanh toán
                            </button>
                          @elseif($slotStatus === 'cho_xac_nhan')
                            <div class="d-flex flex-column gap-2">
                              <button type="button"
                                class="btn btn-sm btn-outline-secondary slot-detail-btn"
                                data-slot-label="{{ $slot['label'] }}"
                                data-sinh-vien="{{ $slot['sinh_vien'] }}"
                                data-amount-label="Tiền điện + nước"
                                data-amount="{{ number_format($tongSlot, 0, ',', '.') }} VND"
                                data-status-text="{{ $statusMeta['text'] }}"
                                data-payment-method-text="{{ $paymentMethodText ?? 'Chưa cập nhật' }}"
                                data-client-requested-at="{{ $clientRequestedText ?? ($sentUtilitiesAt ?? '') }}"
                                data-confirmed-at="{{ $confirmedAtText ?? ($confirmedUtilitiesAt ?? '') }}"
                                data-client-note="{{ $utilitiesPayment->client_ghi_chu ?? '' }}"
                                data-admin-note="{{ $utilitiesPayment->ghi_chu ?? $selectedHoaDon->ghi_chu_thanh_toan_dien_nuoc ?? '' }}"
                                data-transfer-image="{{ $transferImageUrl ?? '' }}">
                                <i class="fa fa-info-circle"></i> Xem chi tiết
                              </button>
                            <span class="text-muted small">Đang chờ xác nhận</span>
                            </div>
                        @elseif($slotStatus === 'da_thanh_toan' && $utilitiesPayment)
                          <button type="button"
                            class="btn btn-sm btn-outline-secondary slot-detail-btn"
                            data-slot-label="{{ $slot['label'] }}"
                            data-sinh-vien="{{ $slot['sinh_vien'] }}"
                            data-amount-label="Tiền điện + nước"
                            data-amount="{{ number_format($tongSlot, 0, ',', '.') }} VND"
                            data-status-text="{{ $statusMeta['text'] }}"
                            data-payment-method-text="{{ $paymentMethodText ?? 'Chưa cập nhật' }}"
                            data-client-requested-at="{{ $clientRequestedText ?? ($sentUtilitiesAt ?? '') }}"
                            data-confirmed-at="{{ $confirmedAtText ?? ($confirmedUtilitiesAt ?? '') }}"
                            data-client-note="{{ $utilitiesPayment->client_ghi_chu ?? '' }}"
                            data-admin-note="{{ $utilitiesPayment->ghi_chu ?? $selectedHoaDon->ghi_chu_thanh_toan_dien_nuoc ?? '' }}"
                            data-transfer-image="{{ $transferImageUrl ?? '' }}">
                            <i class="fa fa-info-circle"></i> Xem chi tiết
                          </button>
                        @elseif($slotStatus === 'da_thanh_toan')
                            <span class="text-muted small">Đã hoàn tất</span>
                        @else
                          <span class="text-muted small">Không khả dụng</span>
                        @endif
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="7" class="text-muted">Không tìm thấy dữ liệu slot của bạn.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          @else
            <p class="text-muted mb-0">Chưa có dữ liệu phân bổ điện nước.</p>
          @endif
        </div>
      </div>

    @endif
  @endif
@if($selectedHoaDon)
<!-- Modal xem chi tiết slot -->
<div class="modal fade" id="slotDetailModal" tabindex="-1" aria-labelledby="slotDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-primary text-white border-0">
        <h5 class="modal-title fw-bold" id="slotDetailModalLabel">
          <i class="fa fa-info-circle me-2"></i>Chi tiết thanh toán slot
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
                    <span data-field="slot-label" class="fw-semibold text-dark">-</span>
                  </div>
                </div>
                <div class="info-item mb-3 pb-3 border-bottom">
                  <div class="d-flex align-items-center mb-1">
                    <i class="fa fa-user text-muted me-2" style="width: 20px;"></i>
                    <strong class="text-muted small">Sinh viên:</strong>
                  </div>
                  <div class="ms-4">
                    <span data-field="sinh-vien" class="fw-semibold text-dark">-</span>
                  </div>
                </div>
                <div class="info-item mb-3 pb-3 border-bottom">
                  <div class="d-flex align-items-center mb-1">
                    <i class="fa fa-money-bill-wave text-muted me-2" style="width: 20px;"></i>
                    <strong class="text-muted small"><span data-field="amount-label">Số tiền</span>:</strong>
                  </div>
                  <div class="ms-4">
                    <span data-field="amount" class="fw-bold text-success fs-6">-</span>
                  </div>
                </div>
                <div class="info-item">
                  <div class="d-flex align-items-center mb-1">
                    <i class="fa fa-check-circle text-muted me-2" style="width: 20px;"></i>
                    <strong class="text-muted small">Trạng thái:</strong>
                  </div>
                  <div class="ms-4">
                    <span data-field="status" class="badge bg-info text-dark">-</span>
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
                    <span data-field="method" class="fw-semibold text-dark">-</span>
                  </div>
                </div>
                <div class="info-item mb-3 pb-3 border-bottom">
                  <div class="d-flex align-items-center mb-1">
                    <i class="fa fa-clock text-muted me-2" style="width: 20px;"></i>
                    <strong class="text-muted small">SV gửi lúc:</strong>
                  </div>
                  <div class="ms-4">
                    <span data-field="client-request" class="text-dark">-</span>
                  </div>
                </div>
                <div class="info-item mb-3 pb-3 border-bottom">
                  <div class="d-flex align-items-center mb-1">
                    <i class="fa fa-check-double text-muted me-2" style="width: 20px;"></i>
                    <strong class="text-muted small">BQL xác nhận:</strong>
                  </div>
                  <div class="ms-4">
                    <span data-field="confirmed-at" class="text-dark">-</span>
                  </div>
                </div>
                <div class="info-item mb-3 pb-3 border-bottom">
                  <div class="d-flex align-items-center mb-1">
                    <i class="fa fa-sticky-note text-muted me-2" style="width: 20px;"></i>
                    <strong class="text-muted small">Ghi chú của bạn:</strong>
                  </div>
                  <div class="ms-4">
                    <span data-field="client-note" class="text-dark">-</span>
                  </div>
                </div>
                <div class="info-item">
                  <div class="d-flex align-items-center mb-1">
                    <i class="fa fa-comment-dots text-muted me-2" style="width: 20px;"></i>
                    <strong class="text-muted small">Phản hồi quản trị:</strong>
                  </div>
                  <div class="ms-4">
                    <span data-field="admin-note" class="text-dark">-</span>
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
                <i class="fa fa-image me-2"></i>Ảnh chuyển khoản
              </h6>
              <div class="text-center mb-3">
                <a id="slotDetailImageLink" href="#" target="_blank" class="btn btn-outline-primary btn-sm">
                  <i class="fa fa-external-link-alt me-1"></i> Mở ảnh trong tab mới
                </a>
              </div>
              <div class="text-center">
                <img id="slotDetailImage" src="" alt="Ảnh chuyển khoản" class="img-fluid rounded shadow" style="max-height: 400px; width: auto; border: 3px solid #e9ecef;">
              </div>
            </div>
          </div>
        </div>
        <div id="slotDetailNoImage" class="text-center mt-4">
          <div class="card border-0 shadow-sm bg-light">
            <div class="card-body p-4">
              <i class="fa fa-image text-muted" style="font-size: 3rem;"></i>
              <p class="text-muted mb-0 mt-2">Chưa có ảnh chuyển khoản được gửi.</p>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0 bg-light">
        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
          <i class="fa fa-times me-1"></i>Đóng
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
        <div class="mb-3" id="slotPaymentNoteWrapper">
          <label for="slotPaymentNote" class="form-label">Ghi chú</label>
          <textarea id="slotPaymentNote" class="form-control" rows="3" placeholder="Ghi chú thanh toán (tùy chọn)"></textarea>
        </div>
        <div id="slotPaymentTransferSection" class="mb-3 d-none">
          <div class="border rounded p-3 bg-light">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3">
              <div>
                <p class="mb-1"><strong>Tên tài khoản:</strong> Nguyen Quang Thắng</p>
                <p class="mb-1"><strong>Số tài khoản:</strong> T1209666</p>
                <p class="mb-1"><strong>Ngân hàng thụ hưởng:</strong> Techcombank · Chi nhánh Hà Nội</p>
                <p class="text-muted small mb-0">Ghi rõ phòng và khu trong nội dung chuyển khoản.</p>
              </div>
              <div class="text-center ms-md-3">
                <img src="{{ asset('images/maqr.jpg') }}" alt="QR chuyển khoản" class="img-fluid rounded" style="max-width: 160px;">
                <div class="small text-muted mt-2">Quét mã để chuyển khoản</div>
              </div>
            </div>
          </div>
          <div class="mt-3">
            <label for="slotPaymentTransferNote" class="form-label">Ghi chú thanh toán</label>
            <textarea id="slotPaymentTransferNote" class="form-control" rows="2" placeholder="Vui lòng nhập tên phòng-khu..."></textarea>
          </div>
          <div class="mt-3">
            <label for="slotPaymentTransferImage" class="form-label">Ảnh chứng từ chuyển khoản</label>
            <input type="file" class="form-control" id="slotPaymentTransferImage" accept="image/*">
            <small class="text-muted">Định dạng JPG, PNG · Tối đa 4MB.</small>
            <div class="mt-2 d-none" id="slotPaymentTransferPreview">
              <img src="" alt="Ảnh chuyển khoản" class="img-fluid rounded shadow-sm">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-success" id="confirmSlotPaymentBtn">Xác nhận thanh toán</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const slotPaymentModal = document.getElementById('slotPaymentModal');
  const confirmSlotPaymentBtn = document.getElementById('confirmSlotPaymentBtn');
  let currentSlotPaymentId = null;
  let currentSlotPaymentAction = 'student_submit';
  let currentSlotPaymentUrl = null;
  const slotPaymentDebugLabel = '[SlotPaymentModal][Client]';
  const hoaDonId = {{ $selectedHoaDon->id }};
  const currentTab = @json($tab);
  const selectedHoaDonId = @json(optional($selectedHoaDon)->id);
  const slotPaymentMethod = document.getElementById('slotPaymentMethod');
  const slotPaymentNote = document.getElementById('slotPaymentNote');
  const slotPaymentNoteWrapper = document.getElementById('slotPaymentNoteWrapper');
  const transferSection = document.getElementById('slotPaymentTransferSection');
  const transferNote = document.getElementById('slotPaymentTransferNote');
  const transferImageInput = document.getElementById('slotPaymentTransferImage');
  const transferPreview = document.getElementById('slotPaymentTransferPreview');
  const slotDetailButtons = document.querySelectorAll('.slot-detail-btn');
  const slotDetailModalEl = document.getElementById('slotDetailModal');
  const slotDetailFields = slotDetailModalEl ? {
    slotLabel: slotDetailModalEl.querySelector('[data-field="slot-label"]'),
    sinhVien: slotDetailModalEl.querySelector('[data-field="sinh-vien"]'),
    amountLabel: slotDetailModalEl.querySelector('[data-field="amount-label"]'),
    amount: slotDetailModalEl.querySelector('[data-field="amount"]'),
    status: slotDetailModalEl.querySelector('[data-field="status"]'),
    method: slotDetailModalEl.querySelector('[data-field="method"]'),
    clientRequest: slotDetailModalEl.querySelector('[data-field="client-request"]'),
    confirmedAt: slotDetailModalEl.querySelector('[data-field="confirmed-at"]'),
    clientNote: slotDetailModalEl.querySelector('[data-field="client-note"]'),
    adminNote: slotDetailModalEl.querySelector('[data-field="admin-note"]'),
  } : {};
  const slotDetailImageWrapper = slotDetailModalEl?.querySelector('#slotDetailImageWrapper');
  const slotDetailNoImage = slotDetailModalEl?.querySelector('#slotDetailNoImage');
  const slotDetailImage = slotDetailModalEl?.querySelector('#slotDetailImage');
  const slotDetailImageLink = slotDetailModalEl?.querySelector('#slotDetailImageLink');

  const setSlotDetailText = (field, value, fallback = 'Chưa cập nhật') => {
    if (!field) return;
    const displayValue = value && value.trim() ? value : fallback;
    field.textContent = displayValue;
  };

  const renderSlotDetailModal = (button) => {
    if (!button || !slotDetailModalEl) return;

    const amountLabel = button.getAttribute('data-amount-label') || 'Số tiền';
    setSlotDetailText(slotDetailFields.slotLabel, button.getAttribute('data-slot-label') || '-');
    setSlotDetailText(slotDetailFields.sinhVien, button.getAttribute('data-sinh-vien') || '-');
    setSlotDetailText(slotDetailFields.amountLabel, amountLabel);
    setSlotDetailText(slotDetailFields.amount, button.getAttribute('data-amount') || '-', '0 VND');
    setSlotDetailText(slotDetailFields.status, button.getAttribute('data-status-text') || '', 'Chưa xác định');
    setSlotDetailText(slotDetailFields.method, button.getAttribute('data-payment-method-text') || '', 'Chưa chọn');
    setSlotDetailText(slotDetailFields.clientRequest, button.getAttribute('data-client-requested-at') || '', 'Chưa gửi');
    setSlotDetailText(slotDetailFields.confirmedAt, button.getAttribute('data-confirmed-at') || '', 'Chưa xác nhận');
    setSlotDetailText(slotDetailFields.clientNote, button.getAttribute('data-client-note') || '', 'Không có');
    setSlotDetailText(slotDetailFields.adminNote, button.getAttribute('data-admin-note') || '', 'Không có');

    const transferImage = button.getAttribute('data-transfer-image');
    if (transferImage && slotDetailImageWrapper && slotDetailImage && slotDetailImageLink) {
      slotDetailImageWrapper.classList.remove('d-none');
      slotDetailNoImage?.classList.add('d-none');
      slotDetailImage.src = transferImage;
      slotDetailImage.alt = `Ảnh chuyển khoản ${button.getAttribute('data-slot-label') || ''}`;
      slotDetailImageLink.href = transferImage;
    } else {
      slotDetailImageWrapper?.classList.add('d-none');
      slotDetailNoImage?.classList.remove('d-none');
      slotDetailImage?.removeAttribute('src');
      slotDetailImageLink?.removeAttribute('href');
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
      console.warn('[SlotDetailModal][Client]', 'Bootstrap show failed', err);
    }

    if (window.jQuery && typeof window.jQuery.fn.modal === 'function') {
      window.jQuery(slotDetailModalEl).modal('show');
      return;
    }

    slotDetailModalEl.classList.add('show');
    slotDetailModalEl.style.display = 'block';
  };

  function toggleTransferSection(method) {
    if (!transferSection || !slotPaymentNoteWrapper) {
      return;
    }
    if (method === 'chuyen_khoan') {
      transferSection.classList.remove('d-none');
      slotPaymentNoteWrapper.classList.add('d-none');
    } else {
      transferSection.classList.add('d-none');
      slotPaymentNoteWrapper.classList.remove('d-none');
    }
  }

  function resetTransferFields(options = { clearNote: true }) {
    const shouldClearNote = options?.clearNote ?? true;
    if (transferNote && shouldClearNote) {
      transferNote.value = '';
    }
    if (transferImageInput) {
      transferImageInput.value = '';
    }
    if (transferPreview) {
      transferPreview.classList.add('d-none');
      const previewImg = transferPreview.querySelector('img');
      if (previewImg) {
        previewImg.src = '';
      }
    }
  }

  if (slotPaymentMethod) {
    slotPaymentMethod.addEventListener('change', function(event) {
      toggleTransferSection(event.target.value);
    });
  }

  if (transferImageInput && transferPreview) {
    transferImageInput.addEventListener('change', function(event) {
      const file = event.target.files?.[0];
      if (!file) {
        resetTransferFields({ clearNote: false });
        return;
      }
      const previewImg = transferPreview.querySelector('img');
      if (previewImg) {
        previewImg.src = URL.createObjectURL(file);
        transferPreview.classList.remove('d-none');
      }
    });
  }

  if (slotPaymentModal) {
    slotPaymentModal.addEventListener('show.bs.modal', function(event) {
      const button = event.relatedTarget;
      currentSlotPaymentId = button.getAttribute('data-slot-payment-id');
      const slotLabel = button.getAttribute('data-slot-label');
      const sinhVien = button.getAttribute('data-sinh-vien');
      currentSlotPaymentAction = button.getAttribute('data-action') || 'student_submit';
      currentSlotPaymentUrl = button.getAttribute('data-slot-url');

      console.debug(slotPaymentDebugLabel, 'Open modal', {
        slotPaymentId: currentSlotPaymentId,
        targetUrl: currentSlotPaymentUrl,
        action: currentSlotPaymentAction,
        slotLabel,
        sinhVien,
      });

      document.getElementById('modalSlotLabel').textContent = slotLabel;
      document.getElementById('modalSinhVien').textContent = sinhVien;

      if (slotPaymentMethod) {
        slotPaymentMethod.value = '';
        toggleTransferSection('');
      }
      if (slotPaymentNote) {
        slotPaymentNote.value = '';
      }
      resetTransferFields();
    });
  }

  if (confirmSlotPaymentBtn) {
    confirmSlotPaymentBtn.addEventListener('click', function() {
      const paymentMethod = slotPaymentMethod ? slotPaymentMethod.value : '';
      const noteValue = paymentMethod === 'chuyen_khoan'
        ? (transferNote ? transferNote.value : '')
        : (slotPaymentNote ? slotPaymentNote.value : '');

      if (!paymentMethod) {
        alert('⚠️ Vui lòng chọn hình thức thanh toán!');
        return;
      }

      if (!currentSlotPaymentId) {
        alert('⚠️ Không xác định được slot thanh toán!');
        return;
      }

      // Lấy CSRF token an toàn (tránh lỗi nếu thẻ meta không tồn tại)
      const csrfMeta = document.querySelector('meta[name="csrf-token"]');
      const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '{{ csrf_token() }}';

      const targetUrl = currentSlotPaymentUrl || `/client/hoadon/${hoaDonId}/slot-payment/${currentSlotPaymentId}`;

      const payload = new FormData();
      payload.append('action', currentSlotPaymentAction);
      payload.append('hinh_thuc_thanh_toan', paymentMethod);
      payload.append('ghi_chu', noteValue || '');
      if (paymentMethod === 'chuyen_khoan' && transferImageInput && transferImageInput.files.length > 0) {
        payload.append('anh_chuyen_khoan', transferImageInput.files[0]);
      }

      console.debug(slotPaymentDebugLabel, 'Submit click', {
        targetUrl,
        slotPaymentId: currentSlotPaymentId,
        action: currentSlotPaymentAction,
        paymentMethod,
      });

      fetch(targetUrl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body: payload
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
        alert(currentSlotPaymentAction === 'student_submit'
          ? '✅ Đã gửi yêu cầu thanh toán, vui lòng chờ xác nhận!'
          : '✅ Thanh toán slot thành công!');
        const modalInstance = bootstrap.Modal.getInstance(slotPaymentModal);
        modalInstance.hide();
        const nextUrl = new URL(window.location.href);
        if (currentTab) {
          nextUrl.searchParams.set('tab', currentTab);
        }
        if (selectedHoaDonId) {
          nextUrl.searchParams.set('hoa_don_id', selectedHoaDonId);
        }
        window.location.assign(nextUrl.toString());
      })
      .catch(err => {
        console.error(slotPaymentDebugLabel, 'Slot payment failed', err);
        alert('❌ Không thể gửi yêu cầu: ' + (err?.message || 'Không xác định'));
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
@endpush
@endif
@endsection

