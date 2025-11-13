@extends('client.layouts.app')

@section('title', 'Thông tin phòng - Sinh viên')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-0">
            <i class="fas fa-door-open text-primary me-2"></i>
            Thông tin phòng của tôi
        </h2>
    </div>
</div>

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
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Chi tiết phòng: {{ $phong->ten_phong }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong><i class="fas fa-building me-2"></i>Khu:</strong> 
                            {{ $phong->khu->ten_khu ?? 'N/A' }}
                        </p>
                        <p><strong><i class="fas fa-users me-2"></i>Số người đang ở:</strong> 
                            {{ isset($soNguoiTrongPhong) ? $soNguoiTrongPhong : 'N/A' }} người
                        </p>
                        <p><strong><i class="fas fa-check-circle me-2"></i>Trạng thái:</strong> 
                            <span class="badge bg-{{ $phong->trang_thai == 'Đang sử dụng' ? 'success' : 'secondary' }}">
                                {{ $phong->trang_thai ?? 'N/A' }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p><strong><i class="fas fa-file-alt me-2"></i>Mô tả:</strong></p>
                        <p>{{ $phong->ghi_chu ?? 'Không có mô tả' }}</p>
                    </div>
                </div>

                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mb-3"><i class="fas fa-boxes me-2 text-secondary"></i>Tài sản của phòng</h6>
                        @if(isset($taiSanPhong) && $taiSanPhong->count())
                            <ul class="list-group">
                                @foreach($taiSanPhong as $ts)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            {{ $ts->khoTaiSan->ten_tai_san ?? $ts->ten_tai_san ?? 'Không rõ' }}
                                        </span>
                                        <span class="badge bg-primary rounded-pill">{{ (int)($ts->so_luong ?? 0) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted fst-italic">Chưa có tài sản phòng.</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-3"><i class="fas fa-hand-holding me-2 text-secondary"></i>Tài sản bàn giao riêng cho bạn</h6>
                        @if(isset($taiSanCaNhan) && $taiSanCaNhan->count())
                            <ul class="list-group">
                                @foreach($taiSanCaNhan as $ts)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            {{ $ts->khoTaiSan->ten_tai_san ?? $ts->ten_tai_san ?? 'Không rõ' }}
                                        </span>
                                        <span class="badge bg-success rounded-pill">
                                            {{ (int)($ts->pivot->so_luong ?? 0) }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted fst-italic">Chưa có tài sản được bàn giao.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-user me-2"></i>
                    Thông tin của bạn
                </h5>
            </div>
            <div class="card-body">
                <p><strong>Họ tên:</strong> {{ $sinhVien->ho_ten ?? 'N/A' }}</p>
                <p><strong>Mã SV:</strong> {{ $sinhVien->ma_sinh_vien ?? 'N/A' }}</p>
                <p><strong>Ngày sinh:</strong> {{ optional($sinhVien->ngay_sinh)->format('d/m/Y') ?? 'N/A' }}</p>
                <p><strong>Giới tính:</strong> {{ $sinhVien->gioi_tinh ?? 'N/A' }}</p>
                <p><strong>Quê quán:</strong> {{ $sinhVien->que_quan ?? 'N/A' }}</p>
                <p><strong>Nơi ở hiện tại:</strong> {{ $sinhVien->noi_o_hien_tai ?? 'N/A' }}</p>
                <p><strong>Lớp:</strong> {{ $sinhVien->lop ?? 'N/A' }}</p>
                <p><strong>Ngành:</strong> {{ $sinhVien->nganh ?? 'N/A' }}</p>
                <p><strong>Khóa học:</strong> {{ $sinhVien->khoa_hoc ?? 'N/A' }}</p>
                <p><strong>Email:</strong> {{ $sinhVien->email ?? 'N/A' }}</p>
                <p><strong>SĐT:</strong> {{ $sinhVien->so_dien_thoai ?? 'N/A' }}</p>
                <hr>
                <p class="mb-2 fw-bold text-secondary">Căn cước công dân</p>
                <p><strong>Số CCCD:</strong> {{ $sinhVien->citizen_id_number ?? 'N/A' }}</p>
                <p><strong>Ngày cấp:</strong> {{ optional($sinhVien->citizen_issue_date)->format('d/m/Y') ?? 'N/A' }}</p>
                <p><strong>Nơi cấp:</strong> {{ $sinhVien->citizen_issue_place ?? 'N/A' }}</p>
                <hr>
                <p class="mb-2 fw-bold text-secondary">Người liên hệ</p>
                <p><strong>Họ tên:</strong> {{ $sinhVien->guardian_name ?? 'N/A' }}</p>
                <p><strong>Quan hệ:</strong> {{ $sinhVien->guardian_relationship ?? 'N/A' }}</p>
                <p><strong>SĐT:</strong> {{ $sinhVien->guardian_phone ?? 'N/A' }}</p>
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
@endsection
