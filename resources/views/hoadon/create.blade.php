@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">🧾 Lập hóa đơn mới</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Lỗi!</strong> Vui lòng kiểm tra lại thông tin:
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('hoadon.store') }}" method="POST" class="row g-3">
        @csrf

        <div class="col-md-6">
            <label class="form-label">Mã sinh viên</label>
            <input type="number" name="sinh_vien_id" class="form-control" value="{{ old('sinh_vien_id') }}" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Loại phí</label>
            <select name="loai_phi" class="form-select" required>
                <option value="">-- Chọn loại phí --</option>
                <option value="Tiền phòng">Tiền phòng</option>
                <option value="Điện">Điện</option>
                <option value="Nước">Nước</option>
                <option value="Dịch vụ">Dịch vụ</option>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Số tiền (VNĐ)</label>
            <input type="number" name="so_tien" class="form-control" value="{{ old('so_tien') }}" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Ngày tạo</label>
            <input type="date" name="ngay_tao" class="form-control" value="{{ old('ngay_tao') }}" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Trạng thái</label>
            <select name="trang_thai" class="form-select" required>
                <option value="Chưa thanh toán">Chưa thanh toán</option>
                <option value="Đã thanh toán">Đã thanh toán</option>
            </select>
        </div>

        <div class="col-12 d-flex justify-content-between mt-3">
            <button type="submit" class="btn btn-success">💾 Lưu hóa đơn</button>
            <a href="{{ route('hoadon.index') }}" class="btn btn-secondary">⬅ Quay lại danh sách</a>
        </div>
    </form>
</div>
@endsection
