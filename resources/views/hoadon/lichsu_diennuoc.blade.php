@extends('admin.layouts.admin')

@section('content')
<div class="container py-4">
  <h3>💡🚰 Lịch sử hóa đơn điện nước đã thanh toán</h3>
  <form method="GET" action="{{ route('hoadon.lichsu_diennuoc') }}" class="row g-3 mb-4">
    <div class="col-md-3">
      <label for="ngay" class="form-label">Ngày</label>
      <input type="date" name="ngay" id="ngay" class="form-control" value="{{ request('ngay') }}">
    </div>
    <div class="col-md-3 d-flex align-items-end">
      <button type="submit" class="btn btn-primary">🔍 Lọc</button>
    </div>
  </form>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
    </div>
  @endif

  @if($hoaDons->isEmpty())
    <div class="alert alert-info">Chưa có hóa đơn điện nước nào được thanh toán.</div>
  @else
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>Khu</th>
          <th>Tên phòng</th>
          <th>Điện cũ</th>
          <th>Điện mới</th>
          <th>Đơn giá điện</th>
          <th>Nước cũ</th>
          <th>Nước mới</th>
          <th>Đơn giá nước</th>
          <th>Thành tiền</th>
          <th>Ngày thanh toán</th>
     
          <th>Ghi chú</th>
          <th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        @foreach($hoaDons as $hoaDon)
        <tr>
          <td>{{ optional($hoaDon->phong->khu)->ten_khu ?? 'Không rõ khu' }}</td>
          <td>{{ $hoaDon->phong->ten_phong ?? 'Không xác định' }}</td>
          <td>{{ $hoaDon->so_dien_cu }}</td>
          <td>{{ $hoaDon->so_dien_moi }}</td>
          <td>{{ number_format($hoaDon->don_gia_dien, 0, ',', '.') }} VND</td>
          <td>{{ $hoaDon->so_nuoc_cu }}</td>
          <td>{{ $hoaDon->so_nuoc_moi }}</td>
          <td>{{ number_format($hoaDon->don_gia_nuoc, 0, ',', '.') }} VND</td>
          <td>{{ number_format($hoaDon->thanh_tien, 0, ',', '.') }} VND</td>
          <td>{{ \Carbon\Carbon::parse($hoaDon->ngay_thanh_toan_dien_nuoc)->format('d/m/Y') }}</td>
          <td>{{ $hoaDon->ghi_chu_thanh_toan_dien_nuoc ?? 'Không có' }}</td>
          <td class="d-flex gap-2">
            <form action="{{ route('hoadon.destroy', $hoaDon->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa hóa đơn này?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-outline-danger btn-action" title="Xóa"><i class="fa fa-trash"></i></button>
              <a href="{{ route('hoadon.export_pdf', $hoaDon->id) }}" target="_blank" class="btn btn-outline-primary btn-action" title="In PDF">🖨️</a>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <div class="mt-3">
      {{ $hoaDons->links() }}
    </div>
  @endif

  <a href="{{ route('hoadon.diennuoc') }}" class="btn btn-secondary mt-3">⬅️ Quay lại danh sách</a>
</div>
@endsection
