@extends('admin.layouts.admin')

@section('content')
<div class="x_panel">
    <div class="x_title">
        <h2>🧾 Danh sách hóa đơn sự cố</h2>
        <div class="clearfix"></div>
    </div>

    <div class="x_content">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- ✅ Bọc bảng để tránh tràn giao diện --}}
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th width="4%">#</th>
                        <th width="14%"> Sinh viên</th>
                        <th width="8%"> Phòng</th>
                        <th width="25%"> Mô tả sự cố</th>
                        <th width="10%"> Giá tiền</th>
                        <th width="12%"> Trạng thái</th>
                        <th width="9%"> Ngày tạo</th>
                        <th width="9%"> Ngày TT</th>
                        <th width="9%"> Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hoaDons as $index => $hd)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $hd->sinhVien->ho_ten ?? '---' }}</strong><br>
                            <small class="text-muted">MSSV: {{ $hd->sinhVien->ma_sinh_vien ?? '---' }}</small>
                        </td>
                        <td>{{ $hd->phong->ten_phong ?? '---' }}</td>
                        <td class="text-start">
                            {{ \Illuminate\Support\Str::limit($hd->suCo->mo_ta ?? '---', 60) }}
                        </td>
                        <td><strong>{{ number_format($hd->amount, 0, ',', '.') }}</strong> VNĐ</td>
                        <td>
                            @if($hd->status === 'Đã thanh toán')
                                <span class="badge bg-success">Đã thanh toán</span>
                            @else
                                <span class="badge bg-warning text-dark">Chưa thanh toán</span>
                            @endif
                        </td>
                        <td>
                            {{ $hd->ngay_tao ? \Carbon\Carbon::parse($hd->ngay_tao)->format('d/m/Y') : '-' }}
                        </td>
                        <td>
                            @if($hd->status === 'Đã thanh toán')
                                {{ \Carbon\Carbon::parse($hd->ngay_thanh_toan)->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($hd->status !== 'Đã thanh toán')
                            <form action="{{ route('suco.hoadon.thanhtoan', $hd->id) }}" method="POST" onsubmit="return confirm('Xác nhận đã thanh toán?')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">
                                    ✅ Xác nhận
                                </button>
                            </form>
                            @else
                                <em>—</em>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9">⚠️ Chưa có hóa đơn sự cố nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <a href="{{ route('suco.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Quay lại danh sách sự cố
            </a>
        </div>

    </div>
</div>

<style>
.table th {
    background-color: #f8f9fa;
    vertical-align: middle;
    white-space: nowrap;
}
.table td {
    vertical-align: middle;
    font-size: 14px;
    word-wrap: break-word;
}
.badge {
    padding: 6px 10px;
    border-radius: 12px;
    font-size: 12px;
}
.bg-success { background-color: #28a745 !important; }
.bg-warning { background-color: #ffc107 !important; }
.table-responsive { overflow-x: auto; }
</style>
@endsection
