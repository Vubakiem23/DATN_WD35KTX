@extends('admin.layouts.admin')

@section('title', 'Chi tiết phòng')

@section('content')
<div class="container mt-4">

    <h2 class="text-center mb-4 text-primary">
        CHI TIẾT HÓA ĐƠN PHÒNG {{ $phong->ten_phong }}
    </h2>

    {{-- CHƯA THANH TOÁN --}}
    <div class="card mb-4">
        <div class="card-header bg-danger text-white">
            <h4 class="mb-0">Sinh viên CHƯA thanh toán</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Sinh viên</th>
                        <th>Tổng tiền</th>
                        <th>Ngày lập hóa đơn</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($chuaThanhToan as $key => $payment)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $payment->sinhVien->ho_ten ?? '-' }}</td>
                            <td>{{ number_format($payment->tong_tien) }}đ</td>
                            <td>{{ $payment->hoaDon->ngay_lap ? $payment->hoaDon->ngay_lap->format('d/m/Y') : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-danger">Không có sinh viên chưa thanh toán</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ĐÃ THANH TOÁN --}}
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">Sinh viên ĐÃ thanh toán</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Sinh viên</th>
                        <th>Tổng tiền</th>
                        <th>Ngày thanh toán</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($daThanhToan as $key => $payment)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $payment->sinhVien->ho_ten ?? '-' }}</td>
                            <td>{{ number_format($payment->tong_tien) }}đ</td>
                            <td>{{ $payment->ngay_thanh_toan ? $payment->ngay_thanh_toan->format('d/m/Y') : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-success">Tất cả sinh viên đã thanh toán</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
