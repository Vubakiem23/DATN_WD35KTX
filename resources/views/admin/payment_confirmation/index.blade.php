@extends('admin.layouts.admin')

@php
  use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="container-fluid py-4">
  <!-- Header với thống kê -->
  <div class="row mb-4">
    <div class="col-md-12">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h3 class="mb-1" style="display:flex;align-items:center;gap:.5rem;">
            <i class="fa fa-clipboard" style="color:#4e54c8;"></i>
            Quản lý yêu cầu thanh toán
          </h3>
          <p class="text-muted mb-0">Xem và xác nhận yêu cầu thanh toán từ sinh viên</p>
        </div>
      </div>
    </div>
  </div>
  <!-- Filter & Search -->
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <form method="GET">
        {{-- Hàng tiêu đề (chỉ chữ) --}}
        <div class="row mb-2">
          <div class="col-md-3">
            <span class="form-label fw-semibold d-block">Loại thanh toán</span>
          </div>
          <div class="col-md-3">
            <span class="form-label fw-semibold d-block">Trạng thái</span>
          </div>
          <div class="col-md-4">
            <span class="form-label fw-semibold d-block">Tìm kiếm</span>
          </div>
        </div>

        {{-- Hàng ô lọc (box) --}}
        <div class="row g-3 align-items-end">
          <div class="col-md-3">
            <select name="type" class="form-select">
              <option value="all" {{ $type === 'all' ? 'selected' : '' }}>Tất cả</option>
              <option value="slot" {{ $type === 'slot' ? 'selected' : '' }}>📄 Tiền phòng</option>
              <option value="utilities" {{ $type === 'utilities' ? 'selected' : '' }}>⚡ Điện nước</option>
            </select>
          </div>
          <div class="col-md-3">
            <select name="status" class="form-select">
              <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Tất cả</option>
              <option value="cho_xac_nhan" {{ $status === 'cho_xac_nhan' ? 'selected' : '' }}>⏳ Chờ xác nhận</option>
              <option value="da_thanh_toan" {{ $status === 'da_thanh_toan' ? 'selected' : '' }}>✅ Đã xác nhận</option>
              <option value="chua_thanh_toan" {{ $status === 'chua_thanh_toan' ? 'selected' : '' }}>❌ Chưa thanh toán</option>
            </select>
          </div>
          <div class="col-md-4">
            <input type="text"
                   name="search"
                   class="form-control"
                   placeholder="Sinh viên, phòng, hóa đơn..."
                   value="{{ $search ?? '' }}">
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">
              <i class="fa fa-search me-1"></i>Tìm kiếm
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Tabs -->
  <div class="card shadow-sm mb-4">
    <div class="card-header">
      <ul class="nav nav-tabs card-header-tabs" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" id="all-tab" data-toggle="tab" href="#all" role="tab">
            <i class="fa fa-list me-2"></i>Tất cả ({{ $slotPayments->count() + $utilitiesPayments->count() }})
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="slot-tab" data-toggle="tab" href="#slot" role="tab">
            <i class="fa fa-home me-2"></i>Tiền phòng ({{ $slotPayments->count() }})
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="utilities-tab" data-toggle="tab" href="#utilities" role="tab">
            <i class="fa fa-bolt me-2"></i>Điện nước ({{ $utilitiesPayments->count() }})
          </a>
        </li>
      </ul>
    </div>

    <div class="card-body">
      <div class="tab-content">
        <!-- TAB: TẤT CẢ -->
        <div class="tab-pane fade show active" id="all" role="tabpanel">
          @if($slotPayments->isEmpty() && $utilitiesPayments->isEmpty())
            <div class="alert alert-info" role="alert">
              <i class="fa fa-info-circle me-2"></i>Không có yêu cầu nào.
            </div>
          @else
            <!-- TIỀN PHÒNG -->
            @if($slotPayments->isNotEmpty())
              <h6 class="fw-bold mb-3 text-primary">📄 Yêu cầu thanh toán tiền phòng</h6>
              <div class="mb-2 d-flex gap-2 align-items-center">
                <button id="batchConfirmSlotAll" class="btn btn-sm btn-success">
                  <i class="fa fa-check me-1"></i>Xác nhận đã chọn
                </button>
                <button id="batchRejectSlotAll" class="btn btn-sm btn-danger">
                  <i class="fa fa-times me-1"></i>Từ chối đã chọn
                </button>
                <input id="batchNoteSlotAll" class="form-control form-control-sm w-50" placeholder="Ghi chú quản lý (tùy chọn)">
              </div>
              <div class="table-responsive mb-4">
                <table class="table table-hover table-striped align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th style="width:40px"><input type="checkbox" class="select-all-slot"></th>
                      <th>Sinh viên</th>
                      <th>Phòng</th>
                      <th>Slot</th>
                      <th>Tiền phòng</th>
                      <th>Hình thức</th>
                      <th>Ngày gửi</th>
                      <th>Trạng thái</th>
                      <th>Thao tác</th>
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
                        $slotDetailData = [
                          'id' => $payment->id,
                          'sinhVien' => $payment->sinhVien->ho_ten ?? 'N/A',
                          'maSv' => $payment->sinhVien->ma_sinh_vien ?? 'N/A',
                          'phong' => optional($payment->hoaDon->phong)->ten_phong ?? 'N/A',
                          'slot' => $payment->slot_label,
                          'amount' => number_format($payment->requested_amount ?? ($payment->hoaDon->slot_unit_price ?? 0), 0, ',', '.') . ' VND',
                          'method' => $payment->hinh_thuc_thanh_toan ?? '-',
                          'requestedAt' => $payment->client_requested_at ? \Carbon\Carbon::parse($payment->client_requested_at)->format('d/m/Y H:i') : '-',
                          'status' => $statusText,
                          'note' => $payment->client_ghi_chu ?? '',
                          'adminNote' => $payment->ghi_chu ?? '',
                          'image' => $payment->hinh_thuc_thanh_toan === 'chuyen_khoan' && $payment->client_transfer_image_path ? Storage::disk('public')->url($payment->client_transfer_image_path) : '',
                        ];
                      @endphp
                      <tr>
                        <td><input type="checkbox" class="select-item-slot" value="{{ $payment->id }}"></td>
                        <td>
                          <strong>{{ $payment->sinhVien->ho_ten ?? 'N/A' }}</strong><br>
                          <small class="text-muted">{{ $payment->sinhVien->ma_sinh_vien ?? 'N/A' }}</small>
                        </td>
                        <td>{{ optional($payment->hoaDon->phong)->ten_phong ?? 'N/A' }}</td>
                        <td><span class="badge bg-success-subtle text-success border border-success-subtle">{{ $payment->slot_label }}</span></td>
                        <td class="fw-semibold text-success">
                          {{ number_format($payment->requested_amount ?? ($payment->hoaDon->slot_unit_price ?? 0), 0, ',', '.') }} VND
                        </td>
                        <td>
                          {{ match($payment->hinh_thuc_thanh_toan ?? '') {
                            'chuyen_khoan' => '🏦 Chuyển khoản',
                            'tien_mat' => '💵 Tiền mặt',
                            default => '-',
                          } }}
                        </td>
                        <td>
                          {{ $payment->client_requested_at ? \Carbon\Carbon::parse($payment->client_requested_at)->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td><span class="badge bg-success-subtle text-success border border-success-subtle""{{ $statusClass }}">{{ $statusText }}</span></td>
                        <td>
                          <button type="button" class="btn btn-sm btn-outline-primary slot-detail-btn" 
                                  data-detail='@json($slotDetailData)'>
                            <i class="fa fa-eye me-1"></i>Chi tiết
                          </button>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @endif

            <!-- ĐIỆN NƯỚC -->
            @if($utilitiesPayments->isNotEmpty())
              <h6 class="fw-bold mb-3 text-success">⚡ Yêu cầu thanh toán điện nước</h6>
              <div class="mb-2 d-flex gap-2 align-items-center">
                <button id="batchConfirmUtilitiesAll" class="btn btn-sm btn-success">
                  <i class="fa fa-check me-1"></i>Xác nhận đã chọn
                </button>
                <button id="batchRejectUtilitiesAll" class="btn btn-sm btn-danger">
                  <i class="fa fa-times me-1"></i>Từ chối đã chọn
                </button>
                <input id="batchNoteUtilitiesAll" class="form-control form-control-sm w-50" placeholder="Ghi chú quản lý (tùy chọn)">
              </div>
              <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th style="width:40px"><input type="checkbox" class="select-all-utilities"></th>
                      <th>Sinh viên</th>
                      <th>Phòng</th>
                      <th>Slot</th>
                      <th>Điện</th>
                      <th>Nước</th>
                      <th>Tổng</th>
                      <th>Hình thức</th>
                      <th>Ngày gửi</th>
                      <th>Trạng thái</th>
                      <th>Thao tác</th>
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
                        $totalAmount = ($payment->requested_amount ?? (($payment->tien_dien ?? 0) + ($payment->tien_nuoc ?? 0)));
                        $utilitiesDetailData = [
                          'id' => $payment->id,
                          'sinhVien' => $payment->sinhVien->ho_ten ?? 'N/A',
                          'maSv' => $payment->sinhVien->ma_sinh_vien ?? 'N/A',
                          'phong' => optional($payment->hoaDon->phong)->ten_phong ?? 'N/A',
                          'slot' => $payment->slot_label,
                          'tienDien' => number_format($payment->requested_tien_dien ?? ($payment->tien_dien ?? 0), 0, ',', '.') . ' VND',
                          'tienNuoc' => number_format($payment->requested_tien_nuoc ?? ($payment->tien_nuoc ?? 0), 0, ',', '.') . ' VND',
                          'total' => number_format($totalAmount, 0, ',', '.') . ' VND',
                          'method' => $payment->hinh_thuc_thanh_toan ?? '-',
                          'requestedAt' => $payment->client_requested_at ? \Carbon\Carbon::parse($payment->client_requested_at)->format('d/m/Y H:i') : '-',
                          'status' => $statusText,
                          'note' => $payment->client_ghi_chu ?? '',
                          'adminNote' => $payment->ghi_chu ?? '',
                          'image' => $payment->hinh_thuc_thanh_toan === 'chuyen_khoan' && $payment->client_transfer_image_path ? Storage::disk('public')->url($payment->client_transfer_image_path) : '',
                        ];
                      @endphp
                      <tr>
                        <td><input type="checkbox" class="select-item-utilities" value="{{ $payment->id }}"></td>
                        <td>
                          <strong>{{ $payment->sinhVien->ho_ten ?? 'N/A' }}</strong><br>
                          <small class="text-muted">{{ $payment->sinhVien->ma_sinh_vien ?? 'N/A' }}</small>
                        </td>
                        <td>{{ optional($payment->hoaDon->phong)->ten_phong ?? 'N/A' }}</td>
                        <td><span class="badge bg-success-subtle text-success border border-success-subtle">{{ $payment->slot_label }}</span></td>
                        <td class="text-danger fw-semibold">{{ number_format($payment->requested_tien_dien ?? ($payment->tien_dien ?? 0), 0, ',', '.') }} VND</td>
                        <td class="text-info fw-semibold">{{ number_format($payment->requested_tien_nuoc ?? ($payment->tien_nuoc ?? 0), 0, ',', '.') }} VND</td>
                        <td class="fw-bold">{{ number_format($totalAmount, 0, ',', '.') }} VND</td>
                        <td>
                          {{ match($payment->hinh_thuc_thanh_toan ?? '') {
                            'chuyen_khoan' => '🏦 Chuyển khoản',
                            'tien_mat' => '💵 Tiền mặt',
                            default => '-',
                          } }}
                        </td>
                        <td>
                          {{ $payment->client_requested_at ? \Carbon\Carbon::parse($payment->client_requested_at)->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td><span class="badge bg-success-subtle text-success border border-success-subtle""{{ $statusClass }}">{{ $statusText }}</span></td>
                        <td>
                          <button type="button" class="btn btn-sm btn-outline-success open-utilities-detail-btn" 
                            data-id="{{ $payment->id }}"
                            data-sinh-vien="{{ $payment->sinhVien->ho_ten ?? 'N/A' }}"
                            data-ma-sv="{{ $payment->sinhVien->ma_sinh_vien ?? 'N/A' }}"
                            data-phong="{{ optional($payment->hoaDon->phong)->ten_phong ?? 'N/A' }}"
                            data-slot="{{ $payment->slot_label }}"
                            data-tien-dien="{{ number_format($payment->requested_tien_dien ?? ($payment->tien_dien ?? 0), 0, ',', '.') }} VND"
                            data-tien-nuoc="{{ number_format($payment->requested_tien_nuoc ?? ($payment->tien_nuoc ?? 0), 0, ',', '.') }} VND"
                            data-total="{{ number_format($totalAmount, 0, ',', '.') }} VND"
                            data-method="{{ $payment->hinh_thuc_thanh_toan ?? '-' }}"
                            data-requested-at="{{ $payment->client_requested_at ? \Carbon\Carbon::parse($payment->client_requested_at)->format('d/m/Y H:i') : '-' }}"
                            data-status="{{ $statusText }}"
                            data-note="{{ $payment->client_ghi_chu ?? '' }}"
                            data-admin-note="{{ $payment->ghi_chu ?? '' }}"
                            data-image="{{ $payment->hinh_thuc_thanh_toan === 'chuyen_khoan' && $payment->client_transfer_image_path ? Storage::disk('public')->url($payment->client_transfer_image_path) : '' }}">
                            <i class="fa fa-eye me-1"></i>Chi tiết
                          </button>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @endif
          @endif
        </div>

        <!-- TAB: TIỀN PHÒNG -->
        <div class="tab-pane fade" id="slot" role="tabpanel">
          @if($slotPayments->isEmpty())
            <div class="alert alert-info">Không có yêu cầu tiền phòng</div>
          @else
            <div class="mb-2 d-flex gap-2 align-items-center">
              <button id="batchConfirmSlot" class="btn btn-sm btn-success">
                <i class="fa fa-check me-1"></i>Xác nhận đã chọn
              </button>
              <button id="batchRejectSlot" class="btn btn-sm btn-danger">
                <i class="fa fa-times me-1"></i>Từ chối đã chọn
              </button>
              <input id="batchNoteSlot" class="form-control form-control-sm w-50" placeholder="Ghi chú quản lý (tùy chọn)">
            </div>
            <div class="table-responsive">
              <table class="table table-hover table-striped align-middle">
                <thead class="table-light">
                  <tr>
                    <th style="width:40px"><input type="checkbox" class="select-all-slot"></th>
                    <th>Sinh viên</th>
                    <th>Phòng</th>
                    <th>Slot</th>
                    <th>Tiền phòng</th>
                    <th>Hình thức</th>
                    <th>Ngày gửi</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($slotPayments as $payment)
                    @php
                      $slotDetailData2 = [
                        'id' => $payment->id,
                        'sinhVien' => $payment->sinhVien->ho_ten ?? 'N/A',
                        'maSv' => $payment->sinhVien->ma_sinh_vien ?? 'N/A',
                        'phong' => optional($payment->hoaDon->phong)->ten_phong ?? 'N/A',
                        'slot' => $payment->slot_label,
                        'amount' => number_format($payment->requested_amount ?? ($payment->hoaDon->slot_unit_price ?? 0), 0, ',', '.') . ' VND',
                        'method' => $payment->hinh_thuc_thanh_toan ?? '-',
                        'requestedAt' => $payment->client_requested_at ? \Carbon\Carbon::parse($payment->client_requested_at)->format('d/m/Y H:i') : '-',
                        'status' => match($payment->trang_thai ?? 'chua_thanh_toan') { 'cho_xac_nhan' => 'Chờ xác nhận', 'da_thanh_toan' => 'Đã xác nhận', default => 'Chưa thanh toán' },
                        'note' => $payment->client_ghi_chu ?? '',
                        'adminNote' => $payment->ghi_chu ?? '',
                        'image' => $payment->hinh_thuc_thanh_toan === 'chuyen_khoan' && $payment->client_transfer_image_path ? Storage::disk('public')->url($payment->client_transfer_image_path) : '',
                      ];
                    @endphp
                    <tr>
                      <td><input type="checkbox" class="select-item-slot" value="{{ $payment->id }}"></td>
                      <td>
                        <strong>{{ $payment->sinhVien->ho_ten ?? 'N/A' }}</strong><br>
                        <small class="text-muted">{{ $payment->sinhVien->ma_sinh_vien ?? 'N/A' }}</small>
                      </td>
                      <td>{{ optional($payment->hoaDon->phong)->ten_phong ?? 'N/A' }}</td>
                      <td><span class="badge bg-secondary">{{ $payment->slot_label }}</span></td>
                      <td class="fw-semibold text-success">{{ number_format($payment->requested_amount ?? ($payment->hoaDon->slot_unit_price ?? 0), 0, ',', '.') }} VND</td>
                      <td>{{ match($payment->hinh_thuc_thanh_toan ?? '') { 'chuyen_khoan' => '🏦 Chuyển khoản', 'tien_mat' => '💵 Tiền mặt', default => '-' } }}</td>
                      <td>{{ $payment->client_requested_at ? \Carbon\Carbon::parse($payment->client_requested_at)->format('d/m/Y H:i') : '-' }}</td>
                      <td>
                        <span class="badge {{ match($payment->trang_thai ?? 'chua_thanh_toan') { 'cho_xac_nhan' => 'bg-info text-dark', 'da_thanh_toan' => 'bg-success', default => 'bg-warning text-dark' } }}">
                          {{ match($payment->trang_thai ?? 'chua_thanh_toan') { 'cho_xac_nhan' => 'Chờ xác nhận', 'da_thanh_toan' => 'Đã xác nhận', default => 'Chưa thanh toán' } }}
                        </span>
                      </td>
                      <td>
                        <button type="button" class="btn btn-sm btn-outline-primary slot-detail-btn" 
                                data-detail='@json($slotDetailData2)'>
                          <i class="fa fa-eye me-1"></i>Chi tiết
                        </button>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>

        <!-- TAB: ĐIỆN NƯỚC -->
        <div class="tab-pane fade" id="utilities" role="tabpanel">
          @if($utilitiesPayments->isEmpty())
            <div class="alert alert-info">Không có yêu cầu điện nước</div>
          @else
            <div class="mb-2 d-flex gap-2 align-items-center">
              <button id="batchConfirmUtilities" class="btn btn-sm btn-success">
                <i class="fa fa-check me-1"></i>Xác nhận đã chọn
              </button>
              <button id="batchRejectUtilities" class="btn btn-sm btn-danger">
                <i class="fa fa-times me-1"></i>Từ chối đã chọn
              </button>
              <input id="batchNoteUtilities" class="form-control form-control-sm w-50" placeholder="Ghi chú quản lý (tùy chọn)">
            </div>
            <div class="table-responsive">
              <table class="table table-hover table-striped align-middle">
                <thead class="table-light">
                  <tr>
                    <th style="width:40px"><input type="checkbox" class="select-all-utilities"></th>
                    <th>Sinh viên</th>
                    <th>Phòng</th>
                    <th>Slot</th>
                    <th>Điện</th>
                    <th>Nước</th>
                    <th>Tổng</th>
                    <th>Hình thức</th>
                    <th>Ngày gửi</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($utilitiesPayments as $payment)
                    @php 
                      $totalAmount = ($payment->requested_amount ?? (($payment->requested_tien_dien ?? 0) + ($payment->requested_tien_nuoc ?? 0)));
                      $utilitiesDetailData2 = [
                        'id' => $payment->id,
                        'sinhVien' => $payment->sinhVien->ho_ten ?? 'N/A',
                        'maSv' => $payment->sinhVien->ma_sinh_vien ?? 'N/A',
                        'phong' => optional($payment->hoaDon->phong)->ten_phong ?? 'N/A',
                        'slot' => $payment->slot_label,
                        'tienDien' => number_format($payment->requested_tien_dien ?? ($payment->tien_dien ?? 0), 0, ',', '.') . ' VND',
                        'tienNuoc' => number_format($payment->requested_tien_nuoc ?? ($payment->tien_nuoc ?? 0), 0, ',', '.') . ' VND',
                        'total' => number_format($totalAmount, 0, ',', '.') . ' VND',
                        'method' => $payment->hinh_thuc_thanh_toan ?? '-',
                        'requestedAt' => $payment->client_requested_at ? \Carbon\Carbon::parse($payment->client_requested_at)->format('d/m/Y H:i') : '-',
                        'status' => match($payment->trang_thai ?? 'chua_thanh_toan') { 'cho_xac_nhan' => 'Chờ xác nhận', 'da_thanh_toan' => 'Đã xác nhận', default => 'Chưa thanh toán' },
                        'note' => $payment->client_ghi_chu ?? '',
                        'adminNote' => $payment->ghi_chu ?? '',
                        'image' => $payment->hinh_thuc_thanh_toan === 'chuyen_khoan' && $payment->client_transfer_image_path ? Storage::disk('public')->url($payment->client_transfer_image_path) : '',
                      ];
                    @endphp
                    <tr>
                      <td><input type="checkbox" class="select-item-utilities" value="{{ $payment->id }}"></td>
                      <td>
                        <strong>{{ $payment->sinhVien->ho_ten ?? 'N/A' }}</strong><br>
                        <small class="text-muted">{{ $payment->sinhVien->ma_sinh_vien ?? 'N/A' }}</small>
                      </td>
                      <td>{{ optional($payment->hoaDon->phong)->ten_phong ?? 'N/A' }}</td>
                      <td><span class="badge bg-secondary">{{ $payment->slot_label }}</span></td>
                      <td class="text-danger fw-semibold">{{ number_format($payment->requested_tien_dien ?? ($payment->tien_dien ?? 0), 0, ',', '.') }} VND</td>
                      <td class="text-info fw-semibold">{{ number_format($payment->requested_tien_nuoc ?? ($payment->tien_nuoc ?? 0), 0, ',', '.') }} VND</td>
                      <td class="fw-bold">{{ number_format($totalAmount, 0, ',', '.') }} VND</td>
                      <td>{{ match($payment->hinh_thuc_thanh_toan ?? '') { 'chuyen_khoan' => '🏦 Chuyển khoản', 'tien_mat' => '💵 Tiền mặt', default => '-' } }}</td>
                      <td>{{ $payment->client_requested_at ? \Carbon\Carbon::parse($payment->client_requested_at)->format('d/m/Y H:i') : '-' }}</td>
                      <td>
                        <span class="badge {{ match($payment->trang_thai ?? 'chua_thanh_toan') { 'cho_xac_nhan' => 'bg-info text-dark', 'da_thanh_toan' => 'bg-success', default => 'bg-warning text-dark' } }}">
                          {{ match($payment->trang_thai ?? 'chua_thanh_toan') { 'cho_xac_nhan' => 'Chờ xác nhận', 'da_thanh_toan' => 'Đã xác nhận', default => 'Chưa thanh toán' } }}
                        </span>
                      </td>
                      <td>
                        <button type="button" class="btn btn-sm btn-outline-success utilities-detail-btn" 
                                data-detail='@json($utilitiesDetailData2)'>
                          <i class="fa fa-eye me-1"></i>Chi tiết
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

<!-- Modal: Chi tiết tiền phòng (Admin) -->
<div class="modal fade" id="slotDetailModal" tabindex="-1" role="dialog" aria-labelledby="slotDetailModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-primary text-white border-0">
        <h5 class="modal-title fw-bold">
          <i class="fa fa-receipt me-2"></i>Chi tiết yêu cầu tiền phòng
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-4">
        <div class="row g-3">
          <div class="col-md-6">
            <div class="simple-section h-100">
              <h6 class="section-title">Thông tin sinh viên</h6>
              <div class="info-item">
                <span class="label">Họ tên:</span>
                <span class="value" id="slotSinhVien">-</span>
              </div>
              <div class="info-item">
                <span class="label">Mã SV:</span>
                <span class="value" id="slotMaSV">-</span>
              </div>
              <div class="info-item">
                <span class="label">Phòng:</span>
                <span class="value" id="slotPhong">-</span>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="simple-section h-100">
              <h6 class="section-title">Thông tin thanh toán</h6>
              <div class="info-item">
                <span class="label">Slot:</span>
                <span class="value"><span class="badge badge-secondary" id="slotLabel">-</span></span>
              </div>
              <div class="info-item">
                <span class="label">Số tiền:</span>
                <span class="value text-success font-weight-bold" id="slotAmount">-</span>
              </div>
              <div class="info-item">
                <span class="label">Hình thức:</span>
                <span class="value" id="slotMethod">-</span>
              </div>
              <div class="info-item">
                <span class="label">Ngày gửi:</span>
                <span class="value" id="slotRequestedAt">-</span>
              </div>
              <div class="info-item">
                <span class="label">Trạng thái:</span>
                <span class="value"><span id="slotStatus" class="status-badge">-</span></span>
              </div>
            </div>
          </div>
        </div>

        <div class="row g-3 mt-0">
          <div class="col-12">
            <div class="simple-section">
              <h6 class="section-title">Ghi chú sinh viên</h6>
              <p id="slotNote" class="note-content mb-0">-</p>
            </div>
          </div>
        </div>

        <div class="row g-3 mt-0" id="slotImageWrapper" style="display: none;">
          <div class="col-12">
            <div class="simple-section">
              <h6 class="section-title">Ảnh chuyển khoản</h6>
              <div class="text-center py-2">
                <img id="slotDetailImage" src="" alt="Ảnh chuyển khoản" class="transfer-image">
              </div>
            </div>
          </div>
        </div>

        <div class="row g-3 mt-0">
          <div class="col-12">
            <div class="simple-section">
              <h6 class="section-title">Ghi chú quản lý</h6>
              <textarea id="adminNoteSlot" class="form-control" rows="3" placeholder="Nhập ghi chú của quản lý..."></textarea>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0 bg-light">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-danger" id="rejectSlotBtn">
          <i class="fa fa-times me-1"></i>Từ chối
        </button>
        <button type="button" class="btn btn-success" id="confirmSlotBtn">
          <i class="fa fa-check me-1"></i>Xác nhận
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Chi tiết điện nước (Admin) -->
<div class="modal fade" id="utilitiesDetailModal" tabindex="-1" role="dialog" aria-labelledby="utilitiesDetailModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-success text-white border-0">
        <h5 class="modal-title fw-bold">
          <i class="fa fa-leaf me-2"></i>Chi tiết yêu cầu điện nước
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-4">
        <div class="row g-3">
          <div class="col-md-6">
            <div class="simple-section h-100">
              <h6 class="section-title">Thông tin sinh viên</h6>
              <div class="info-item">
                <span class="label">Họ tên:</span>
                <span class="value" id="utilitiesSinhVien">-</span>
              </div>
              <div class="info-item">
                <span class="label">Mã SV:</span>
                <span class="value" id="utilitiesMaSV">-</span>
              </div>
              <div class="info-item">
                <span class="label">Phòng:</span>
                <span class="value" id="utilitiesPhong">-</span>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="simple-section h-100">
              <h6 class="section-title">Thông tin thanh toán</h6>
              <div class="info-item">
                <span class="label">Slot:</span>
                <span class="value"><span class="badge badge-secondary" id="utilitiesLabel">-</span></span>
              </div>
              <div class="info-item">
                <span class="label">Tiền điện:</span>
                <span class="value text-danger" id="utilitiesTienDien">-</span>
              </div>
              <div class="info-item">
                <span class="label">Tiền nước:</span>
                <span class="value text-info" id="utilitiesTienNuoc">-</span>
              </div>
              <div class="info-item">
                <span class="label">Tổng tiền:</span>
                <span class="value text-success font-weight-bold" id="utilitiesTotal">-</span>
              </div>
              <div class="info-item">
                <span class="label">Hình thức:</span>
                <span class="value" id="utilitiesMethod">-</span>
              </div>
              <div class="info-item">
                <span class="label">Ngày gửi:</span>
                <span class="value" id="utilitiesRequestedAt">-</span>
              </div>
            </div>
          </div>
        </div>

        <div class="row g-3 mt-0">
          <div class="col-12">
            <div class="simple-section">
              <h6 class="section-title">Ghi chú sinh viên</h6>
              <p id="utilitiesNote" class="note-content mb-0">-</p>
            </div>
          </div>
        </div>

        <div class="row g-3 mt-0" id="utilitiesImageWrapper" style="display: none;">
          <div class="col-12">
            <div class="simple-section">
              <h6 class="section-title">Ảnh chuyển khoản</h6>
              <div class="text-center py-2">
                <img id="utilitiesDetailImage" src="" alt="Ảnh chuyển khoản" class="transfer-image">
              </div>
            </div>
          </div>
        </div>

        <div class="row g-3 mt-0">
          <div class="col-12">
            <div class="simple-section">
              <h6 class="section-title">Ghi chú quản lý</h6>
              <textarea id="adminNoteUtilities" class="form-control" rows="3" placeholder="Nhập ghi chú của quản lý..."></textarea>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0 bg-light">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-danger" id="rejectUtilitiesBtn">
          <i class="fa fa-times me-1"></i>Từ chối
        </button>
        <button type="button" class="btn btn-success" id="confirmUtilitiesBtn">
          <i class="fa fa-check me-1"></i>Xác nhận
        </button>
      </div>
    </div>
  </div>
</div>

@push('styles')
<style>
  /* Simple Section Styles */
  .simple-section {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 16px;
    display: flex;
    flex-direction: column;
  }
  
  .simple-section.h-100 {
    height: 100%;
  }
  
  .section-title {
    font-size: 14px;
    font-weight: 600;
    color: #495057;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid #dee2e6;
  }
  
  .info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #f8f9fa;
  }
  
  .info-item:last-child {
    border-bottom: none;
  }
  
  .info-item .label {
    font-size: 14px;
    font-weight: 500;
    color: #6c757d;
    min-width: 130px;
    flex-shrink: 0;
  }
  
  .info-item .value {
    font-size: 14px;
    font-weight: 600;
    color: #212529;
    text-align: right;
    flex: 1;
    word-break: break-word;
  }
  
  .note-content {
    font-size: 14px;
    color: #495057;
    line-height: 1.6;
    margin: 0;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 4px;
    min-height: 50px;
  }
  
  .note-content:empty::before {
    content: "(Không có ghi chú)";
    color: #adb5bd;
    font-style: italic;
  }
  
  .transfer-image {
    max-width: 100%;
    max-height: 400px;
    border-radius: 4px;
    border: 1px solid #dee2e6;
  }
  
  .status-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
  }
  
  .status-badge.status-confirmed {
    background: #28a745;
    color: #fff;
  }
  
  .status-badge.status-pending {
    background: #ffc107;
    color: #000;
  }
  
  .status-badge.status-rejected {
    background: #dc3545;
    color: #fff;
  }
  
  .status-badge.status-unpaid {
    background: #6c757d;
    color: #fff;
  }
  
  /* Ensure consistent spacing */
  .row.g-3 {
    margin-left: -12px;
    margin-right: -12px;
  }
  
  .row.g-3 > [class*="col-"] {
    padding-left: 12px;
    padding-right: 12px;
  }
</style>
@endpush

@push('scripts')
<script>
// Khai báo biến global
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
let currentSlotPaymentId = null;
let currentUtilitiesPaymentId = null;

// Đảm bảo hàm ở global scope - ĐỊNH NGHĨA TRƯỚC DOMContentLoaded
window.showSlotDetail = function(data) {
  console.log('showSlotDetail called with data:', data);
  
  if (!data) {
    console.error('No data provided to showSlotDetail');
    alert('Lỗi: Không có dữ liệu để hiển thị');
    return;
  }
  
  currentSlotPaymentId = data.id;
  
  // Kiểm tra các element tồn tại trước khi set
  const elements = {
    slotSinhVien: document.getElementById('slotSinhVien'),
    slotMaSV: document.getElementById('slotMaSV'),
    slotPhong: document.getElementById('slotPhong'),
    slotLabel: document.getElementById('slotLabel'),
    slotAmount: document.getElementById('slotAmount'),
    slotMethod: document.getElementById('slotMethod'),
    slotRequestedAt: document.getElementById('slotRequestedAt'),
    slotStatus: document.getElementById('slotStatus'),
    slotNote: document.getElementById('slotNote'),
    adminNoteSlot: document.getElementById('adminNoteSlot')
  };
  
  // Set giá trị
  if (elements.slotSinhVien) elements.slotSinhVien.textContent = data.sinhVien || '-';
  if (elements.slotMaSV) elements.slotMaSV.textContent = data.maSv || '-';
  if (elements.slotPhong) elements.slotPhong.textContent = data.phong || '-';
  if (elements.slotLabel) elements.slotLabel.textContent = data.slot || '-';
  if (elements.slotAmount) elements.slotAmount.textContent = data.amount || '-';
  if (elements.slotMethod) elements.slotMethod.textContent = data.method || '-';
  if (elements.slotRequestedAt) elements.slotRequestedAt.textContent = data.requestedAt || '-';
  // Xử lý status badge với màu sắc
  if (elements.slotStatus) {
    const status = data.status || '-';
    elements.slotStatus.textContent = status;
    // Xóa các class cũ
    elements.slotStatus.className = 'status-badge';
    // Thêm class màu sắc dựa trên status
    const statusLower = status.toLowerCase();
    if (statusLower.includes('đã xác nhận') || statusLower.includes('confirmed')) {
      elements.slotStatus.classList.add('status-confirmed');
    } else if (statusLower.includes('chờ xác nhận') || statusLower.includes('pending')) {
      elements.slotStatus.classList.add('status-pending');
    } else if (statusLower.includes('từ chối') || statusLower.includes('rejected')) {
      elements.slotStatus.classList.add('status-rejected');
    } else if (statusLower.includes('chưa thanh toán') || statusLower.includes('unpaid')) {
      elements.slotStatus.classList.add('status-unpaid');
    } else {
      elements.slotStatus.classList.add('status-unpaid');
    }
  }
  if (elements.slotNote) elements.slotNote.textContent = data.note || '(Không có ghi chú)';
  if (elements.adminNoteSlot) elements.adminNoteSlot.value = data.adminNote || '';
  
  // Xử lý ảnh
  const imgWrapper = document.getElementById('slotImageWrapper');
  const slotDetailImage = document.getElementById('slotDetailImage');
  if (imgWrapper && slotDetailImage) {
    if (data.image && data.image.trim() !== '') {
      imgWrapper.style.display = 'block';
      slotDetailImage.src = data.image;
    } else {
      imgWrapper.style.display = 'none';
    }
  }
  
  // Mở modal - ưu tiên jQuery (Bootstrap 4)
  const modalEl = document.getElementById('slotDetailModal');
  if (!modalEl) {
    console.error('Modal element not found');
    alert('Lỗi: Không tìm thấy modal');
    return;
  }
  
  try {
    // Ưu tiên jQuery (Bootstrap 4)
    if (typeof $ !== 'undefined' && $.fn.modal) {
      $(modalEl).modal('show');
      console.log('Slot modal shown successfully (jQuery/Bootstrap 4)');
    }
    // Fallback Bootstrap 5
    else if (window.bootstrap && bootstrap.Modal) {
      const modal = new bootstrap.Modal(modalEl);
      modal.show();
      console.log('Slot modal shown successfully (Bootstrap 5)');
    }
    // Fallback cuối cùng
    else {
      console.error('jQuery and Bootstrap not found');
      alert('Lỗi: jQuery/Bootstrap không được tải. Vui lòng tải lại trang.');
    }
  } catch (e) {
    console.error('Lỗi mở modal tiền phòng:', e);
    alert('Lỗi khi mở modal: ' + e.message);
  }
};

window.showUtilitiesDetail = function(data) {
  console.log('showUtilitiesDetail called with data:', data);
  
  if (!data) {
    console.error('No data provided to showUtilitiesDetail');
    alert('Lỗi: Không có dữ liệu để hiển thị');
    return;
  }
  
  currentUtilitiesPaymentId = data.id;
  
  // Kiểm tra các element tồn tại trước khi set
  const elements = {
    utilitiesSinhVien: document.getElementById('utilitiesSinhVien'),
    utilitiesMaSV: document.getElementById('utilitiesMaSV'),
    utilitiesPhong: document.getElementById('utilitiesPhong'),
    utilitiesLabel: document.getElementById('utilitiesLabel'),
    utilitiesTienDien: document.getElementById('utilitiesTienDien'),
    utilitiesTienNuoc: document.getElementById('utilitiesTienNuoc'),
    utilitiesTotal: document.getElementById('utilitiesTotal'),
    utilitiesMethod: document.getElementById('utilitiesMethod'),
    utilitiesRequestedAt: document.getElementById('utilitiesRequestedAt'),
    utilitiesNote: document.getElementById('utilitiesNote'),
    adminNoteUtilities: document.getElementById('adminNoteUtilities')
  };
  
  // Set giá trị
  if (elements.utilitiesSinhVien) elements.utilitiesSinhVien.textContent = data.sinhVien || '-';
  if (elements.utilitiesMaSV) elements.utilitiesMaSV.textContent = data.maSv || '-';
  if (elements.utilitiesPhong) elements.utilitiesPhong.textContent = data.phong || '-';
  if (elements.utilitiesLabel) elements.utilitiesLabel.textContent = data.slot || '-';
  if (elements.utilitiesTienDien) elements.utilitiesTienDien.textContent = data.tienDien || '-';
  if (elements.utilitiesTienNuoc) elements.utilitiesTienNuoc.textContent = data.tienNuoc || '-';
  if (elements.utilitiesTotal) elements.utilitiesTotal.textContent = data.total || '-';
  if (elements.utilitiesMethod) elements.utilitiesMethod.textContent = data.method || '-';
  if (elements.utilitiesRequestedAt) elements.utilitiesRequestedAt.textContent = data.requestedAt || '-';
  if (elements.utilitiesNote) elements.utilitiesNote.textContent = data.note || '(Không có ghi chú)';
  if (elements.adminNoteUtilities) elements.adminNoteUtilities.value = data.adminNote || '';
  
  // Xử lý ảnh
  const imgWrapper = document.getElementById('utilitiesImageWrapper');
  const utilitiesDetailImage = document.getElementById('utilitiesDetailImage');
  if (imgWrapper && utilitiesDetailImage) {
    if (data.image && data.image.trim() !== '') {
      imgWrapper.style.display = 'block';
      utilitiesDetailImage.src = data.image;
    } else {
      imgWrapper.style.display = 'none';
    }
  }
  
  // Mở modal - ưu tiên jQuery (Bootstrap 4)
  const modalEl = document.getElementById('utilitiesDetailModal');
  if (!modalEl) {
    console.error('Modal element not found');
    alert('Lỗi: Không tìm thấy modal');
    return;
  }
  
  try {
    // Ưu tiên jQuery (Bootstrap 4)
    if (typeof $ !== 'undefined' && $.fn.modal) {
      $(modalEl).modal('show');
      console.log('Utilities modal shown successfully (jQuery/Bootstrap 4)');
    }
    // Fallback Bootstrap 5
    else if (window.bootstrap && bootstrap.Modal) {
      const modal = new bootstrap.Modal(modalEl);
      modal.show();
      console.log('Utilities modal shown successfully (Bootstrap 5)');
    }
    // Fallback cuối cùng
    else {
      console.error('jQuery and Bootstrap not found');
      alert('Lỗi: jQuery/Bootstrap không được tải. Vui lòng tải lại trang.');
    }
  } catch (e) {
    console.error('Lỗi mở modal điện nước:', e);
    alert('Lỗi khi mở modal: ' + e.message);
  }
}

// ===== Xử lý nút xem chi tiết =====
// Đơn giản hóa: dùng jQuery khi sẵn sàng
$(document).ready(function() {
  // Xử lý nút xem chi tiết slot
  $(document).on('click', '.slot-detail-btn', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    try {
      const $btn = $(this);
      const dataStr = $btn.attr('data-detail');
      
      if (!dataStr || dataStr.trim() === '') {
        alert('Lỗi: Không có dữ liệu để hiển thị');
        return false;
      }
      
      const data = JSON.parse(dataStr);
      
      if (window.showSlotDetail) {
        window.showSlotDetail(data);
      } else {
        alert('Lỗi: Hàm hiển thị chi tiết chưa được tải');
      }
    } catch (e) {
      console.error('Error:', e);
      alert('Lỗi: ' + e.message);
    }
    return false;
  });
  
  // Xử lý nút xem chi tiết utilities
  $(document).on('click', '.utilities-detail-btn, .open-utilities-detail-btn', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    try {
      const $btn = $(this);
      const dataStr = $btn.attr('data-detail');
      let data;
      
      if (dataStr && dataStr.trim() !== '') {
        data = JSON.parse(dataStr);
      } else {
        // Fallback: lấy từ các data attributes riêng lẻ
        data = {
          id: $btn.attr('data-id'),
          sinhVien: $btn.attr('data-sinh-vien') || 'N/A',
          maSv: $btn.attr('data-ma-sv') || 'N/A',
          phong: $btn.attr('data-phong') || 'N/A',
          slot: $btn.attr('data-slot') || 'N/A',
          tienDien: $btn.attr('data-tien-dien') || '-',
          tienNuoc: $btn.attr('data-tien-nuoc') || '-',
          total: $btn.attr('data-total') || '-',
          method: $btn.attr('data-method') || '-',
          requestedAt: $btn.attr('data-requested-at') || '-',
          status: $btn.attr('data-status') || '-',
          note: $btn.attr('data-note') || '',
          adminNote: $btn.attr('data-admin-note') || '',
          image: $btn.attr('data-image') || ''
        };
      }
      
      if (window.showUtilitiesDetail) {
        window.showUtilitiesDetail(data);
      } else {
        alert('Lỗi: Hàm hiển thị chi tiết chưa được tải');
      }
    } catch (e) {
      console.error('Error:', e);
      alert('Lỗi: ' + e.message);
    }
    return false;
  });
});

