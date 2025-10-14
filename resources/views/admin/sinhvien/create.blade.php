@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">
    <h3>➕ Thêm sinh viên mới</h3>
    <hr>

    <form method="POST" action="{{ route('sinhvien.store') }}">
        @csrf

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Mã sinh viên</label>
                <input type="text" name="ma_sinh_vien" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Họ và tên</label>
                <input type="text" name="ho_ten" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>Ngày sinh</label>
                <input type="date" name="ngay_sinh" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Giới tính</label>
                <select name="gioi_tinh" class="form-select" required>
                    <option value="">-- Chọn giới tính --</option>
                    <option value="Nam">Nam</option>
                    <option value="Nữ">Nữ</option>
                    <option value="Khác">Khác</option>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label>Quê quán</label>
                <input type="text" name="que_quan" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Nơi ở hiện tại</label>
                <input type="text" name="noi_o_hien_tai" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>Lớp</label>
                <input type="text" name="lop" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Ngành</label>
                <input type="text" name="nganh" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>Khóa học</label>
                <input type="text" name="khoa_hoc" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Số điện thoại</label>
                <input type="text" name="so_dien_thoai" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>Phòng</label>
                <select name="phong_id" class="form-select" required>
                    <option value="">-- Chọn phòng --</option>
                    @foreach($phongs as $phong)
                        <option value="{{ $phong->id }}">{{ $phong->ten_phong ?? 'Phòng '.$phong->id }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label>Trạng thái hồ sơ</label>
                <select name="trang_thai_ho_so" class="form-select">
                    <option value="Chờ duyệt" selected>Chờ duyệt</option>
                    <option value="Đã duyệt">Đã duyệt</option>
                </select>
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Lưu sinh viên</button>
            <a href="{{ route('sinhvien.index') }}" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>
@endsection
