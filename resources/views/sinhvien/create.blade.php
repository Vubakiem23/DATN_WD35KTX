@extends('admin.layouts.admin')

@section('title', 'Thêm sinh viên mới')

@section('content')
    <div class="container mt-4">

        @push('styles')
        <style>
            .page-title {
                font-size: 1.5rem;
                font-weight: 700;
                color: #1f2937;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .page-title i {
                color: #4e54c8;
            }

            .section-title {
                font-weight: 600;
                margin-bottom: 16px;
                display: flex;
                align-items: center;
                gap: 8px;
                color: #1f2937;
                font-size: 1rem;
            }

            .section-title .dot {
                width: 8px;
                height: 8px;
                background: #4e54c8;
                border-radius: 50%;
                display: inline-block;
            }

            .form-section {
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 14px;
                box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
                margin-bottom: 20px;
                transition: box-shadow 0.2s ease;
            }

            .form-section:hover {
                box-shadow: 0 12px 35px rgba(15, 23, 42, 0.08);
            }

            .form-section .card-body {
                padding: 24px;
            }

            .form-label {
                font-weight: 600;
                color: #495057;
                margin-bottom: 0.5rem;
                font-size: 0.9rem;
            }

            .form-control,
            .form-select {
                border-radius: 10px;
                border: 1px solid #e5e7eb;
                padding: 0.65rem 1rem;
                transition: all 0.2s ease;
                font-size: 0.9rem;
            }

            .form-control:focus,
            .form-select:focus {
                border-color: #4e54c8;
                box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.1);
                outline: none;
            }

            .form-control.is-invalid,
            .form-select.is-invalid {
                border-color: #ef4444;
            }

            .form-control.is-invalid:focus,
            .form-select.is-invalid:focus {
                border-color: #ef4444;
                box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
            }

            .btn-dergin {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: .35rem;
                padding: .5rem 1.2rem;
                border-radius: 999px;
                font-weight: 600;
                font-size: .85rem;
                border: none;
                color: #fff;
                background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
                box-shadow: 0 6px 16px rgba(78, 84, 200, .22);
                transition: transform .2s ease, box-shadow .2s ease;
                text-decoration: none;
            }

            .btn-dergin:hover {
                transform: translateY(-1px);
                box-shadow: 0 10px 22px rgba(78, 84, 200, .32);
                color: #fff;
            }

            .btn-dergin i {
                font-size: .8rem;
            }

            .btn-dergin--success {
                background: linear-gradient(135deg, #10b981 0%, #22c55e 100%);
                box-shadow: 0 6px 16px rgba(16, 185, 129, .22);
            }

            .btn-dergin--success:hover {
                box-shadow: 0 10px 22px rgba(16, 185, 129, .32);
            }

            .btn-dergin--muted {
                background: linear-gradient(135deg, #6b7280 0%, #9ca3af 100%);
                box-shadow: 0 6px 16px rgba(107, 114, 128, .22);
            }

            .btn-dergin--muted:hover {
                box-shadow: 0 10px 22px rgba(107, 114, 128, .32);
            }

            .invalid-feedback {
                display: block;
                width: 100%;
                margin-top: 0.25rem;
                font-size: 0.875rem;
                color: #ef4444;
            }

            .text-danger {
                color: #ef4444 !important;
            }
        </style>
        @endpush

        <h4 class="page-title mb-0">
            <i class="fa fa-plus-circle"></i>
            Thêm sinh viên mới
        </h4>
        <p class="text-muted mb-4">Tạo hồ sơ sinh viên mới cho hệ thống ký túc xá.</p>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <form method="POST" action="{{ route('sinhvien.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Thông tin cá nhân --}}
            <div class="form-section">
                <div class="card-body">
                    <div class="section-title"><span class="dot"></span>Thông tin cá nhân</div>
                    <div class="row g-3">
                        {{-- Mã SV --}}
                        <div class="col-md-3">
                            <label for="ma_sinh_vien" class="form-label">Mã sinh viên <span class="text-danger">*</span></label>
                            <input type="text" name="ma_sinh_vien" id="ma_sinh_vien" 
                                class="form-control @error('ma_sinh_vien') is-invalid @enderror" 
                                placeholder="Nhập mã sinh viên" required
                                value="{{ old('ma_sinh_vien') }}">
                            @error('ma_sinh_vien')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- Họ tên --}}
                        <div class="col-md-4">
                            <label for="ho_ten" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="ho_ten" id="ho_ten" 
                                class="form-control @error('ho_ten') is-invalid @enderror" 
                                placeholder="Nhập họ và tên" required 
                                value="{{ old('ho_ten') }}">
                            @error('ho_ten')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- Ngày sinh --}}
                        <div class="col-md-3">
                            <label for="ngay_sinh" class="form-label">Ngày sinh <span class="text-danger">*</span></label>
                            <input type="date" name="ngay_sinh" id="ngay_sinh" 
                                class="form-control @error('ngay_sinh') is-invalid @enderror" required
                                value="{{ old('ngay_sinh') }}">
                            @error('ngay_sinh')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- Giới tính --}}
                        <div class="col-md-2">
                            <label for="gioi_tinh" class="form-label">Giới tính <span class="text-danger">*</span></label>
                            <select name="gioi_tinh" id="gioi_tinh" 
                                class="form-select @error('gioi_tinh') is-invalid @enderror" required>
                                <option value="">-- Chọn giới tính --</option>
                                <option value="Nam" @selected(old('gioi_tinh') === 'Nam')>Nam</option>
                                <option value="Nữ" @selected(old('gioi_tinh') === 'Nữ')>Nữ</option>
                                <option value="Khác" @selected(old('gioi_tinh') === 'Khác')>Khác</option>
                            </select>
                            @error('gioi_tinh')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Ảnh sinh viên --}}
                        <div class="col-md-3">
                            <label for="anh_sinh_vien" class="form-label">Ảnh sinh viên (tuỳ chọn)</label>
                            <input type="file" name="anh_sinh_vien" id="anh_sinh_vien" 
                                class="form-control @error('anh_sinh_vien') is-invalid @enderror" 
                                accept="image/*">
                            @error('anh_sinh_vien')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- Ảnh giấy xác nhận --}}
                        <div class="col-md-3">
                            <label for="anh_giay_xac_nhan" class="form-label">Ảnh giấy xác nhận (tuỳ chọn)</label>
                            <input type="file" name="anh_giay_xac_nhan" id="anh_giay_xac_nhan" 
                                class="form-control @error('anh_giay_xac_nhan') is-invalid @enderror" 
                                accept="image/*">
                            @error('anh_giay_xac_nhan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- Quê quán (full hàng) --}}
                        <div class="col-md-12">
                            <label for="que_quan" class="form-label">Quê quán <span class="text-danger">*</span></label>
                            <input type="text" name="que_quan" id="que_quan" 
                                class="form-control @error('que_quan') is-invalid @enderror" 
                                placeholder="Nhập quê quán" required
                                value="{{ old('que_quan') }}">
                            @error('que_quan')
                                <div class="invalid-feedback">{{ $message }}</div>
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
                            <label for="citizen_id_number" class="form-label">Số CCCD</label>
                            <input type="text" name="citizen_id_number" id="citizen_id_number" 
                                class="form-control @error('citizen_id_number') is-invalid @enderror"
                                placeholder="Nhập số CCCD"
                                value="{{ old('citizen_id_number') }}">
                            @error('citizen_id_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="citizen_issue_date" class="form-label">Ngày cấp</label>
                            <input type="date" name="citizen_issue_date" id="citizen_issue_date" 
                                class="form-control @error('citizen_issue_date') is-invalid @enderror"
                                value="{{ old('citizen_issue_date') }}">
                            @error('citizen_issue_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="citizen_issue_place" class="form-label">Nơi cấp</label>
                            <input type="text" name="citizen_issue_place" id="citizen_issue_place" 
                                class="form-control @error('citizen_issue_place') is-invalid @enderror"
                                placeholder="Nhập nơi cấp"
                                value="{{ old('citizen_issue_place') }}">
                            @error('citizen_issue_place')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                            <label for="so_dien_thoai" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="so_dien_thoai" id="so_dien_thoai" 
                                class="form-control @error('so_dien_thoai') is-invalid @enderror" 
                                placeholder="Nhập số điện thoại" required
                                value="{{ old('so_dien_thoai') }}">
                            @error('so_dien_thoai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                placeholder="Nhập email" required
                                value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-5">
                            <label for="noi_o_hien_tai" class="form-label">Nơi ở hiện tại <span class="text-danger">*</span></label>
                            <input type="text" name="noi_o_hien_tai" id="noi_o_hien_tai" 
                                class="form-control @error('noi_o_hien_tai') is-invalid @enderror" 
                                placeholder="Nhập nơi ở hiện tại" required
                                value="{{ old('noi_o_hien_tai') }}">
                            @error('noi_o_hien_tai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Học vụ & phòng --}}
            <div class="form-section">
                <div class="card-body">
                    <div class="section-title"><span class="dot"></span>Học vụ & phòng</div>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="lop" class="form-label">Lớp <span class="text-danger">*</span></label>
                            <input type="text" name="lop" id="lop" 
                                class="form-control @error('lop') is-invalid @enderror" 
                                placeholder="Nhập lớp" required
                                value="{{ old('lop') }}">
                            @error('lop')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="nganh" class="form-label">Ngành <span class="text-danger">*</span></label>
                            <input type="text" name="nganh" id="nganh" 
                                class="form-control @error('nganh') is-invalid @enderror" 
                                placeholder="Nhập ngành" required
                                value="{{ old('nganh') }}">
                            @error('nganh')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="khoa_hoc" class="form-label">Khóa học <span class="text-danger">*</span></label>
                            <input type="text" name="khoa_hoc" id="khoa_hoc" 
                                class="form-control @error('khoa_hoc') is-invalid @enderror" 
                                placeholder="Nhập khóa học" required
                                value="{{ old('khoa_hoc') }}">
                            @error('khoa_hoc')
                                <div class="invalid-feedback">{{ $message }}</div>
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
                            <label for="guardian_name" class="form-label">Họ và tên</label>
                            <input type="text" name="guardian_name" id="guardian_name" 
                                class="form-control @error('guardian_name') is-invalid @enderror"
                                placeholder="Nhập họ và tên người thân"
                                value="{{ old('guardian_name') }}">
                            @error('guardian_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="guardian_phone" class="form-label">Số điện thoại</label>
                            <input type="text" name="guardian_phone" id="guardian_phone" 
                                class="form-control @error('guardian_phone') is-invalid @enderror"
                                placeholder="Nhập số điện thoại"
                                value="{{ old('guardian_phone') }}">
                            @error('guardian_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="guardian_relationship" class="form-label">Quan hệ</label>
                            <input type="text" name="guardian_relationship" id="guardian_relationship" 
                                class="form-control @error('guardian_relationship') is-invalid @enderror"
                                placeholder="Ví dụ: Cha, Mẹ, Anh, Chị..."
                                value="{{ old('guardian_relationship') }}">
                            @error('guardian_relationship')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                            <label for="trang_thai_ho_so" class="form-label">Trạng thái</label>
                            <select name="trang_thai_ho_so" id="trang_thai_ho_so" 
                                class="form-select @error('trang_thai_ho_so') is-invalid @enderror">
                                <option value="{{ \App\Models\SinhVien::STATUS_PENDING_APPROVAL }}" @selected(old('trang_thai_ho_so', \App\Models\SinhVien::STATUS_PENDING_APPROVAL) === \App\Models\SinhVien::STATUS_PENDING_APPROVAL)>Chờ duyệt</option>
                                <option value="{{ \App\Models\SinhVien::STATUS_PENDING_CONFIRMATION }}" @selected(old('trang_thai_ho_so') === \App\Models\SinhVien::STATUS_PENDING_CONFIRMATION)>Chờ xác nhận</option>
                                <option value="{{ \App\Models\SinhVien::STATUS_APPROVED }}" @selected(old('trang_thai_ho_so') === \App\Models\SinhVien::STATUS_APPROVED)>Đã duyệt</option>
                            </select>
                            @error('trang_thai_ho_so')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn-dergin btn-dergin--success">
                    <i class="fa fa-save"></i> Lưu sinh viên
                </button>
                <a href="{{ route('sinhvien.index') }}" class="btn-dergin btn-dergin--muted">
                    <i class="fa fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </form>
    </div>
@endsection
