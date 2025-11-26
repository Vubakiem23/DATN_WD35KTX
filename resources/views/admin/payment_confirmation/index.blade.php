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
          <h3 class="mb-1">📋 Quản lý yêu cầu thanh toán</h3>
          <p class="text-muted mb-0">Xem và xác nhận yêu cầu thanh toán từ sinh viên</p>
        </div>
      </div>
    </div>
  </div>
  <!-- Filter & Search -->
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-3">
          <label class="form-label fw-semibold">Loại thanh toán</label>
          <select name="type" class="form-select">
            <option value="all" {{ $type === 'all' ? 'selected' : '' }}>Tất cả</option>
            <option value="slot" {{ $type === 'slot' ? 'selected' : '' }}>📄 Tiền phòng</option>
            <option value="utilities" {{ $type === 'utilities' ? 'selected' : '' }}>⚡ Điện nước</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold">Trạng thái</label>
          <select name="status" class="form-select">
            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Tất cả</option>
            <option value="cho_xac_nhan" {{ $status === 'cho_xac_nhan' ? 'selected' : '' }}>⏳ Chờ xác nhận</option>
            <option value="da_thanh_toan" {{ $status === 'da_thanh_toan' ? 'selected' : '' }}>✅ Đã xác nhận</option>
            <option value="chua_thanh_toan" {{ $status === 'chua_thanh_toan' ? 'selected' : '' }}>❌ Chưa thanh toán</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Tìm kiếm</label>
          <input type="text" name="search" class="form-control" placeholder="Sinh viên, phòng, hóa đơn..." value="{{ $search ?? '' }}">
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary w-100">
            <i class="fa fa-search me-1"></i>Tìm kiếm
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Tabs -->
  <div class="card shadow-sm mb-4">
    <div class="card-header">
      <ul class="nav nav-tabs card-header-tabs" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" id="all-tab" data-bs-toggle="tab" href="#all" role="tab">
            <i class="fa fa-list me-2"></i>Tất cả ({{ $slotPayments->count() + $utilitiesPayments->count() }})
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="slot-tab" data-bs-toggle="tab" href="#slot" role="tab">
            <i class="fa fa-home me-2"></i>Tiền phòng ({{ $slotPayments->count() }})
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="utilities-tab" data-bs-toggle="tab" href="#utilities" role="tab">
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
                        <td><span class="badge bg-secondary">{{ $payment->slot_label }}</span></td>
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
                        <td><span class="{{ $statusClass }}">{{ $statusText }}</span></td>
                        <td>
                          <button type="button" class="btn btn-sm btn-outline-primary" onclick="showSlotDetail(@json($slotDetailData))">
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
                        <td><span class="badge bg-secondary">{{ $payment->slot_label }}</span></td>
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
                        <td><span class="{{ $statusClass }}">{{ $statusText }}</span></td>
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
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="showSlotDetail(@json($slotDetailData2))">
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
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="showUtilitiesDetail(@json($utilitiesDetailData2))">
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
<div class="modal fade" id="slotDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-primary text-white border-0">
        <h5 class="modal-title fw-bold">
          <i class="fa fa-receipt me-2"></i>Chi tiết yêu cầu tiền phòng
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <div class="row g-3">
          <div class="col-md-6">
            <div class="card border-0 bg-light">
              <div class="card-body">
                <h6 class="text-primary fw-bold mb-3">👤 Sinh viên</h6>
                <div class="mb-3">
                  <small class="text-muted">Họ tên</small><br>
                  <strong id="slotSinhVien">-</strong>
                </div>
                <div class="mb-3">
                  <small class="text-muted">Mã SV</small><br>
                  <strong id="slotMaSV">-</strong>
                </div>
                <div>
                  <small class="text-muted">Phòng</small><br>
                  <strong id="slotPhong">-</strong>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card border-0 bg-light">
              <div class="card-body">
                <h6 class="text-success fw-bold mb-3">💰 Tiền phòng</h6>
                <div class="mb-3">
                  <small class="text-muted">Slot</small><br>
                  <span class="badge bg-secondary" id="slotLabel">-</span>
                </div>
                <div class="mb-3">
                  <small class="text-muted">Số tiền</small><br>
                  <span class="fw-bold text-success" id="slotAmount">-</span>
                </div>
                <div>
                  <small class="text-muted">Hình thức</small><br>
                  <strong id="slotMethod">-</strong>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row g-3 mt-2">
          <div class="col-md-6">
            <div class="card border-0 bg-light">
              <div class="card-body">
                <h6 class="text-warning fw-bold mb-3">📅 Thời gian</h6>
                <div class="mb-3">
                  <small class="text-muted">Ngày gửi</small><br>
                  <span id="slotRequestedAt">-</span>
                </div>
                <div>
                  <small class="text-muted">Trạng thái</small><br>
                  <span id="slotStatus" class="badge">-</span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card border-0 bg-light">
              <div class="card-body">
                <h6 class="text-info fw-bold mb-3">📝 Ghi chú SV</h6>
                <p id="slotNote" class="mb-0 text-muted small">-</p>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-4" id="slotImageWrapper" style="display: none;">
          <h6 class="fw-bold mb-2">🖼️ Ảnh chuyển khoản</h6>
          <div class="text-center">
            <img id="slotDetailImage" src="" alt="Ảnh chuyển khoản" class="img-fluid rounded shadow" style="max-height: 300px;">
          </div>
        </div>

        <div class="mt-4">
          <h6 class="fw-bold mb-2">✏️ Ghi chú quản lý</h6>
          <textarea id="adminNoteSlot" class="form-control" rows="3" placeholder="Nhập ghi chú của quản lý..." style="max-height: 100px;"></textarea>
        </div>
      </div>
      <div class="modal-footer border-0 bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
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
<div class="modal fade" id="utilitiesDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-success text-white border-0">
        <h5 class="modal-title fw-bold">
          <i class="fa fa-leaf me-2"></i>Chi tiết yêu cầu điện nước
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <div class="row g-3">
          <div class="col-md-6">
            <div class="card border-0 bg-light">
              <div class="card-body">
                <h6 class="text-primary fw-bold mb-3">👤 Sinh viên</h6>
                <div class="mb-3">
                  <small class="text-muted">Họ tên</small><br>
                  <strong id="utilitiesSinhVien">-</strong>
                </div>
                <div class="mb-3">
                  <small class="text-muted">Mã SV</small><br>
                  <strong id="utilitiesMaSV">-</strong>
                </div>
                <div>
                  <small class="text-muted">Phòng</small><br>
                  <strong id="utilitiesPhong">-</strong>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card border-0 bg-light">
              <div class="card-body">
                <h6 class="text-warning fw-bold mb-3">⚡ Điện · Nước</h6>
                <div class="mb-3">
                  <small class="text-muted">Slot</small><br>
                  <span class="badge bg-secondary" id="utilitiesLabel">-</span>
                </div>
                <div class="mb-3">
                  <small class="text-muted">Tiền điện</small><br>
                  <span class="fw-semibold text-danger" id="utilitiesTienDien">-</span>
                </div>
                <div>
                  <small class="text-muted">Tiền nước</small><br>
                  <span class="fw-semibold text-info" id="utilitiesTienNuoc">-</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row g-3 mt-2">
          <div class="col-md-4">
            <div class="card border-0 bg-light">
              <div class="card-body">
                <h6 class="text-success fw-bold mb-2">💰 Tổng tiền</h6>
                <span class="fw-bold fs-5" id="utilitiesTotal">-</span>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card border-0 bg-light">
              <div class="card-body">
                <h6 class="text-muted fw-bold mb-2">Hình thức</h6>
                <span id="utilitiesMethod">-</span>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card border-0 bg-light">
              <div class="card-body">
                <h6 class="text-warning fw-bold mb-2">Ngày gửi</h6>
                <small id="utilitiesRequestedAt">-</small>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-4">
          <h6 class="text-info fw-bold mb-2">📝 Ghi chú của SV</h6>
          <div class="card border-0 bg-light">
            <div class="card-body">
              <p id="utilitiesNote" class="mb-0 text-muted small">-</p>
            </div>
          </div>
        </div>

        <div class="mt-4" id="utilitiesImageWrapper" style="display: none;">
          <h6 class="fw-bold mb-2">🖼️ Ảnh chuyển khoản</h6>
          <div class="text-center">
            <img id="utilitiesDetailImage" src="" alt="Ảnh chuyển khoản" class="img-fluid rounded shadow" style="max-height: 300px;">
          </div>
        </div>

        <div class="mt-4">
          <h6 class="fw-bold mb-2">✏️ Ghi chú quản lý</h6>
          <textarea id="adminNoteUtilities" class="form-control" rows="3" placeholder="Nhập ghi chú của quản lý..." style="max-height: 100px;"></textarea>
        </div>
      </div>
      <div class="modal-footer border-0 bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
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

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
let currentSlotPaymentId = null;
let currentUtilitiesPaymentId = null;

