@extends('client.layouts.app')

@section('title', 'Lịch sử tiền phòng')

@section('content')
  <h3 class="mb-4 text-primary fw-bold">
    <i class="fa fa-bed"></i> Lịch sử thanh toán tiền phòng của bạn
  </h3>

  <div class="card shadow-sm">
    <div class="card-body">
      <table class="table table-hover table-striped align-middle">
        <thead class="table-primary">
          <tr>
            <th scope="col">STT</th>
            <th scope="col">Khu</th>
            <th scope="col">Phòng</th>
            <th scope="col">Số tiền</th>
            <th scope="col">Tháng</th>
            <th scope="col">Ngày thanh toán</th>
          </tr>
        </thead>
        <tbody>
          @forelse($hoaDons as $slot)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>{{ optional($slot->hoaDon->phong->khu)->ten_khu ?? 'N/A' }}</td>
              <td>{{ optional($slot->hoaDon->phong)->ten_phong }}</td>
              <td class="fw-semibold text-success">
                {{ number_format($slot->so_tien, 0, ',', '.') }} VND
              </td>
              <td>{{ \Carbon\Carbon::parse($slot->hoaDon->created_at)->format('m/Y') }}</td>
              <td>{{ \Carbon\Carbon::parse($slot->ngay_thanh_toan)->format('d/m/Y') }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted py-3">
                <i class="fa fa-info-circle"></i> Bạn chưa có khoản tiền phòng nào đã thanh toán.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection
