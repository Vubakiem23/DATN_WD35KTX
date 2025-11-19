@extends('client.layouts.app')

@section('title', 'Hóa đơn')

@section('content')
<div class="container py-4">
  <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
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

  <ul class="nav nav-pills mb-4">
    <li class="nav-item">
      <a class="nav-link {{ $tab === 'dien-nuoc' ? 'active' : '' }}" href="{{ route('client.hoadon.diennuoc') }}">
        <i class="fa fa-bolt me-1"></i> Điện & nước
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ $tab === 'tien-phong' ? 'active' : '' }}" href="{{ route('client.hoadon.tienphong') }}">
        <i class="fa fa-bed me-1"></i> Tiền phòng
      </a>
    </li>
  </ul>

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
      $studentBreakdown = $selectedHoaDon->student_breakdown ?? null;
      $studentSlotLabel = optional($selectedHoaDon->student_payment)->slot_label;
      $showPersonalUtilities = $tab === 'dien-nuoc';
      $showPersonalRent = $tab === 'tien-phong';
      $clientsTienDien = $showPersonalUtilities
        ? ($studentBreakdown['tien_dien'] ?? 0)
        : ($selectedHoaDon->tien_dien ?? 0);
      $clientsTienNuoc = $showPersonalUtilities
        ? ($studentBreakdown['tien_nuoc'] ?? 0)
        : ($selectedHoaDon->tien_nuoc ?? 0);
      $clientsTienPhong = $showPersonalRent
        ? ($studentBreakdown['tien_phong'] ?? 0)
        : ($selectedHoaDon->tien_phong_slot ?? 0);
      $shouldShowOnlyStudentSlot = $showPersonalUtilities || $showPersonalRent;
      $clientsTongCong = $clientsTienDien + $clientsTienNuoc + $clientsTienPhong;
    @endphp

    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3">
            <div class="p-3 bg-light rounded h-100">
              <div class="text-uppercase text-muted small">Tháng</div>
              <div class="fs-4 fw-semibold">{{ \Carbon\Carbon::parse($hoaDon->created_at)->format('m/Y') }}</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="p-3 bg-light rounded h-100">
              <div class="text-uppercase text-muted small">Số slot tính phí</div>
              <div class="fs-4 fw-semibold">
                {{ $shouldShowOnlyStudentSlot ? 1 : ($selectedHoaDon->slot_billing_count ?? 0) }}
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="p-3 bg-light rounded h-100">
              <div class="text-uppercase text-muted small">Tiền điện</div>
              <div class="fs-4 fw-semibold text-danger">{{ number_format($clientsTienDien, 0, ',', '.') }} VND</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="p-3 bg-light rounded h-100">
              <div class="text-uppercase text-muted small">Tiền nước</div>
              <div class="fs-4 fw-semibold text-primary">{{ number_format($clientsTienNuoc, 0, ',', '.') }} VND</div>
            </div>
          </div>
        </div>

        <div class="row g-3 mt-1">
          <div class="col-md-3">
            <div class="p-3 bg-light rounded h-100">
              <div class="text-uppercase text-muted small">Tiền phòng</div>
              <div class="fs-4 fw-semibold text-success">{{ number_format($clientsTienPhong, 0, ',', '.') }} VND</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="p-3 bg-light rounded h-100">
              <div class="text-uppercase text-muted small">Tổng cộng</div>
              <div class="fs-4 fw-semibold">{{ number_format($clientsTongCong, 0, ',', '.') }} VND</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="p-3 bg-light rounded h-100">
              <div class="text-uppercase text-muted small">
                {{ $tab === 'tien-phong' ? 'Trạng thái tiền phòng' : 'Trạng thái điện · nước' }}
              </div>
              @if($tab === 'tien-phong')
                <div class="fs-5 fw-semibold {{ $selectedHoaDon->da_thanh_toan ? 'text-success' : 'text-warning' }}">
                  {{ $selectedHoaDon->trang_thai ?? 'Chưa rõ' }}
                </div>
                <div class="small text-muted">
                  {{ $selectedHoaDon->sent_to_client_at ? 'Gửi SV: '.$selectedHoaDon->sent_to_client_at->format('d/m/Y H:i') : 'Chưa gửi SV' }}
                </div>
              @else
                <div class="fs-5 fw-semibold {{ $selectedHoaDon->da_thanh_toan_dien_nuoc ? 'text-success' : 'text-warning' }}">
                  {{ $selectedHoaDon->da_thanh_toan_dien_nuoc ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                </div>
                <div class="small text-muted">
                  {{ $selectedHoaDon->sent_dien_nuoc_at ? 'Gửi SV: '.$selectedHoaDon->sent_dien_nuoc_at->format('d/m/Y H:i') : 'Chưa gửi SV' }}
                </div>
              @endif
            </div>
          </div>
          <div class="col-md-3">
            <div class="p-3 bg-light rounded h-100">
              <div class="text-uppercase text-muted small">
                {{ $tab === 'tien-phong' ? 'Slot của bạn' : 'Thông tin điện · nước' }}
              </div>
              @if($tab === 'tien-phong')
                @if($selectedHoaDon->student_payment)
                  @php
                    $myPayment = $selectedHoaDon->student_payment;
                    $myStatus = $myPayment->trang_thai ?? ($myPayment->da_thanh_toan ? 'da_thanh_toan' : 'chua_thanh_toan');
                  @endphp
                  <div class="fs-5 fw-semibold">{{ $myPayment->slot_label }}</div>
                  <div class="small text-muted">
                    @if($myStatus === 'da_thanh_toan')
                      <span class="text-success"><i class="fa fa-check-circle"></i> Đã thanh toán</span>
                    @elseif($myStatus === 'cho_xac_nhan')
                      <span class="text-info"><i class="fa fa-hourglass-half"></i> Chờ ban quản lý xác nhận</span>
                    @else
                      <span class="text-warning"><i class="fa fa-clock"></i> Chưa thanh toán</span>
                    @endif
                  </div>
                  @if($myPayment->client_ghi_chu)
                    <div class="small text-muted mt-1">Ghi chú: {{ $myPayment->client_ghi_chu }}</div>
                  @endif
                @else
                  <div class="fs-6 text-muted">Chưa có dữ liệu slot</div>
                @endif
              @else
                <div class="fs-6 text-muted">
                  Chi phí điện · nước được tính và thu chung cho cả phòng. Vui lòng phối hợp với trưởng phòng để hoàn tất.
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>

    @if($tab === 'tien-phong')
      <div class="card shadow-sm mb-4">
        <div class="card-header fw-semibold text-uppercase">Chi tiết tiền phòng</div>
        <div class="card-body">
          @if(!empty($selectedHoaDon->slot_breakdowns))
            <div class="table-responsive">
              <table class="table table-striped text-center">
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
                    @php $hasVisibleRows = true; @endphp
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
                              data-slot-url="{{ route('hoadon.thanhtoanslot', ['hoaDonId' => $selectedHoaDon->id, 'slotPaymentId' => $slotPayment->id]) }}"
                              data-slot-label="{{ $slot['label'] }}"
                              data-sinh-vien="{{ $slot['sinh_vien'] }}"
                              data-action="student_submit">
                              <i class="fa fa-money-bill"></i> Thanh toán
                            </button>
                          @elseif($slotStatus === 'cho_xac_nhan')
                            <span class="text-muted small">Đang chờ xác nhận</span>
                          @else
                            <span class="text-muted small">Đã hoàn tất</span>
                          @endif
                        @elseif($slotPayment && $slotStatus === 'da_thanh_toan')
                          <span class="text-muted small">Đã hoàn tất</span>
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
        <div class="card-header fw-semibold text-uppercase">Chi tiết tiền điện · nước</div>
        <div class="card-body">
          @if(!empty($selectedHoaDon->slot_breakdowns))
            <div class="table-responsive">
              <table class="table table-striped text-center">
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
                  @endphp
                  @forelse($studentRows as $slot)
                    @php
                      $tongSlot = ($slot['tien_dien'] ?? 0) + ($slot['tien_nuoc'] ?? 0);
                      $slotPayment = $selectedHoaDon->slotPaymentsMap[$slot['label']] ?? null;
                      $slotStatus = $slotPayment
                        ? ($slotPayment->trang_thai ?? ($slotPayment->da_thanh_toan ? 'da_thanh_toan' : 'chua_thanh_toan'))
                        : 'chua_thanh_toan';
                      $statusMeta = match($slotStatus) {
                        'da_thanh_toan' => ['class' => 'badge bg-success', 'text' => 'Đã thanh toán', 'icon' => 'fa-check-circle'],
                        'cho_xac_nhan' => ['class' => 'badge bg-info text-dark', 'text' => 'Chờ xác nhận', 'icon' => 'fa-hourglass-half'],
                        default => ['class' => 'badge bg-warning text-dark', 'text' => 'Chưa thanh toán', 'icon' => 'fa-clock'],
                      };
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
                        @if($selectedHoaDon->da_thanh_toan_dien_nuoc && $selectedHoaDon->ngay_thanh_toan_dien_nuoc)
                          <div class="small text-muted mt-1">
                            Hóa đơn điện · nước xác nhận {{ \Carbon\Carbon::parse($selectedHoaDon->ngay_thanh_toan_dien_nuoc)->format('d/m/Y H:i') }}
                          </div>
                        @elseif(!$selectedHoaDon->sent_dien_nuoc_to_client)
                          <div class="small text-muted mt-1">Hóa đơn chưa gửi tới sinh viên</div>
                        @endif
                      </td>
                      <td>
                        @if($slotPayment)
                          @if($slotStatus === 'chua_thanh_toan')
                            <button type="button"
                              class="btn btn-sm btn-primary"
                              data-bs-toggle="modal"
                              data-bs-target="#slotPaymentModal"
                              data-slot-payment-id="{{ $slotPayment->id }}"
                              data-slot-url="{{ route('hoadon.thanhtoanslot', ['hoaDonId' => $selectedHoaDon->id, 'slotPaymentId' => $slotPayment->id]) }}"
                              data-slot-label="{{ $slot['label'] }}"
                              data-sinh-vien="{{ $slot['sinh_vien'] }}"
                              data-action="student_submit">
                              <i class="fa fa-money-bill"></i> Thanh toán
                            </button>
                          @elseif($slotStatus === 'cho_xac_nhan')
                            <span class="text-muted small">Đang chờ xác nhận</span>
                          @else
                            <span class="text-muted small">Đã hoàn tất</span>
                          @endif
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
</div>

@if($selectedHoaDon)
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

      document.getElementById('slotPaymentMethod').value = '';
      document.getElementById('slotPaymentNote').value = '';
    });
  }

  if (confirmSlotPaymentBtn) {
    confirmSlotPaymentBtn.addEventListener('click', function() {
      const paymentMethod = document.getElementById('slotPaymentMethod').value;
      const note = document.getElementById('slotPaymentNote').value;

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
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          action: currentSlotPaymentAction,
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
        alert(currentSlotPaymentAction === 'student_submit'
          ? '✅ Đã gửi yêu cầu thanh toán, vui lòng chờ xác nhận!'
          : '✅ Thanh toán slot thành công!');
        const modalInstance = bootstrap.Modal.getInstance(slotPaymentModal);
        modalInstance.hide();
        setTimeout(() => location.reload(), 500);
      })
      .catch(err => {
        console.error(slotPaymentDebugLabel, 'Slot payment failed', err);
        alert('❌ Không thể gửi yêu cầu: ' + (err?.message || 'Không xác định'));
      });
    });
  }
});
</script>
@endpush
@endif
@endsection

