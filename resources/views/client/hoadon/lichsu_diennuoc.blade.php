@extends('client.layouts.app')

@section('title', 'Lịch sử điện · nước')
@section('content')

<h3 class="mb-4 text-primary fw-bold">
  <i class="fa fa-bolt"></i> Lịch sử thanh toán điện · nước của bạn
</h3>

<div class="card shadow-sm">
  <div class="card-body">
    <table class="table table-hover table-striped align-middle">
      <thead class="table-primary">
        <tr>
          <th scope="col">STT</th>
          <th scope="col">Phòng</th>
          <th scope="col">Tiền điện</th>
          <th scope="col">Tiền nước</th>
          <th scope="col">Tháng</th>
          <th scope="col">Ngày thanh toán</th>
        </tr>
      </thead>
      <tbody>
        @forelse($hoaDons as $hoaDon)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ optional($hoaDon->phong)->ten_phong }}</td>
            <td class="text-danger fw-semibold">
              {{ number_format($hoaDon->tien_dien, 0, ',', '.') }} VND
            </td>
            <td class="text-info fw-semibold">
              {{ number_format($hoaDon->tien_nuoc, 0, ',', '.') }} VND
            </td>
            <td>{{ \Carbon\Carbon::parse($hoaDon->created_at)->format('m/Y') }}</td>
            <td>{{ \Carbon\Carbon::parse($hoaDon->ngay_thanh_toan_dien_nuoc)->format('d/m/Y') }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-3">
              <i class="fa fa-info-circle"></i> Bạn chưa có hóa đơn điện · nước nào đã thanh toán.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
