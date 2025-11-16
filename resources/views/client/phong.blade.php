@extends('client.layouts.app')

@section('title', 'Thông tin phòng - Sinh viên')

@section('content')
@if(!$sinhVien)
<!-- Thông báo chưa nộp hồ sơ -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-file-alt fa-4x text-info mb-3"></i>
                <h4 class="text-info">Bạn chưa nộp hồ sơ đăng ký ký túc xá</h4>
                <p class="text-muted">Vui lòng nộp hồ sơ để xem thông tin phòng của bạn.</p>
            </div>
        </div>
    </div>
</div>
@elseif($phong)
<!-- Header màu xanh đậm -->
<div class="page-header-dark mb-4">
    <div class="d-flex justify-content-between align-items-center py-4 px-4">
        <h4 class="mb-0 text-white fw-bold">
            <i class="fas fa-door-open me-2"></i>
            Thông tin phòng của tôi
        </h4>
        <h4 class="mb-0 text-white fw-bold">
            <i class="fas fa-user me-2"></i>
            Thông tin của bạn
        </h4>
    </div>
</div>

<!-- Row 1: Chi tiết phòng và Thông tin cá nhân -->
<div class="row g-4 mb-4">
    <!-- Bên trái: Chi tiết phòng -->
    <div class="col-lg-8 col-md-12 order-lg-1">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-info-circle me-2"></i>
                    Chi tiết phòng: {{ $phong->ten_phong }}
                </h5>
            </div>
            <div class="card-body">
                @php
                $gioiTinh = strtolower($phong->gioi_tinh ?? '');
                $gioiTinhClass = match($gioiTinh) {
                'nam' => 'nam',
                'nữ' => 'nu',
                'cả hai' => 'cả-hai',
                default => ''
                };
                // Tính giá tiền chia slots
                $totalSlots = $phong->totalSlots() ?? 0;
                $giaTienTong = $phong->gia_phong ?? null;
                $giaTienSlot = null;
                if ($giaTienTong && $totalSlots > 0) {
                $giaTienSlot = (int) round($giaTienTong / $totalSlots);
                }
                // Ảnh phòng
                $anhPhong = $phong->hinh_anh ?? null;
                $imageUrl = $anhPhong ? asset('storage/' . $anhPhong) : asset('uploads/default.png');
                @endphp

                <!-- Ảnh phòng -->
                @if($anhPhong)
                <div class="text-center mb-4 room-image-wrapper">
                    <img src="{{ $imageUrl }}"
                        alt="{{ $phong->ten_phong }}"
                        class="img-fluid room-image"
                        style="max-height: 350px; width: auto; object-fit: cover;">
                </div>
                <hr class="my-4 room-divider">
                @endif

                <div class="info-item mb-3">
                    <p class="mb-1 info-label"><i class="fas fa-tag me-2"></i>Loại phòng:</p>
                    <p class="ms-4 mb-0">
                        <span class="badge badge-room-type">
                            {{ $phong->loai_phong ?? 'N/A' }}
                        </span>
                    </p>
                </div>
                <div class="info-item mb-3">
                    <p class="mb-1 info-label"><i class="fas fa-building me-2"></i>Khu:</p>
                    <p class="ms-4 mb-0 info-value">{{ $phong->khu->ten_khu ?? 'N/A' }}</p>
                </div>
                <div class="info-item mb-3">
                    <p class="mb-1 info-label"><i class="fas fa-venus-mars me-2"></i>Giới tính:</p>
                    <p class="ms-4 mb-0">
                        <span class="gender-badge {{ $gioiTinhClass }}">
                            {{ $phong->gioi_tinh ?? 'N/A' }}
                        </span>
                    </p>
                </div>
                <div class="info-item mb-3">
                    <p class="mb-1 info-label"><i class="fas fa-users me-2"></i>Số người đang ở:</p>
                    <p class="ms-4 mb-0 info-value">
                        {{ isset($soNguoiTrongPhong) ? $soNguoiTrongPhong : 'N/A' }} người
                        @if(isset($danhSachSinhVien) && $danhSachSinhVien->count() > 0)
                        <button class="btn btn-sm btn-link p-0 ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#danhSachSinhVienCollapse" aria-expanded="false" aria-controls="danhSachSinhVienCollapse">
                            <i class="fas fa-chevron-down"></i> Xem danh sách
                        </button>
                        @endif
                    </p>
                </div>
                @if(isset($danhSachSinhVien) && $danhSachSinhVien->count() > 0)
                <div class="collapse mt-3" id="danhSachSinhVienCollapse">
                    <div class="student-list-card">
                        <ul class="student-list mb-0">
                            @foreach($danhSachSinhVien as $sv)
                            <li class="student-item">
                                <div class="student-avatar">
                                    @php
                                    $anhSV = $sv->anh_sinh_vien ?? null;
                                    $avatarUrl = $anhSV ? asset('storage/' . $anhSV) : asset('uploads/default.png');
                                    @endphp
                                    <img src="{{ $avatarUrl }}" alt="{{ $sv->ho_ten ?? 'Sinh viên' }}" class="student-avatar-img">
                                </div>
                                <div class="student-info">
                                    <div class="student-name">{{ $sv->ho_ten ?? 'N/A' }}</div>
                                    <div class="student-id">{{ $sv->ma_sinh_vien ?? 'N/A' }}</div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
                <div class="info-item mb-3">
                    <p class="mb-1 info-label"><i class="fas fa-check-circle me-2"></i>Trạng thái:</p>
                    <p class="ms-4 mb-0">
                        <span class="badge badge-status bg-{{ $phong->trang_thai == 'Đang sử dụng' ? 'success' : 'secondary' }}">
                            {{ $phong->trang_thai ?? 'N/A' }}
                        </span>
                    </p>
                </div>

                <!-- Giá tiền - Highlight section -->
                @if($giaTienTong || $giaTienSlot)
                <div class="price-section mb-4">
                    <div class="row g-3">
                        @if($giaTienTong)
                        <div class="col-md-6">
                            <div class="price-card price-card-total">
                                <div class="price-icon">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <div class="price-content">
                                    <p class="price-label">Giá tiền tổng</p>
                                    <p class="price-value">
                                        {{ number_format($giaTienTong, 0, ',', '.') }} <span class="price-unit">VND/tháng</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($giaTienSlot)
                        <div class="col-md-6">
                            <div class="price-card price-card-slot">
                                <div class="price-icon">
                                    <i class="fas fa-coins"></i>
                                </div>
                                <div class="price-content">
                                    <p class="price-label">Giá tiền chia slots</p>
                                    <p class="price-value">
                                        {{ number_format($giaTienSlot, 0, ',', '.') }} <span class="price-unit">VND/tháng/slot</span>
                                    </p>
                                    <p class="price-note"><small>({{ $totalSlots }} slot)</small></p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <hr class="my-4 room-divider">
                <div class="info-item">
                    <p class="mb-2 info-label"><i class="fas fa-file-alt me-2"></i>Mô tả phòng:</p>
                    <div class="ms-4 p-3 description-box">
                        <p class="mb-0">{{ $phong->ghi_chu ?? 'Không có mô tả' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tài sản (chia 2 cột: Tài sản phòng bên trái, Tài sản riêng bên phải) -->
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-boxes me-2"></i>
                    Tài sản
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <!-- Cột trái: Tài sản của phòng -->
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                            <h6 class="mb-0 fw-bold text-secondary">
                                <i class="fas fa-building me-2"></i>
                                Tài sản của phòng
                            </h6>
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#taiSanPhongCollapse" aria-expanded="true" aria-controls="taiSanPhongCollapse">
                                <i class="fas fa-chevron-up" id="taiSanPhongChevron"></i>
                            </button>
                        </div>
                        <div class="collapse show" id="taiSanPhongCollapse">
                            @if(isset($taiSanPhong) && $taiSanPhong->count())
                            <div class="row g-3">
                                @foreach($taiSanPhong as $ts)
                                <div class="col-12">
                                    <div class="card border tai-san-card">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-start">
                                                @php
                                                $hinhAnh = $ts->hinh_anh ?? ($ts->khoTaiSan->hinh_anh ?? null);
                                                $imageUrl = $hinhAnh ? asset('storage/' . $hinhAnh) : asset('uploads/default.png');
                                                $maTaiSan = $ts->khoTaiSan->ma_tai_san ?? 'N/A';
                                                $trangThai =
                                                $ts->trang_thai_bao_tri
                                                ? $ts->trang_thai_bao_tri
                                                : ($ts->tinh_trang_hien_tai ?? $ts->tinh_trang ?? 'N/A');
                                                @endphp
                                                <img src="{{ $imageUrl }}" alt="{{ $ts->khoTaiSan->ten_tai_san ?? $ts->ten_tai_san ?? 'Không rõ' }}"
                                                    class="me-3" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ $ts->khoTaiSan->ten_tai_san ?? $ts->ten_tai_san ?? 'Không rõ' }}</h6>
                                                    <p class="mb-1 small text-muted">
                                                        <strong>Mã tài sản:</strong> {{ $maTaiSan }}
                                                    </p>
                                                    <p class="mb-0 small">
                                                        <strong>Trạng thái:</strong>
                                                        <span class="badge bg-{{ $trangThai == 'Bình thường' ? 'success' : ($trangThai == 'Chờ bảo trì' ? 'info' : ($trangThai == 'Đang bảo trì' ? 'warning' : 'warning')) }}">
                                                            {{ $trangThai }}
                                                        </span>
                                                    </p>
                                                    @php
                                                    $isBaoTri = in_array($trangThai, ['Đang bảo trì', 'Chờ bảo trì']);
                                                    @endphp

                                                    @if(!$isBaoTri)
                                                    <button class="btn btn-sm btn-danger mt-2 btn-bao-hong"
                                                        data-id="{{ $ts->id }}"
                                                        data-ten="{{ $ts->khoTaiSan->ten_tai_san ?? $ts->ten_tai_san }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalBaoHong">
                                                        <i class="fas fa-exclamation-triangle"></i> Báo hỏng
                                                    </button>
                                                    @endif

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <p class="text-muted fst-italic">Chưa có tài sản phòng.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Cột phải: Tài sản riêng của bạn -->
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                            <h6 class="mb-0 fw-bold text-secondary">
                                <i class="fas fa-hand-holding me-2"></i>
                                Tài sản riêng của bạn
                            </h6>
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#taiSanCaNhanCollapse" aria-expanded="true" aria-controls="taiSanCaNhanCollapse">
                                <i class="fas fa-chevron-up" id="taiSanCaNhanChevron"></i>
                            </button>
                        </div>
                        <div class="collapse show" id="taiSanCaNhanCollapse">
                            @if(isset($taiSanCaNhan) && $taiSanCaNhan->count())
                            <div class="row g-3">
                                @foreach($taiSanCaNhan as $ts)
                                <div class="col-12">
                                    <div class="card border tai-san-card">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-start">
                                                @php
                                                $hinhAnh = $ts->hinh_anh ?? ($ts->khoTaiSan->hinh_anh ?? null);
                                                $imageUrl = $hinhAnh ? asset('storage/' . $hinhAnh) : asset('uploads/default.png');
                                                $maTaiSan = $ts->khoTaiSan->ma_tai_san ?? 'N/A';
                                                $trangThai =
                                                $ts->trang_thai_bao_tri
                                                ? $ts->trang_thai_bao_tri
                                                : ($ts->tinh_trang_hien_tai ?? $ts->tinh_trang ?? 'N/A');
                                                @endphp
                                                <img src="{{ $imageUrl }}" alt="{{ $ts->khoTaiSan->ten_tai_san ?? $ts->ten_tai_san ?? 'Không rõ' }}"
                                                    class="me-3" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ $ts->khoTaiSan->ten_tai_san ?? $ts->ten_tai_san ?? 'Không rõ' }}</h6>
                                                    <p class="mb-1 small text-muted">
                                                        <strong>Mã tài sản:</strong> {{ $maTaiSan }}
                                                    </p>

                                                    <p class="mb-0 small">
                                                        <strong>Trạng thái:</strong>
                                                        <span class="badge bg-{{ $trangThai == 'Bình thường' ? 'success' :
                                                            ($trangThai == 'Chờ bảo trì' ? 'info' :
                                                            ($trangThai == 'Đang bảo trì' ? 'warning' :
                                                            'secondary')) }}">
                                                            {{ $trangThai }}
                                                        </span>
                                                    </p>
                                                    @php
                                                    $isBaoTri = in_array($trangThai, ['Đang bảo trì', 'Chờ bảo trì']);
                                                    @endphp

                                                    @if(!$isBaoTri)
                                                    <button class="btn btn-sm btn-danger mt-2 btn-bao-hong"
                                                        data-id="{{ $ts->id }}"
                                                        data-ten="{{ $ts->khoTaiSan->ten_tai_san ?? $ts->ten_tai_san }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalBaoHong">
                                                        <i class="fas fa-exclamation-triangle"></i> Báo hỏng
                                                    </button>
                                                    @endif

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <p class="text-muted fst-italic">Chưa có tài sản được bàn giao.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bên phải: Thông tin sinh viên -->
    <div class="col-lg-4 col-md-12 order-lg-2">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-user me-2"></i>
                    Thông tin cá nhân của bạn
                </h5>
            </div>
            <div class="card-body">
                <!-- Ảnh sinh viên -->
                <div class="text-center mb-4 student-profile-header">
                    <div class="student-avatar-wrapper">
                        @php
                        $anhSinhVien = $sinhVien->anh_sinh_vien ?? null;
                        $imageUrl = $anhSinhVien ? asset('storage/' . $anhSinhVien) : asset('uploads/default.png');
                        @endphp
                        <div class="avatar-ring">
                            <img src="{{ $imageUrl }}"
                                alt="{{ $sinhVien->ho_ten ?? 'Sinh viên' }}"
                                class="student-main-avatar">
                        </div>
                        <div class="avatar-badge">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <h5 class="mt-3 mb-1 student-name-title">{{ $sinhVien->ho_ten ?? 'N/A' }}</h5>
                    <p class="student-id-title">{{ $sinhVien->ma_sinh_vien ?? 'N/A' }}</p>
                </div>
                <hr class="my-4">

                <div class="row g-4">
                    <!-- Cột 1: Thông tin cơ bản -->
                    <div class="col-12">
                        <div class="info-section-header" data-bs-toggle="collapse" data-bs-target="#thongTinCoBan" aria-expanded="true">
                            <h6 class="mb-0 fw-bold text-secondary">
                                <i class="fas fa-user-circle me-2"></i>
                                Thông tin cơ bản
                            </h6>
                            <i class="fas fa-chevron-down toggle-icon"></i>
                        </div>
                        <div class="collapse show" id="thongTinCoBan">
                            <div class="info-section-content">
                                <div class="info-item mb-3">
                                    <p class="mb-1"><strong><i class="fas fa-user me-2 text-primary"></i>Họ tên:</strong></p>
                                    <p class="ms-4 mb-0">{{ $sinhVien->ho_ten ?? 'N/A' }}</p>
                                </div>
                                <div class="info-item mb-3">
                                    <p class="mb-1"><strong><i class="fas fa-id-card me-2 text-primary"></i>Mã SV:</strong></p>
                                    <p class="ms-4 mb-0">{{ $sinhVien->ma_sinh_vien ?? 'N/A' }}</p>
                                </div>
                                <div class="info-item mb-3">
                                    <p class="mb-1"><strong><i class="fas fa-birthday-cake me-2 text-primary"></i>Ngày sinh:</strong></p>
                                    <p class="ms-4 mb-0">{{ optional($sinhVien->ngay_sinh)->format('d/m/Y') ?? 'N/A' }}</p>
                                </div>
                                <div class="info-item mb-3">
                                    <p class="mb-1"><strong><i class="fas fa-venus-mars me-2 text-primary"></i>Giới tính:</strong></p>
                                    <p class="ms-4 mb-0">{{ $sinhVien->gioi_tinh ?? 'N/A' }}</p>
                                </div>
                                <div class="info-item mb-3">
                                    <p class="mb-1"><strong><i class="fas fa-map-marker-alt me-2 text-primary"></i>Quê quán:</strong></p>
                                    <p class="ms-4 mb-0">{{ $sinhVien->que_quan ?? 'N/A' }}</p>
                                </div>
                                <div class="info-item mb-3">
                                    <p class="mb-1"><strong><i class="fas fa-home me-2 text-primary"></i>Nơi ở hiện tại:</strong></p>
                                    <p class="ms-4 mb-0">{{ $sinhVien->noi_o_hien_tai ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cột 2: Thông tin học tập & Liên hệ -->
                    <div class="col-12">
                        <div class="info-section-header" data-bs-toggle="collapse" data-bs-target="#thongTinHocTap" aria-expanded="true">
                            <h6 class="mb-0 fw-bold text-secondary">
                                <i class="fas fa-graduation-cap me-2"></i>
                                Thông tin học tập
                            </h6>
                            <i class="fas fa-chevron-down toggle-icon"></i>
                        </div>
                        <div class="collapse show" id="thongTinHocTap">
                            <div class="info-section-content">
                                <div class="info-item mb-3">
                                    <p class="mb-1"><strong><i class="fas fa-graduation-cap me-2 text-primary"></i>Lớp:</strong></p>
                                    <p class="ms-4 mb-0">{{ $sinhVien->lop ?? 'N/A' }}</p>
                                </div>
                                <div class="info-item mb-3">
                                    <p class="mb-1"><strong><i class="fas fa-book me-2 text-primary"></i>Ngành:</strong></p>
                                    <p class="ms-4 mb-0">{{ $sinhVien->nganh ?? 'N/A' }}</p>
                                </div>
                                <div class="info-item mb-3">
                                    <p class="mb-1"><strong><i class="fas fa-calendar me-2 text-primary"></i>Khóa học:</strong></p>
                                    <p class="ms-4 mb-0">{{ $sinhVien->khoa_hoc ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        <hr class="my-3">
                        <div class="info-section-header" data-bs-toggle="collapse" data-bs-target="#lienHe" aria-expanded="true">
                            <h6 class="mb-0 fw-bold text-secondary">
                                <i class="fas fa-address-book me-2"></i>
                                Liên hệ
                            </h6>
                            <i class="fas fa-chevron-down toggle-icon"></i>
                        </div>
                        <div class="collapse show" id="lienHe">
                            <div class="info-section-content">
                                <div class="info-item mb-3">
                                    <p class="mb-1"><strong><i class="fas fa-envelope me-2 text-primary"></i>Email:</strong></p>
                                    <p class="ms-4 mb-0">{{ $sinhVien->email ?? 'N/A' }}</p>
                                </div>
                                <div class="info-item mb-3">
                                    <p class="mb-1"><strong><i class="fas fa-phone me-2 text-primary"></i>SĐT:</strong></p>
                                    <p class="ms-4 mb-0">{{ $sinhVien->so_dien_thoai ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cột 3: CCCD & Người liên hệ -->
                    <div class="col-12">
                        <div class="info-section-header" data-bs-toggle="collapse" data-bs-target="#canCuocCongDan" aria-expanded="true">
                            <h6 class="mb-0 fw-bold text-secondary">
                                <i class="fas fa-id-badge me-2"></i>
                                Căn cước công dân
                            </h6>
                            <i class="fas fa-chevron-down toggle-icon"></i>
                        </div>
                        <div class="collapse show" id="canCuocCongDan">
                            <div class="info-section-content">
                                <div class="info-item mb-3">
                                    <p class="mb-1"><strong>Số CCCD:</strong></p>
                                    <p class="ms-4 mb-0">{{ $sinhVien->citizen_id_number ?? 'N/A' }}</p>
                                </div>
                                <div class="info-item mb-3">
                                    <p class="mb-1"><strong>Ngày cấp:</strong></p>
                                    <p class="ms-4 mb-0">{{ optional($sinhVien->citizen_issue_date)->format('d/m/Y') ?? 'N/A' }}</p>
                                </div>
                                <div class="info-item mb-3">
                                    <p class="mb-1"><strong>Nơi cấp:</strong></p>
                                    <p class="ms-4 mb-0">{{ $sinhVien->citizen_issue_place ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        <hr class="my-3">
                        <div class="info-section-header" data-bs-toggle="collapse" data-bs-target="#nguoiLienHe" aria-expanded="true">
                            <h6 class="mb-0 fw-bold text-secondary">
                                <i class="fas fa-users me-2"></i>
                                Người liên hệ
                            </h6>
                            <i class="fas fa-chevron-down toggle-icon"></i>
                        </div>
                        <div class="collapse show" id="nguoiLienHe">
                            <div class="info-section-content">
                                <div class="info-item mb-3">
                                    <p class="mb-1"><strong>Họ tên:</strong></p>
                                    <p class="ms-4 mb-0">{{ $sinhVien->guardian_name ?? 'N/A' }}</p>
                                </div>
                                <div class="info-item mb-3">
                                    <p class="mb-1"><strong>Quan hệ:</strong></p>
                                    <p class="ms-4 mb-0">{{ $sinhVien->guardian_relationship ?? 'N/A' }}</p>
                                </div>
                                <div class="info-item mb-3">
                                    <p class="mb-1"><strong>SĐT:</strong></p>
                                    <p class="ms-4 mb-0">{{ $sinhVien->guardian_phone ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-door-open fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Bạn chưa được phân phòng</h4>
                <p class="text-muted">Vui lòng liên hệ quản trị viên để được phân phòng.</p>
            </div>
        </div>
    </div>
</div>
@endif

@push('styles')
<style>
    /* Header */
    .page-header-dark {
        background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
        border-radius: 15px;
        box-shadow: 0 6px 20px rgba(26, 35, 126, 0.4);
        margin-bottom: 30px;
        overflow: hidden;
    }

    .page-header-dark h4 {
        font-size: 20px;
        letter-spacing: 0.5px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    /* Card Styles */
    .card {
        border: none;
        border-radius: 15px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }

    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15) !important;
    }

    .card-header.bg-info {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
        border-radius: 15px 15px 0 0 !important;
        padding: 20px 25px;
        box-shadow: 0 2px 10px rgba(23, 162, 184, 0.2);
    }

    .card-header h5 {
        font-size: 18px;
        font-weight: 700;
        letter-spacing: 0.3px;
    }

    .card-body {
        padding: 30px;
        background: #ffffff;
    }

    /* Ảnh phòng */
    .room-image-wrapper {
        position: relative;
        overflow: hidden;
        border-radius: 15px;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        padding: 10px;
    }

    .room-image {
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 3px solid #fff;
    }

    .room-image:hover {
        transform: scale(1.03);
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.25);
    }

    .room-divider {
        border: none;
        height: 2px;
        background: linear-gradient(90deg, transparent, #17a2b8, transparent);
        margin: 25px 0;
    }

    /* Info Items */
    .info-item {
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
        transition: all 0.3s ease;
    }

    .info-item:hover {
        background: linear-gradient(90deg, rgba(23, 162, 184, 0.05), transparent);
        padding-left: 8px;
        border-radius: 8px;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-label {
        color: #495057;
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
    }

    .info-label i {
        color: #17a2b8;
        width: 20px;
    }

    .info-value {
        color: #212529;
        font-size: 15px;
        font-weight: 500;
    }

    /* Badges */
    .badge-room-type {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        color: white;
        padding: 8px 20px;
        border-radius: 25px;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 4px 10px rgba(23, 162, 184, 0.3);
        letter-spacing: 0.5px;
    }

    .badge-status {
        padding: 8px 18px;
        border-radius: 25px;
        font-size: 13px;
        font-weight: 600;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
    }

    .gender-badge {
        display: inline-block;
        padding: 8px 18px;
        border-radius: 25px;
        font-size: 13px;
        font-weight: 600;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }

    .gender-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .gender-badge.nam {
        background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
        color: white;
    }

    .gender-badge.nu {
        background: linear-gradient(135deg, #e91e63 0%, #c2185b 100%);
        color: white;
    }

    .gender-badge.cả-hai {
        background: linear-gradient(135deg, #9c27b0 0%, #7b1fa2 100%);
        color: white;
    }

    /* Price Section */
    .price-section {
        margin: 25px 0;
    }

    .price-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 15px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
    }

    .price-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #17a2b8, #138496);
    }

    .price-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        border-color: #17a2b8;
    }

    .price-card-total::before {
        background: linear-gradient(90deg, #28a745, #20c997);
    }

    .price-card-slot::before {
        background: linear-gradient(90deg, #17a2b8, #138496);
    }

    .price-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
        flex-shrink: 0;
    }

    .price-card-total .price-icon {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    }

    .price-card-slot .price-icon {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);
    }

    .price-content {
        flex: 1;
    }

    .price-label {
        font-size: 13px;
        color: #6c757d;
        margin-bottom: 5px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .price-value {
        font-size: 22px;
        font-weight: 700;
        color: #212529;
        margin-bottom: 0;
        line-height: 1.2;
    }

    .price-card-total .price-value {
        color: #28a745;
    }

    .price-card-slot .price-value {
        color: #17a2b8;
    }

    .price-unit {
        font-size: 14px;
        font-weight: 500;
        color: #6c757d;
    }

    .price-note {
        margin-top: 5px;
        margin-bottom: 0;
        color: #6c757d;
        font-size: 12px;
    }

    /* Description Box */
    .description-box {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        border-left: 4px solid #17a2b8;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .description-box:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateX(3px);
    }

    /* Buttons */
    .btn-link {
        text-decoration: none;
        color: #17a2b8;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-link:hover {
        text-decoration: underline;
        color: #138496;
        transform: translateX(3px);
    }

    .collapse .card-body {
        max-height: 300px;
        overflow-y: auto;
        border-radius: 10px;
    }

    /* Tài sản cards */
    .tai-san-card {
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: #ffffff;
    }

    .tai-san-card:hover {
        border-color: #17a2b8;
        box-shadow: 0 6px 20px rgba(23, 162, 184, 0.2);
        transform: translateY(-3px);
    }

    /* Nút thu gọn */
    .btn-outline-secondary {
        border-color: #6c757d;
        color: #6c757d;
        transition: all 0.3s ease;
        border-radius: 8px;
    }

    .btn-outline-secondary:hover {
        background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
        border-color: #6c757d;
        color: white;
        transform: scale(1.05);
    }

    .btn-outline-secondary i {
        transition: transform 0.3s ease;
    }

    /* Ảnh sinh viên chính */
    .student-profile-header {
        position: relative;
        padding: 20px 0;
    }

    .student-avatar-wrapper {
        position: relative;
        display: inline-block;
    }

    .avatar-ring {
        position: relative;
        width: 160px;
        height: 160px;
        margin: 0 auto;
        border-radius: 50%;
        background: linear-gradient(135deg, #17a2b8 0%, #138496 50%, #0d6efd 100%);
        padding: 5px;
        box-shadow: 0 8px 25px rgba(23, 162, 184, 0.4);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .avatar-ring:hover {
        transform: scale(1.05);
        box-shadow: 0 12px 35px rgba(23, 162, 184, 0.5);
    }

    .student-main-avatar {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #ffffff;
        display: block;
    }

    .avatar-badge {
        position: absolute;
        bottom: 10px;
        right: 10px;
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
        border: 3px solid #ffffff;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }
    }

    .student-name-title {
        font-size: 20px;
        font-weight: 700;
        color: #212529;
        letter-spacing: 0.3px;
        margin-top: 15px !important;
    }

    .student-id-title {
        color: #6c757d;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-bottom: 0;
    }

    /* Danh sách sinh viên trong phòng */
    .student-list-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border: 1px solid #e9ecef;
        margin-top: 10px;
    }

    .student-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .student-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        margin-bottom: 12px;
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e9ecef;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .student-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        transform: scaleY(0);
        transition: transform 0.3s ease;
    }

    .student-item:hover {
        transform: translateX(5px);
        box-shadow: 0 6px 20px rgba(23, 162, 184, 0.15);
        border-color: #17a2b8;
    }

    .student-item:hover::before {
        transform: scaleY(1);
    }

    .student-item:last-child {
        margin-bottom: 0;
    }

    .student-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        overflow: hidden;
        flex-shrink: 0;
        border: 3px solid #17a2b8;
        box-shadow: 0 3px 10px rgba(23, 162, 184, 0.2);
        transition: all 0.3s ease;
    }

    .student-item:hover .student-avatar {
        transform: scale(1.1);
        box-shadow: 0 5px 15px rgba(23, 162, 184, 0.3);
    }

    .student-avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .student-info {
        flex: 1;
        min-width: 0;
    }

    .student-name {
        font-size: 15px;
        font-weight: 700;
        color: #212529;
        margin-bottom: 4px;
        line-height: 1.3;
    }

    .student-id {
        font-size: 13px;
        color: #6c757d;
        font-weight: 500;
        letter-spacing: 0.3px;
    }

    /* Info Section Header với Collapse */
    .info-section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        margin: 0 -20px 15px -20px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        user-select: none;
        border-bottom: 2px solid transparent;
    }

    .info-section-header:hover {
        background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
        border-bottom-color: #17a2b8;
        transform: translateX(3px);
    }

    .info-section-header h6 {
        margin: 0;
        color: #495057;
        font-size: 15px;
        display: flex;
        align-items: center;
    }

    .info-section-header .toggle-icon {
        color: #17a2b8;
        font-size: 14px;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        flex-shrink: 0;
    }

    .info-section-header[aria-expanded="false"] .toggle-icon {
        transform: rotate(-90deg);
    }

    .info-section-header[aria-expanded="true"] .toggle-icon {
        transform: rotate(0deg);
    }

    .info-section-content {
        padding: 10px 0;
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .collapse:not(.show) {
        display: none;
    }

    /* Responsive */
    @media (max-width: 991px) {
        .page-header-dark {
            margin-bottom: 20px;
            border-radius: 12px;
        }

        .page-header-dark h4 {
            font-size: 16px;
        }

        .page-header-dark .d-flex {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }

        .card-body {
            padding: 20px;
        }

        .price-card {
            flex-direction: column;
            text-align: center;
        }

        .price-icon {
            width: 50px;
            height: 50px;
            font-size: 20px;
        }
    }

    @media (max-width: 768px) {
        .card-header h5 {
            font-size: 16px;
        }

        .info-item p {
            font-size: 13px;
        }

        .avatar-ring {
            width: 130px !important;
            height: 130px !important;
        }

        .avatar-badge {
            width: 35px !important;
            height: 35px !important;
            font-size: 16px !important;
            bottom: 5px !important;
            right: 5px !important;
        }

        .student-name-title {
            font-size: 18px;
        }

        .price-value {
            font-size: 18px;
        }

        .room-image {
            max-height: 250px !important;
        }

        .student-item {
            padding: 12px;
            gap: 12px;
        }

        .student-avatar {
            width: 45px;
            height: 45px;
        }

        .student-name {
            font-size: 14px;
        }

        .student-id {
            font-size: 12px;
        }

        .info-section-header {
            padding: 12px 15px;
            margin: 0 -15px 12px -15px;
        }

        .info-section-header h6 {
            font-size: 14px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Toggle icon chevron khi thu gọn/mở rộng phần tài sản
    document.addEventListener('DOMContentLoaded', function() {
        // Tài sản của phòng
        const taiSanPhongCollapse = document.getElementById('taiSanPhongCollapse');
        const taiSanPhongChevron = document.getElementById('taiSanPhongChevron');

        if (taiSanPhongCollapse && taiSanPhongChevron) {
            taiSanPhongCollapse.addEventListener('show.bs.collapse', function() {
                taiSanPhongChevron.classList.remove('fa-chevron-down');
                taiSanPhongChevron.classList.add('fa-chevron-up');
            });

            taiSanPhongCollapse.addEventListener('hide.bs.collapse', function() {
                taiSanPhongChevron.classList.remove('fa-chevron-up');
                taiSanPhongChevron.classList.add('fa-chevron-down');
            });
        }

        // Tài sản riêng của bạn
        const taiSanCaNhanCollapse = document.getElementById('taiSanCaNhanCollapse');
        const taiSanCaNhanChevron = document.getElementById('taiSanCaNhanChevron');

        if (taiSanCaNhanCollapse && taiSanCaNhanChevron) {
            taiSanCaNhanCollapse.addEventListener('show.bs.collapse', function() {
                taiSanCaNhanChevron.classList.remove('fa-chevron-down');
                taiSanCaNhanChevron.classList.add('fa-chevron-up');
            });

            taiSanCaNhanCollapse.addEventListener('hide.bs.collapse', function() {
                taiSanCaNhanChevron.classList.remove('fa-chevron-up');
                taiSanCaNhanChevron.classList.add('fa-chevron-down');
            });
        }

        // Toggle icon cho các section thông tin cá nhân
        const infoSections = [{
                id: 'thongTinCoBan',
                header: document.querySelector('[data-bs-target="#thongTinCoBan"]')
            },
            {
                id: 'thongTinHocTap',
                header: document.querySelector('[data-bs-target="#thongTinHocTap"]')
            },
            {
                id: 'lienHe',
                header: document.querySelector('[data-bs-target="#lienHe"]')
            },
            {
                id: 'canCuocCongDan',
                header: document.querySelector('[data-bs-target="#canCuocCongDan"]')
            },
            {
                id: 'nguoiLienHe',
                header: document.querySelector('[data-bs-target="#nguoiLienHe"]')
            }
        ];

        infoSections.forEach(section => {
            if (section.header) {
                const collapse = document.getElementById(section.id);
                const toggleIcon = section.header.querySelector('.toggle-icon');

                if (collapse && toggleIcon) {
                    collapse.addEventListener('show.bs.collapse', function() {
                        section.header.setAttribute('aria-expanded', 'true');
                    });

                    collapse.addEventListener('hide.bs.collapse', function() {
                        section.header.setAttribute('aria-expanded', 'false');
                    });
                }
            }
        });
    });
    document.querySelectorAll('.btn-bao-hong').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('modal_tai_san_id').value = this.dataset.id;
            document.getElementById('modal_tai_san_ten').innerText = this.dataset.ten;
        });
    });
</script>
@endpush
<!-- Modal Báo Hỏng -->
<div class="modal fade" id="modalBaoHong" tabindex="-1">
    <div class="modal-dialog">
        <form id="formBaoHong" method="POST" enctype="multipart/form-data" action="{{ route('client.baoHong') }}">
            @csrf
            <input type="hidden" name="tai_san_id" id="modal_tai_san_id">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Báo hỏng tài sản: <span id="modal_tai_san_ten"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Mô tả sự cố</label>
                        <textarea name="mo_ta" class="form-control" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Hình ảnh minh chứng</label>
                        <input type="file" name="hinh_anh_truoc" class="form-control" accept="image/*">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Gửi báo hỏng</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection