@extends('public.layouts.app')

@section('title', 'Đăng ký ký túc xá')

@section('content')
<div class="content-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="mb-1">Đăng ký ký túc xá</h3>
                        <p class="text-muted mb-4">Điền đầy đủ thông tin bên dưới. Hồ sơ sẽ ở trạng thái <strong>Chờ duyệt</strong>.</p>

                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('public.apply.store') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Mã sinh viên <span class="text-danger">*</span></label>
                                    <input type="text" name="ma_sinh_vien" class="form-control" value="{{ old('ma_sinh_vien') }}" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" name="ho_ten" class="form-control" value="{{ old('ho_ten') }}" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Ngày sinh <span class="text-danger">*</span></label>
                                    <input type="date" name="ngay_sinh" class="form-control" value="{{ old('ngay_sinh') }}" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Giới tính <span class="text-danger">*</span></label>
                                    <select name="gioi_tinh" class="form-control" required>
                                        <option value="">-- Chọn --</option>
                                        <option value="Nam" @selected(old('gioi_tinh')==='Nam')>Nam</option>
                                        <option value="Nữ" @selected(old('gioi_tinh')==='Nữ')>Nữ</option>
                                        <option value="Khác" @selected(old('gioi_tinh')==='Khác')>Khác</option>
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Quê quán <span class="text-danger">*</span></label>
                                    <input type="text" name="que_quan" class="form-control" value="{{ old('que_quan') }}" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Nơi ở hiện tại <span class="text-danger">*</span></label>
                                    <input type="text" name="noi_o_hien_tai" class="form-control" value="{{ old('noi_o_hien_tai') }}" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Lớp <span class="text-danger">*</span></label>
                                    <input type="text" name="lop" class="form-control" value="{{ old('lop') }}" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Ngành <span class="text-danger">*</span></label>
                                    <input type="text" name="nganh" class="form-control" value="{{ old('nganh') }}" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Khóa học <span class="text-danger">*</span></label>
                                    <input type="text" name="khoa_hoc" class="form-control" value="{{ old('khoa_hoc') }}" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Ảnh sinh viên</label>
                                    <input type="file" name="anh_sinh_vien" class="form-control" accept="image/*">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Ảnh giấy xác nhận</label>
                                    <input type="file" name="anh_giay_xac_nhan" class="form-control" accept="image/*">
                                </div>
                            </div>

                            <hr class="my-4">
                            <h5 class="mb-3">Liên hệ</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="text" name="so_dien_thoai" class="form-control" value="{{ old('so_dien_thoai') }}" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input
                                        type="email"
                                        name="email"
                                        class="form-control"
                                        value="{{ auth()->check() ? auth()->user()->email : old('email') }}"
                                        @if(auth()->check()) readonly @endif
                                        required
                                    >
                                </div>
                            </div>

                            <hr class="my-4">
                            <h5 class="mb-3">Thông tin CCCD & Người giám hộ (không bắt buộc)</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Số CCCD</label>
                                    <input type="text" name="citizen_id_number" class="form-control" value="{{ old('citizen_id_number') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Ngày cấp</label>
                                    <input type="date" name="citizen_issue_date" class="form-control" value="{{ old('citizen_issue_date') }}">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Nơi cấp</label>
                                    <input type="text" name="citizen_issue_place" class="form-control" value="{{ old('citizen_issue_place') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Họ tên người giám hộ</label>
                                    <input type="text" name="guardian_name" class="form-control" value="{{ old('guardian_name') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Số điện thoại giám hộ</label>
                                    <input type="text" name="guardian_phone" class="form-control" value="{{ old('guardian_phone') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Quan hệ</label>
                                    <input type="text" name="guardian_relationship" class="form-control" value="{{ old('guardian_relationship') }}">
                                </div>
                            </div>

                            <div class="mt-4 d-flex gap-2">
                                <button type="submit" class="btn btn-primary px-4">
                                    Gửi hồ sơ
                                </button>
                                <a href="{{ route('public.home') }}" class="btn btn-outline-secondary">Hủy</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


