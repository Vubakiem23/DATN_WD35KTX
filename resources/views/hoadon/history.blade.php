@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">📜 Lịch sử thanh toán</h2>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-success">
                <tr>
                    <th>ID</th>
                    <th>Sinh viên</th>
                    <th>Loại phí</th>
                    <th>Số tiền</th>
                    <th>Ngày tạo</th>
                    <th>Ngày thanh toán</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hoaDons as $hoaDon)
                <tr>
                    <td>{{ $hoaDon->id }}</td>
                    <td>{{ $hoaDon->sinhVien->ho_ten ?? 'Không rõ' }}</td>
                    <td>{{ $hoaDon->loai_phi }}</td>
                    <td>{{ number_format($hoaDon->so_tien, 0, ',', '.') }} đ</td>
                    <td>{{ $hoaDon->ngay_tao }}</td>
                    <td>{{ $hoaDon->ngay_thanh_toan ?? 'Chưa cập nhật' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">Chưa có hóa đơn nào được thanh toán.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $hoaDons->links() }}
    </div>
</div>
@endsection
