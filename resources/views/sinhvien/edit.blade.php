@extends('admin.layouts.admin')

@section('content')
    <div class="container mt-4">
        <h3>✏️ Chỉnh sửa thông tin sinh viên</h3>
        <hr>

        <form method="POST" action="{{ route('sinhvien.update', $sinhvien->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Mã sinh viên</label>
                    <input type="text" name="ma_sinh_vien" value="{{ $sinhvien->ma_sinh_vien }}" class="form-control"
                        required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Họ và tên</label>
                    <input type="text" name="ho_ten" value="{{ $sinhvien->ho_ten }}" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Ảnh sinh viên (tải ảnh mới để thay)</label>
                    <input type="file" name="anh_sinh_vien" class="form-control" accept="image/*">
                    @error('anh_sinh_vien')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror

                    @if (!empty($sinhvien->anh_sinh_vien))
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $sinhvien->anh_sinh_vien) }}" alt="Ảnh hiện tại"
                                style="width:84px;height:84px;object-fit:cover;border-radius:8px;">
                        </div>
                    @endif
                </div>

                <div class="col-md-6 mb-3">
                    <label>Ngày sinh</label>
                    <input type="date" name="ngay_sinh" value="{{ $sinhvien->ngay_sinh }}" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Giới tính</label>
                    <select name="gioi_tinh" class="form-select" required>
                        <option value="Nam" {{ $sinhvien->gioi_tinh == 'Nam' ? 'selected' : '' }}>Nam</option>
                        <option value="Nữ" {{ $sinhvien->gioi_tinh == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                        <option value="Khác" {{ $sinhvien->gioi_tinh == 'Khác' ? 'selected' : '' }}>Khác</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Quê quán</label>
                    <input type="text" name="que_quan" value="{{ $sinhvien->que_quan }}" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Họ và tên người thân</label>
                    <input type="text" name="guardian_name" value="{{ $sinhvien->guardian_name }}" class="form-control">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Số điện thoại người thân</label>
                    <input type="text" name="guardian_phone" value="{{ $sinhvien->guardian_phone }}"
                        class="form-control">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Quan hệ</label>
                    <input type="text" name="guardian_relationship" value="{{ $sinhvien->guardian_relationship }}"
                        class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Số CCCD</label>
                    <input type="text" name="citizen_id_number" value="{{ $sinhvien->citizen_id_number }}"
                        class="form-control">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Ngày cấp CCCD</label>
                    <input type="date" name="citizen_issue_date"
                        value="{{ $sinhvien->citizen_issue_date?->format('Y-m-d') }}" class="form-control">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Nơi cấp CCCD</label>
                    <input type="text" name="citizen_issue_place" value="{{ $sinhvien->citizen_issue_place }}"
                        class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Nơi ở hiện tại</label>
                    <input type="text" name="noi_o_hien_tai" value="{{ $sinhvien->noi_o_hien_tai }}"
                        class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Lớp</label>
                    <input type="text" name="lop" value="{{ $sinhvien->lop }}" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Ngành</label>
                    <input type="text" name="nganh" value="{{ $sinhvien->nganh }}" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Khóa học</label>
                    <input type="text" name="khoa_hoc" value="{{ $sinhvien->khoa_hoc }}" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Số điện thoại</label>
                    <input type="text" name="so_dien_thoai" value="{{ $sinhvien->so_dien_thoai }}" class="form-control"
                        required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ $sinhvien->email }}" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Phòng</label>
                    <select name="phong_id" class="form-select" required>
                        @foreach ($phongs as $phong)
                            <option value="{{ $phong->id }}"
                                {{ $sinhvien->phong_id == $phong->id ? 'selected' : '' }}>
                                {{ $phong->ten_phong ?? 'Phòng ' . $phong->id }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Trạng thái hồ sơ</label>
                    <select name="trang_thai_ho_so" class="form-select">
                        <option value="Chờ duyệt" {{ $sinhvien->trang_thai_ho_so == 'Chờ duyệt' ? 'selected' : '' }}>Chờ
                            duyệt</option>
                        <option value="Đã duyệt" {{ $sinhvien->trang_thai_ho_so == 'Đã duyệt' ? 'selected' : '' }}>Đã
                            duyệt
                        </option>
                    </select>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-success">Cập nhật</button>
                <a href="{{ route('sinhvien.index') }}" class="btn btn-secondary">Quay lại</a>
            </div>
        </form>
    </div>
@endsection
