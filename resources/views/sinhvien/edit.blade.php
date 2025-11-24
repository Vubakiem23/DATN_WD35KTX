@extends('admin.layouts.admin')

@section('content')
    <div class="container mt-4">
        <h3>✏️ Chỉnh sửa thông tin sinh viên</h3>
        <hr>

        {{-- css --}}
        <style>
            .section-title {
                font-weight: 600;
                margin-bottom: 12px;
                display: flex;
                align-items: center;
                gap: 8px
            }

            .section-title .dot {
                width: 8px;
                height: 8px;
                background: #0d6efd;
                border-radius: 50%;
                display: inline-block
            }

            .form-section {
                background: #fff;
                border: 1px solid #eef0f3;
                border-radius: 10px;
                box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
                margin-bottom: 16px
            }

            .form-section .card-body {
                padding: 16px
            }

            .preview-avatar {
                width: 100%;
                max-width: 140px;
                border-radius: 10px;
                border: 1px solid #e9ecef
            }
        </style>

        <form method="POST" action="{{ route('sinhvien.update', $sinhvien->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Thông tin cá nhân --}}
            <div class="form-section">
                <div class="card-body">
                    <div class="section-title"><span class="dot"></span>Thông tin cá nhân</div>
                    <div class="row g-3">
                        {{-- Mã SV --}}
                        <div class="col-md-3">
                            <label class="form-label">Mã sinh viên <span class="text-danger">*</span></label>
                            <input type="text" name="ma_sinh_vien" class="form-control" required
                                value="{{ old('ma_sinh_vien', $sinhvien->ma_sinh_vien) }}">
                            @error('ma_sinh_vien')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        {{-- Họ tên --}}
                        <div class="col-md-4">
                            <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="ho_ten" class="form-control" required
                                value="{{ old('ho_ten', $sinhvien->ho_ten) }}">
                            @error('ho_ten')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        {{-- Ngày sinh --}}
                        <div class="col-md-3">
                            <label class="form-label">Ngày sinh <span class="text-danger">*</span></label>
                            <input type="date" name="ngay_sinh" class="form-control" required
                                value="{{ old('ngay_sinh', \Illuminate\Support\Str::of($sinhvien->ngay_sinh)->substr(0, 10)) }}">
                            @error('ngay_sinh')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        {{-- Giới tính --}}
                        <div class="col-md-2 mb-3">
                            <label>Giới tính <span class="req">*</span></label>
                            <select name="gioi_tinh" class="form-control" required>
                                <option value="Nam" @selected(old('gioi_tinh', $sinhvien->gioi_tinh) === 'Nam')>Nam</option>
                                <option value="Nữ" @selected(old('gioi_tinh', $sinhvien->gioi_tinh) === 'Nữ')>Nữ</option>
                                <option value="Khác" @selected(old('gioi_tinh', $sinhvien->gioi_tinh) === 'Khác')>Khác</option>
                            </select>
                            @error('gioi_tinh')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        {{-- Ảnh --}}
                        <div class="col-md-3">
                            <label class="form-label">Hình ảnh</label>
                            <input type="file" name="anh_sinh_vien" class="form-control" accept="image/*">
                            @error('anh_sinh_vien')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror

                            @if (!empty($sinhvien->anh_sinh_vien))
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $sinhvien->anh_sinh_vien) }}" alt="Ảnh hiện tại"
                                        class="preview-avatar">
                                </div>
                            @endif
                        </div>
                        {{-- Quê quán --}}
                        <div class="col-md-12">
                            <label class="form-label">Quê quán <span class="text-danger">*</span></label>
                            <input type="text" name="que_quan" class="form-control" required
                                value="{{ old('que_quan', $sinhvien->que_quan) }}">
                            @error('que_quan')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- CMND/CCCD --}}
            <div class="form-section">
                <div class="card-body">
                    <div class="section-title"><span class="dot"></span>CMND/CCCD</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Số CCCD</label>
                            <input type="text" name="citizen_id_number" class="form-control"
                                value="{{ old('citizen_id_number', $sinhvien->citizen_id_number) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ngày cấp</label>
                            <input type="date" name="citizen_issue_date" class="form-control"
                                value="{{ old('citizen_issue_date', \Illuminate\Support\Str::of($sinhvien->citizen_issue_date)->substr(0, 10)) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nơi cấp</label>
                            <input type="text" name="citizen_issue_place" class="form-control"
                                value="{{ old('citizen_issue_place', $sinhvien->citizen_issue_place) }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Liên hệ & nơi ở --}}
            <div class="form-section">
                <div class="card-body">
                    <div class="section-title"><span class="dot"></span>Liên hệ & nơi ở</div>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="so_dien_thoai" class="form-control" required
                                value="{{ old('so_dien_thoai', $sinhvien->so_dien_thoai) }}">
                            @error('so_dien_thoai')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required
                                value="{{ old('email', $sinhvien->email) }}">
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Nơi ở hiện tại <span class="text-danger">*</span></label>
                            <input type="text" name="noi_o_hien_tai" class="form-control" required
                                value="{{ old('noi_o_hien_tai', $sinhvien->noi_o_hien_tai) }}">
                            @error('noi_o_hien_tai')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Học vụ --}}
            <div class="form-section">
                <div class="card-body">
                    <div class="section-title"><span class="dot"></span>Học vụ</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Lớp <span class="text-danger">*</span></label>
                            <input type="text" name="lop" class="form-control" required
                                value="{{ old('lop', $sinhvien->lop) }}">
                            @error('lop')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ngành <span class="text-danger">*</span></label>
                            <input type="text" name="nganh" class="form-control" required
                                value="{{ old('nganh', $sinhvien->nganh) }}">
                            @error('nganh')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Khóa học <span class="text-danger">*</span></label>
                            <input type="text" name="khoa_hoc" class="form-control" required
                                value="{{ old('khoa_hoc', $sinhvien->khoa_hoc) }}">
                            @error('khoa_hoc')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>



            {{-- Người thân liên hệ --}}
            <div class="form-section">
                <div class="card-body">
                    <div class="section-title"><span class="dot"></span>Người thân liên hệ</div>
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Họ và tên</label>
                            <input type="text" name="guardian_name" class="form-control"
                                value="{{ old('guardian_name', $sinhvien->guardian_name) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="guardian_phone" class="form-control"
                                value="{{ old('guardian_phone', $sinhvien->guardian_phone) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Quan hệ</label>
                            <input type="text" name="guardian_relationship" class="form-control"
                                value="{{ old('guardian_relationship', $sinhvien->guardian_relationship) }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Trạng thái hồ sơ --}}
            <div class="form-section">
                <div class="card-body">
                    <div class="section-title"><span class="dot"></span>Trạng thái hồ sơ</div>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Trạng thái</label>
                            <select name="trang_thai_ho_so" class="form-select">
                                <option value="{{ \App\Models\SinhVien::STATUS_PENDING_APPROVAL }}" @selected(old('trang_thai_ho_so', $sinhvien->trang_thai_ho_so) === \App\Models\SinhVien::STATUS_PENDING_APPROVAL)>Chờ duyệt</option>
                                <option value="{{ \App\Models\SinhVien::STATUS_PENDING_CONFIRMATION }}" @selected(old('trang_thai_ho_so', $sinhvien->trang_thai_ho_so) === \App\Models\SinhVien::STATUS_PENDING_CONFIRMATION)>Chờ xác nhận</option>
                                <option value="{{ \App\Models\SinhVien::STATUS_APPROVED }}" @selected(old('trang_thai_ho_so', $sinhvien->trang_thai_ho_so) === \App\Models\SinhVien::STATUS_APPROVED)>Đã duyệt</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-success"><i class="fa fa-save me-1"></i> Cập nhật</button>
                <a href="{{ route('sinhvien.index') }}" class="btn btn-secondary">Quay lại</a>
            </div>
        </form>
    </div>
@endsection
