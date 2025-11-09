@extends('admin.layouts.admin')

@section('content')
<div class="x_panel">
    <div class="x_title">
        <h2>💳 Tạo Hóa Đơn Sự Cố #{{ $suco->id }}</h2>
        <div class="clearfix"></div>
    </div>

    <div class="x_content">

       

        {{-- 🧾 Thông tin sự cố --}}
        <table class="table table-bordered mb-4">
            <tr>
                <th width="25%">👨‍🎓 Sinh viên</th>
                <td>
                    @if($suco->sinhVien)
                        <strong>{{ $suco->sinhVien->ho_ten }}</strong><br>
                        <small>MSSV: {{ $suco->sinhVien->ma_sinh_vien ?? '---' }}</small><br>
                        <small>Email: {{ $suco->sinhVien->email ?? '---' }}</small>
                    @else
                        <em>Không xác định</em>
                    @endif
                </td>
            </tr>
            <tr>
                <th>🏠 Phòng</th>
                <td>{{ $suco->phong->ten_phong ?? '---' }}</td>
            </tr>
            <tr>
                <th>📝 Mô tả sự cố</th>
                <td>{{ $suco->mo_ta ?? '---' }}</td>
            </tr>
            <tr>
                <th>📅 Ngày gửi</th>
                <td>{{ \Carbon\Carbon::parse($suco->ngay_gui)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <th>⚙️ Trạng thái xử lý</th>
                <td>
                    <span class="badge 
                        @if($suco->trang_thai == 'Tiếp nhận') bg-secondary
                        @elseif($suco->trang_thai == 'Đang xử lý') bg-info
                        @elseif($suco->trang_thai == 'Hoàn thành') bg-success
                        @else bg-danger
                        @endif">
                        {{ $suco->trang_thai }}
                    </span>
                </td>
            </tr>
        </table>

        {{-- 💰 Form tạo hóa đơn --}}
        <div class="x_panel mt-4">
            <div class="x_title">
                <h2>🧾 Nhập giá tiền </h2>
                <div class="clearfix"></div>
            </div>

            <div class="x_content">
                <form action="{{ route('suco.luuHoaDon', $suco->id) }}" method="POST">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">💰 Giá tiền (VNĐ)</label>
                            <input type="number" name="payment_amount" 
                                   class="form-control" 
                                   placeholder="Nhập số tiền cần thanh toán" 
                                   value="{{ old('payment_amount', $suco->payment_amount ?? 0) }}" 
                                   required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">📅 Ngày tạo hóa đơn</label>
                            <input type="text" class="form-control" 
                                   value="{{ now()->format('d/m/Y') }}" disabled>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('suco.show', $suco->id) }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-save"></i> Lưu hóa đơn
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.table th { background-color: #f8f9fa; }
.badge {
    padding: 6px 10px;
    border-radius: 12px;
    color: #fff;
    font-size: 12px;
}
.bg-secondary { background-color: #6c757d !important; }
.bg-info { background-color: #17a2b8 !important; }
.bg-success { background-color: #28a745 !important; }
.bg-danger { background-color: #dc3545 !important; }
</style>
@endsection
