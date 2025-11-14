@extends('admin.layouts.admin')

@section('content')
<div class="container py-4 ">
  <h3 class="mb-1">📄 Chi tiết hóa đơn phòng {{ $hoaDon->phong->ten_phong }}</h3>
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

  <div class="card shadow-sm">
    <div class="card-body">
      <table class="table table-bordered text-start table-invoice mb-0">
      <tr><th>Khu</th><td>{{ optional($hoaDon->phong->khu)->ten_khu ?? 'Không rõ khu' }}</td></tr>
      <tr><th>Tên Phòng</th><td>{{ optional($hoaDon->phong)->ten_phong ?? 'Không rõ' }}</td></tr>
      <tr><th>Tháng</th><td>{{ \Carbon\Carbon::parse($hoaDon->created_at)->format('m/Y') }}</td></tr>
      <tr><th>Loại Phòng</th><td>{{ optional($hoaDon->phong)->loai_phong ?? 'Không rõ' }}</td></tr>
      <tr><th>Điện đã dùng</th><td>{{ $hoaDon->so_dien_moi - $hoaDon->so_dien_cu }}</td></tr>
      <tr><th>Tiền điện</th><td>{{ number_format($hoaDon->tien_dien, 0, ',', '.') }} VND</td></tr>
      <tr><th>Nước đã dùng</th><td>{{ $hoaDon->so_nuoc_moi - $hoaDon->so_nuoc_cu }}</td></tr>
      <tr><th>Tiền nước</th><td>{{ number_format($hoaDon->tien_nuoc, 0, ',', '.') }} VND</td></tr>
      <tr><th>Giá phòng</th><td>{{ number_format($hoaDon->phong->gia_phong, 0, ',', '.') }} VND</td></tr>
        <tr class="total-row"><th>Thành tiền</th><td><strong>{{ number_format($hoaDon->thanh_tien, 0, ',', '.') }} VND</strong></td></tr>
      <tr><th>Tính từ ngày</th><td>{{ $hoaDon->created_at ? $hoaDon->created_at->format('d/m/Y') : '-' }}</td></tr>
      <tr><th>Ngày chốt</th><td>{{ $hoaDon->created_at->format('d/m/Y H:i') }}</td></tr>
      <tr><th>Ngày thanh toán</th><td>{{ $hoaDon->ngay_thanh_toan ? \Carbon\Carbon::parse($hoaDon->ngay_thanh_toan)->format('d/m/Y') : '-' }}</td></tr>
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
    </div>
  </div>
</div>
@endsection
