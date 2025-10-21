@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">📋 Danh sách hóa đơn</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Bộ lọc -->
    <form method="GET" action="{{ route('hoadon.index') }}" class="row g-3 mb-4">
        <div class="col-md-3">
            <label class="form-label">Loại phí:</label>
            <select name="loai_phi" class="form-select">
                <option value="">-- Tất cả --</option>
                <option value="Tiền phòng">Tiền phòng</option>
                <option value="Điện">Điện</option>
                <option value="Nước">Nước</option>
                <option value="Dịch vụ">Dịch vụ</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Trạng thái:</label>
            <select name="trang_thai" class="form-select">
                <option value="">-- Tất cả --</option>
                <option value="Chưa thanh toán">Chưa thanh toán</option>
                <option value="Đã thanh toán">Đã thanh toán</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Mã sinh viên:</label>
            <input type="text" name="sinh_vien_id" class="form-control" placeholder="VD: 1001">
        </div>
        <div class="col-md-3 d-flex align-items-end justify-content-between">
            <button type="submit" class="btn btn-primary">Lọc</button>
            <a href="{{ route('hoadon.create') }}" class="btn btn-success">+ Lập hóa đơn mới</a>
        </div>
    </form>

    <!-- Bảng hóa đơn -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="">
                <tr>
                    <th>ID</th>
                    <th>Sinh viên</th>
                    <th>Loại phí</th>
                    <th>Số tiền</th>
                    <th>Ngày tạo</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
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
                    <td>
                        <span class="badge {{ $hoaDon->trang_thai == 'Chưa thanh toán' ? 'bg-danger' : 'bg-success' }}">
                            {{ $hoaDon->trang_thai }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('hoadon.edit', $hoaDon->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                        <form action="{{ route('hoadon.destroy', $hoaDon->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Xóa hóa đơn này?')">Xóa</button>
                        </form>
                        <form action="{{ route('hoadon.duplicate', $hoaDon->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-secondary">Sao chép</button>
                        </form>
                        @if($hoaDon->trang_thai == 'Chưa thanh toán')
                            <form action="{{ route('hoadon.pay', $hoaDon->id) }}" method="POST" class="d-inline">
                                @csrf
                                
                                <button type="submit" class="btn btn-sm btn-success">💳 Thanh toán</button>
                            </form>
                        @endif

                        <form action="{{ route('hoadon.send', $hoaDon->id) }}" method="POST" class="d-inline">
                            @csrf
                            
                        </form>
                        <a href="{{ route('hoadon.pdf', $hoaDon->id) }}" target="_blank" class="btn btn-sm btn-dark">PDF</a>

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">Không có hóa đơn nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Phân trang -->
    <div class="d-flex justify-content-center">
        {{ $hoaDons->links() }}
    </div>
</div>
@endsection