// ===== Xử lý nút xác nhận/từ chối =====
document.addEventListener('DOMContentLoaded', function() {
  document.getElementById('confirmSlotBtn')?.addEventListener('click', function() {
    const note = document.getElementById('adminNoteSlot').value;
    fetch(`/admin/payment-confirmation/slot/${currentSlotPaymentId}/confirm`, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
      body: JSON.stringify({ ghi_chu_admin: note })
    }).then(r => r.json()).then(d => {
      alert(d.message);
      location.reload();
    }).catch(e => alert('Lỗi: ' + e));
  });

  document.getElementById('rejectSlotBtn')?.addEventListener('click', function() {
    const note = document.getElementById('adminNoteSlot').value || 'Yêu cầu bị từ chối';
    fetch(`/admin/payment-confirmation/slot/${currentSlotPaymentId}/reject`, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
      body: JSON.stringify({ ghi_chu_admin: note })
    }).then(r => r.json()).then(d => {
      alert(d.message);
      location.reload();
    }).catch(e => alert('Lỗi: ' + e));
  });

  document.getElementById('confirmUtilitiesBtn')?.addEventListener('click', function() {
    const note = document.getElementById('adminNoteUtilities').value;
    fetch(`/admin/payment-confirmation/utilities/${currentUtilitiesPaymentId}/confirm`, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
      body: JSON.stringify({ ghi_chu_admin: note })
    }).then(r => r.json()).then(d => {
      alert(d.message);
      location.reload();
    }).catch(e => alert('Lỗi: ' + e));
  });

  document.getElementById('rejectUtilitiesBtn')?.addEventListener('click', function() {
    const note = document.getElementById('adminNoteUtilities').value || 'Yêu cầu bị từ chối';
    fetch(`/admin/payment-confirmation/utilities/${currentUtilitiesPaymentId}/reject`, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
      body: JSON.stringify({ ghi_chu_admin: note })
    }).then(r => r.json()).then(d => {
      alert(d.message);
      location.reload();
    }).catch(e => alert('Lỗi: ' + e));
  });

  // ===== Chọn nhiều & xác nhận hàng loạt =====
  function getCheckedValues(selector) {
    return Array.from(document.querySelectorAll(selector)).filter(i => i.checked).map(i => i.value);
  }

  function sendBulkAction(type, ids, action, note) {
    if (!ids.length) { alert('Vui lòng chọn ít nhất 1 yêu cầu.'); return; }
    if (!confirm(`Xác nhận thực hiện '${action}' cho ${ids.length} yêu cầu?`)) return;
    fetch('/admin/payment-confirmation/bulk-action', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
      body: JSON.stringify({ type: type, ids: ids, action: action, ghi_chu_admin: note })
    }).then(r => r.json()).then(d => {
      if (d && d.success) {
        alert(d.message || 'Thực hiện thành công');
        location.reload();
      } else {
        alert(d.message || 'Lỗi khi thực hiện');
      }
    }).catch(e => alert('Lỗi: ' + e));
  }

  // select-all handlers
  const selectAllSlotEls = document.querySelectorAll('.select-all-slot');
  selectAllSlotEls.forEach(function(el){
    el.addEventListener('change', function(){
      document.querySelectorAll('.select-item-slot').forEach(cb => cb.checked = el.checked);
    });
  });
  const selectAllUtilitiesEls = document.querySelectorAll('.select-all-utilities');
  selectAllUtilitiesEls.forEach(function(el){
    el.addEventListener('change', function(){
      document.querySelectorAll('.select-item-utilities').forEach(cb => cb.checked = el.checked);
    });
  });

  // batch buttons (all-tab)
  document.getElementById('batchConfirmSlotAll')?.addEventListener('click', function(){
    const ids = getCheckedValues('.select-item-slot');
    const note = document.getElementById('batchNoteSlotAll')?.value || null;
    sendBulkAction('slot', ids, 'confirm', note);
  });
  document.getElementById('batchRejectSlotAll')?.addEventListener('click', function(){
    const ids = getCheckedValues('.select-item-slot');
    const note = document.getElementById('batchNoteSlotAll')?.value || null;
    sendBulkAction('slot', ids, 'reject', note);
  });
  document.getElementById('batchConfirmUtilitiesAll')?.addEventListener('click', function(){
    const ids = getCheckedValues('.select-item-utilities');
    const note = document.getElementById('batchNoteUtilitiesAll')?.value || null;
    sendBulkAction('utilities', ids, 'confirm', note);
  });
  document.getElementById('batchRejectUtilitiesAll')?.addEventListener('click', function(){
    const ids = getCheckedValues('.select-item-utilities');
    const note = document.getElementById('batchNoteUtilitiesAll')?.value || null;
    sendBulkAction('utilities', ids, 'reject', note);
  });

  // batch buttons (per-tab)
  document.getElementById('batchConfirmSlot')?.addEventListener('click', function(){
    const ids = getCheckedValues('.select-item-slot');
    const note = document.getElementById('batchNoteSlot')?.value || null;
    sendBulkAction('slot', ids, 'confirm', note);
  });
  document.getElementById('batchRejectSlot')?.addEventListener('click', function(){
    const ids = getCheckedValues('.select-item-slot');
    const note = document.getElementById('batchNoteSlot')?.value || null;
    sendBulkAction('slot', ids, 'reject', note);
  });
  document.getElementById('batchConfirmUtilities')?.addEventListener('click', function(){
    const ids = getCheckedValues('.select-item-utilities');
    const note = document.getElementById('batchNoteUtilities')?.value || null;
    sendBulkAction('utilities', ids, 'confirm', note);
  });
  document.getElementById('batchRejectUtilities')?.addEventListener('click', function(){
    const ids = getCheckedValues('.select-item-utilities');
    const note = document.getElementById('batchNoteUtilities')?.value || null;
    sendBulkAction('utilities', ids, 'reject', note);
  });
});
</script>
@endpush

@endsection
