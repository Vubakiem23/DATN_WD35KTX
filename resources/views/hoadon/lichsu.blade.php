@extends('admin.layouts.admin')

@section('content')
<div class="container py-4">
  <h3>📜 Lịch sử hóa đơn đã thanh toán</h3>

  {{-- Thông báo thành công --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
    </div>
  @endif

  {{-- Kiểm tra danh sách hóa đơn --}}
  @if($hoaDons->isEmpty())
    <div class="alert alert-info">Chưa có hóa đơn nào được thanh toán.</div>
  @else
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>Tên phòng</th>
          <th>Số tiền đã thanh toán</th>
          <th>Ngày thanh toán</th>
          <th>Hình thức</th>
          <th>Biên lai</th>
          <th>Ghi chú</th>
          <th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        @foreach($hoaDons as $hoaDon)
        <tr>
          <td>{{ $hoaDon->phong->ten_phong ?? 'Không xác định' }}</td>
          <td>{{ number_format($hoaDon->thanh_tien, 0, ',', '.') }} VND</td>
          <td>{{ \Carbon\Carbon::parse($hoaDon->ngay_thanh_toan)->format('d/m/Y') }}</td>
          <td>{{ $hoaDon->hinh_thuc_thanh_toan_label }}</td>
          <td>
              @if($hoaDon->da_thanh_toan)
                <a href="{{ route('hoadon.bienlai', $hoaDon->id) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                  📎 Xem biên lai
                </a>
              @else
                <span class="text-muted">Chưa thanh toán</span>
              @endif
          </td>

          <td>{{ $hoaDon->ghi_chu_thanh_toan ?? 'Không có' }}</td>
          <td class="d-flex gap-2">
            <form action="{{ route('hoadon.destroy', $hoaDon->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa hóa đơn này?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-outline-danger btn-action" type="submit" title="Xóa"><i class="fa fa-trash"></i></button>
              <a href="{{ route('hoadon.export_pdf', $hoaDon->id) }}" target="_blank"  class="btn btn-outline-primary btn-action"" title=" In PDF"">🖨️</a>
                 
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

    {{-- Phân trang --}}
    <div class="mt-3">
      {{ $hoaDons->links() }}
    </div>
  @endif

  <a href="{{ route('hoadon.index') }}" class="btn btn-secondary mt-3">⬅️ Quay lại danh sách</a>
</div>
@endsection
