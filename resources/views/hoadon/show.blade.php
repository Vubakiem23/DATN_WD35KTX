@extends('admin.layouts.admin')

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

  @if(!$isDienNuocOnly)
  <div class="card shadow-sm mb-4">
    <div class="card-header fw-semibold text-uppercase">Chi tiết tiền phòng</div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4">
          <div class="p-3 border rounded bg-light">
            <div class="text-muted text-uppercase small">Số slot tính phí</div>
            <div class="fs-4 fw-semibold">{{ $hoaDon->slot_billing_count ?? 0 }}</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-3 border rounded bg-light">
            <div class="text-muted text-uppercase small">Đơn giá mỗi slot</div>
            <div class="fs-4 fw-semibold">{{ number_format($hoaDon->slot_unit_price ?? 0, 0, ',', '.') }} VND</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-3 border rounded bg-light">
            <div class="text-muted text-uppercase small">Tiền phòng</div>
            <div class="fs-4 fw-semibold text-success">{{ number_format($hoaDon->tien_phong_slot ?? 0, 0, ',', '.') }} VND</div>
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
              </tr>
            </thead>
            <tbody>
              @foreach($hoaDon->slot_breakdowns as $slot)
                <tr>
                  <td>{{ $slot['label'] }}</td>
                  <td>{{ $slot['sinh_vien'] }}</td>
                  <td class="fw-semibold">{{ number_format($slot['tien_phong'] ?? 0, 0, ',', '.') }} VND</td>
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
          <table class="table table-striped text-center mb-0">
            <thead class="table-light">
              <tr>
                <th>Slot</th>
                <th>Sinh viên</th>
                <th>Tiền điện</th>
                <th>Tiền nước</th>
                <th>Tổng điện + nước</th>
              </tr>
            </thead>
            <tbody>
              @foreach(($hoaDon->slot_breakdowns_dien_nuoc ?? $hoaDon->slot_breakdowns) as $slot)
                <tr>
                  <td>{{ $slot['label'] }}</td>
                  <td>{{ $slot['sinh_vien'] }}</td>
                  <td>{{ number_format($slot['tien_dien'] ?? 0, 0, ',', '.') }} VND</td>
                  <td>{{ number_format($slot['tien_nuoc'] ?? 0, 0, ',', '.') }} VND</td>
                  <td class="fw-semibold">
                    {{ number_format(($slot['tien_dien'] ?? 0) + ($slot['tien_nuoc'] ?? 0), 0, ',', '.') }} VND
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
@endsection
