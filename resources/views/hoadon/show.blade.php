@extends('admin.layouts.admin')

@section('content')
<div class="container py-4 ">
  <h3>📄 Chi tiết hóa đơn phòng {{ $hoaDon->phong->ten_phong }}</h3>
  <h2 class="mb-4">Kiểm tra kĩ thông tin trước khi thanh toán</h2>

  
    <table class="table table-bordered text-start">
      <tr><th>Khu</th><td>{{ optional($hoaDon->phong->khu)->ten_khu ?? 'Không rõ khu' }}</td></tr>
      <tr><th>Tên Phòng</th><td>{{ optional($hoaDon->phong)->ten_phong ?? 'Không rõ' }}</td></tr>
      <tr><th>Tháng</th><td>{{ \Carbon\Carbon::parse($hoaDon->created_at)->format('m/Y') }}</td></tr>
      <tr><th>Loại Phòng</th><td>{{ optional($hoaDon->phong)->loai_phong ?? 'Không rõ' }}</td></tr>
      <tr><th>Điện đã dùng</th><td>{{ $hoaDon->so_dien_moi - $hoaDon->so_dien_cu }}</td></tr>
      <tr><th>Tiền điện</th><td>{{ number_format($hoaDon->tien_dien, 0, ',', '.') }} VND</td></tr>
      <tr><th>Nước đã dùng</th><td>{{ $hoaDon->so_nuoc_moi - $hoaDon->so_nuoc_cu }}</td></tr>
      <tr><th>Tiền nước</th><td>{{ number_format($hoaDon->tien_nuoc, 0, ',', '.') }} VND</td></tr>
      <tr><th>Giá phòng</th><td>{{ number_format($hoaDon->phong->gia_phong, 0, ',', '.') }} VND</td></tr>
      <tr><th>Thành tiền</th><td><strong>{{ number_format($hoaDon->thanh_tien, 0, ',', '.') }} VND</strong></td></tr>
      <tr><th>Tính từ ngày</th><td>{{ $hoaDon->created_at ? $hoaDon->created_at->format('d/m/Y') : '-' }}</td></tr>
      <tr><th>Ngày chốt</th><td>{{ $hoaDon->created_at->format('d/m/Y H:i') }}</td></tr>
      <tr><th>Ngày thanh toán</th><td>{{ $hoaDon->ngay_thanh_toan ? \Carbon\Carbon::parse($hoaDon->ngay_thanh_toan)->format('d/m/Y') : '-' }}</td></tr>
      <tr><th>Trạng thái</th><td>{{ $hoaDon->trang_thai ?? 'Chưa thanh toán' }}</td></tr>
    </table>
 

  <a href="{{ route('hoadon.index') }}" class="btn btn-secondary mt-3">⬅️ Quay lại danh sách</a>
</div>
@endsection
