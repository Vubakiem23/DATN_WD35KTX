@extends('client.layouts.app')

@section('title', 'Thông tin cá nhân - Sinh viên')

@push('styles')
<style>
    .profile-page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }

    .profile-page-header h2 {
        margin: 0;
        font-weight: 600;
        font-size: 28px;
    }

    .profile-page-header i {
        font-size: 32px;
        opacity: 0.9;
    }

    .profile-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        margin-bottom: 25px;
    }

    .profile-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
    }

    .profile-card-header {
        padding: 20px 25px;
        border-bottom: none;
        font-weight: 600;
        font-size: 18px;
    }

    .profile-card-header.primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .profile-card-header.info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }

    .profile-card-header.success {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        color: white;
    }

    .profile-card-body {
        padding: 30px;
    }

    .info-item {
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
        transition: background-color 0.2s ease;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-item:hover {
        background-color: #f8f9fa;
        margin: 0 -15px;
        padding-left: 15px;
        padding-right: 15px;
        border-radius: 8px;
    }

    .info-item strong {
        color: #495057;
        font-weight: 600;
        min-width: 150px;
        display: inline-block;
    }

    .info-item p {
        margin: 0;
        color: #6c757d;
        display: inline;
    }

    .profile-avatar-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        margin-bottom: 25px;
    }

    .profile-avatar-wrapper {
        position: relative;
        padding: 30px;
        text-align: center;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }

    .profile-avatar {
        width: 250px;
        height: 250px;
        border-radius: 15px;
        object-fit: cover;
        border: 5px solid white;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s ease;
    }

    .profile-avatar:hover {
        transform: scale(1.05);
    }

    .profile-avatar-placeholder {
        width: 250px;
        height: 250px;
        border-radius: 15px;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        border: 5px solid white;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .profile-avatar-placeholder i {
        font-size: 120px;
        color: #dee2e6;
    }

    .status-badge {
        padding: 10px 20px;
        border-radius: 25px;
        font-weight: 600;
        font-size: 14px;
        display: inline-block;
    }

    .section-divider {
        height: 2px;
        background: linear-gradient(90deg, transparent, #dee2e6, transparent);
        border: none;
        margin: 25px 0;
    }

    .section-title {
        color: #495057;
        font-weight: 600;
        font-size: 16px;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e9ecef;
    }

    .violation-table {
        border-radius: 10px;
        overflow: hidden;
    }

    .violation-table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .violation-table thead th {
        border: none;
        padding: 15px;
        font-weight: 600;
    }

    .violation-table tbody tr {
        transition: background-color 0.2s ease;
    }

    .violation-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .violation-table tbody td {
        padding: 15px;
        vertical-align: middle;
        border-color: #f0f0f0;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state i {
        font-size: 80px;
        color: #dee2e6;
        margin-bottom: 20px;
    }

    .empty-state h4 {
        color: #6c757d;
        margin-bottom: 15px;
    }

    .empty-state p {
        color: #adb5bd;
    }

    @media (max-width: 768px) {
        .profile-page-header {
            padding: 20px;
        }

        .profile-page-header h2 {
            font-size: 22px;
        }

        .profile-card-body {
            padding: 20px;
        }

        .info-item strong {
            min-width: auto;
            display: block;
            margin-bottom: 5px;
        }

        .profile-avatar {
            width: 200px;
            height: 200px;
        }

        .profile-avatar-placeholder {
            width: 200px;
            height: 200px;
        }
    }
</style>
@endpush

@section('content')
<div class="profile-page-header">
    <h2>
        <i class="fas fa-user me-3"></i>
        Thông tin cá nhân
    </h2>
</div>

@if(!$sinhVien)
<!-- Thông báo chưa nộp hồ sơ -->
<div class="row">
    <div class="col-12">
        <div class="profile-card">
            <div class="profile-card-body">
                <div class="empty-state">
                    <i class="fas fa-file-alt"></i>
                    <h4>Bạn chưa nộp hồ sơ đăng ký ký túc xá</h4>
                    <p>Vui lòng nộp hồ sơ để xem thông tin cá nhân của bạn.</p>
                </div>
            </div>
        </div>
        <div class="profile-card mt-4">
            <div class="profile-card-header info">
                <h5 class="mb-0">
                    <i class="fas fa-user-circle me-2"></i>
                    Thông tin tài khoản
                </h5>
            </div>
            <div class="profile-card-body">
                <div class="info-item">
                    <strong>Họ tên:</strong>
                    <p>{{ $user->name ?? 'N/A' }}</p>
                </div>
                <div class="info-item">
                    <strong>Email:</strong>
                    <p>{{ $user->email ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="row">
    <div class="col-md-8">
        <div class="profile-card">
            <div class="profile-card-header primary">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Chi tiết thông tin
                </h5>
            </div>
            <div class="profile-card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <strong>Mã sinh viên:</strong>
                            <p>{{ $sinhVien->ma_sinh_vien }}</p>
                        </div>
                        <div class="info-item">
                            <strong>Họ tên:</strong>
                            <p>{{ $sinhVien->ho_ten }}</p>
                        </div>
                        <div class="info-item">
                            <strong>Ngày sinh:</strong>
                            <p>{{ $sinhVien->ngay_sinh ? $sinhVien->ngay_sinh->format('d/m/Y') : 'N/A' }}</p>
                        </div>
                        <div class="info-item">
                            <strong>Giới tính:</strong>
                            <p>{{ $sinhVien->gioi_tinh }}</p>
                        </div>
                        <div class="info-item">
                            <strong>Quê quán:</strong>
                            <p>{{ $sinhVien->que_quan }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <strong>Nơi ở hiện tại:</strong>
                            <p>{{ $sinhVien->noi_o_hien_tai }}</p>
                        </div>
                        <div class="info-item">
                            <strong>Lớp:</strong>
                            <p>{{ $sinhVien->lop }}</p>
                        </div>
                        <div class="info-item">
                            <strong>Ngành:</strong>
                            <p>{{ $sinhVien->nganh }}</p>
                        </div>
                        <div class="info-item">
                            <strong>Khóa học:</strong>
                            <p>{{ $sinhVien->khoa_hoc }}</p>
                        </div>
                        <div class="info-item">
                            <strong>Số điện thoại:</strong>
                            <p>{{ $sinhVien->so_dien_thoai }}</p>
                        </div>
                        <div class="info-item">
                            <strong>Email:</strong>
                            <p>{{ $sinhVien->email }}</p>
                        </div>
                    </div>
                </div>
                
                @if($sinhVien->citizen_id_number)
                <hr class="section-divider">
                <h6 class="section-title">
                    <i class="fas fa-id-card me-2"></i>
                    Thông tin CMND/CCCD
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <strong>Số CMND/CCCD:</strong>
                            <p>{{ $sinhVien->citizen_id_number }}</p>
                        </div>
                        <div class="info-item">
                            <strong>Ngày cấp:</strong>
                            <p>{{ $sinhVien->citizen_issue_date ? $sinhVien->citizen_issue_date->format('d/m/Y') : 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <strong>Nơi cấp:</strong>
                            <p>{{ $sinhVien->citizen_issue_place }}</p>
                        </div>
                    </div>
                </div>
                @endif
                
                @if($sinhVien->guardian_name)
                <hr class="section-divider">
                <h6 class="section-title">
                    <i class="fas fa-users me-2"></i>
                    Thông tin người giám hộ
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <strong>Họ tên:</strong>
                            <p>{{ $sinhVien->guardian_name }}</p>
                        </div>
                        <div class="info-item">
                            <strong>Số điện thoại:</strong>
                            <p>{{ $sinhVien->guardian_phone }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <strong>Quan hệ:</strong>
                            <p>{{ $sinhVien->guardian_relationship }}</p>
                        </div>
                    </div>
                </div>
                @endif
                
                <hr class="section-divider">
                <h6 class="section-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Vi phạm của sinh viên
                </h6>
                @php
                    $violations = $sinhVien->violations()->with('type')->latest('occurred_at')->get();
                @endphp
                @if($violations->count())
                <div class="table-responsive">
                    <table class="table violation-table">
                        <thead>
                            <tr>
                                <th>Ngày</th>
                                <th>Loại vi phạm</th>
                                <th>Trạng thái</th>
                                <th>Tiền phạt</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($violations as $vp)
                            <tr>
                                <td>{{ optional($vp->occurred_at)->format('d/m/Y') ?? 'N/A' }}</td>
                                <td>{{ $vp->type->name ?? 'Không rõ' }}</td>
                                <td>
                                    @php
                                        $status = strtolower((string)$vp->status);
                                        $processed = in_array($status, ['resolved','paid'], true);
                                    @endphp
                                    <span class="badge bg-{{ $processed ? 'success' : 'warning' }}">
                                        {{ $processed ? 'Đã xử lý' : 'Chưa xử lý' }}
                                    </span>
                                </td>
                                <td>{{ is_null($vp->penalty_amount) ? '0' : number_format((float)$vp->penalty_amount, 0, ',', '.') }} đ</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    <p class="text-muted fst-italic text-center py-3">
                        <i class="fas fa-check-circle me-2"></i>
                        Chưa có vi phạm nào.
                    </p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="profile-avatar-card">
            <div class="profile-card-header info">
                <h5 class="mb-0">
                    <i class="fas fa-image me-2"></i>
                    Ảnh đại diện
                </h5>
            </div>
            <div class="profile-avatar-wrapper">
                @if($sinhVien->anh_sinh_vien)
                    <img src="{{ asset('storage/' . $sinhVien->anh_sinh_vien) }}" 
                         alt="Ảnh sinh viên" 
                         class="profile-avatar">
                @else
                    <div class="profile-avatar-placeholder">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <p class="text-muted mt-3 mb-0">Chưa có ảnh</p>
                @endif
            </div>
        </div>
        
        <div class="profile-card">
            <div class="profile-card-header success">
                <h5 class="mb-0">
                    <i class="fas fa-check-circle me-2"></i>
                    Trạng thái hồ sơ
                </h5>
            </div>
            <div class="profile-card-body text-center">
                <p class="mb-0">
                    <strong class="d-block mb-2">Trạng thái:</strong>
                    <span class="status-badge bg-{{ $sinhVien->trang_thai_ho_so == 'Đã duyệt' ? 'success' : 'warning' }} text-white">
                        {{ $sinhVien->trang_thai_ho_so ?? 'Chờ duyệt' }}
                    </span>
                </p>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