// ===== Hàm hiển thị modal chi tiết =====
function showSlotDetail(data) {
  console.log('showSlotDetail called with data:', data);
  currentSlotPaymentId = data.id;
  document.getElementById('slotSinhVien').textContent = data.sinhVien || '-';
  document.getElementById('slotMaSV').textContent = data.maSv || '-';
  document.getElementById('slotPhong').textContent = data.phong || '-';
  document.getElementById('slotLabel').textContent = data.slot || '-';
  document.getElementById('slotAmount').textContent = data.amount || '-';
  document.getElementById('slotMethod').textContent = data.method || '-';
  document.getElementById('slotRequestedAt').textContent = data.requestedAt || '-';
  document.getElementById('slotStatus').textContent = data.status || '-';
  document.getElementById('slotNote').textContent = data.note || '(Không có ghi chú)';
  document.getElementById('adminNoteSlot').value = data.adminNote || '';
  
  const imgWrapper = document.getElementById('slotImageWrapper');
  if (data.image && data.image.trim() !== '') {
    imgWrapper.style.display = 'block';
    document.getElementById('slotDetailImage').src = data.image;
  } else {
    imgWrapper.style.display = 'none';
  }
  
  try {
    const modal = new bootstrap.Modal(document.getElementById('slotDetailModal'));
    modal.show();
    console.log('Slot modal shown successfully');
  } catch (e) {
    console.error('Lỗi mở modal tiền phòng:', e);
  }
}

function showUtilitiesDetail(data) {
  console.log('showUtilitiesDetail called with data:', data);
  currentUtilitiesPaymentId = data.id;
  document.getElementById('utilitiesSinhVien').textContent = data.sinhVien || '-';
  document.getElementById('utilitiesMaSV').textContent = data.maSv || '-';
  document.getElementById('utilitiesPhong').textContent = data.phong || '-';
  document.getElementById('utilitiesLabel').textContent = data.slot || '-';
  document.getElementById('utilitiesTienDien').textContent = data.tienDien || '-';
  document.getElementById('utilitiesTienNuoc').textContent = data.tienNuoc || '-';
  document.getElementById('utilitiesTotal').textContent = data.total || '-';
  document.getElementById('utilitiesMethod').textContent = data.method || '-';
  document.getElementById('utilitiesRequestedAt').textContent = data.requestedAt || '-';
  document.getElementById('utilitiesNote').textContent = data.note || '(Không có ghi chú)';
  document.getElementById('adminNoteUtilities').value = data.adminNote || '';
  
  const imgWrapper = document.getElementById('utilitiesImageWrapper');
  if (data.image && data.image.trim() !== '') {
    imgWrapper.style.display = 'block';
    document.getElementById('utilitiesDetailImage').src = data.image;
  } else {
    imgWrapper.style.display = 'none';
  }
  
  try {
    const modal = new bootstrap.Modal(document.getElementById('utilitiesDetailModal'));
    modal.show();
    console.log('Utilities modal shown successfully');
  } catch (e) {
    console.error('Lỗi mở modal điện nước:', e);
  }
}

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
