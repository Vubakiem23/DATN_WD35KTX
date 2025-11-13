@extends('client.layouts.app')

@section('title', 'Trang chủ - Sinh viên')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-0">
            <i class="fas fa-home text-primary me-2"></i>
            Chào mừng, {{ $sinhVien->ho_ten ?? ($user->name ?? 'Sinh viên') }}!
        </h2>
        <p class="text-muted">Thông tin tổng quan về ký túc xá của bạn</p>
    </div>
</div>

@if(!$sinhVien)
<!-- Thông báo chưa nộp hồ sơ -->
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-info d-flex align-items-center" role="alert">
            <i class="fas fa-info-circle fa-2x me-3"></i>
            <div>
                <h5 class="alert-heading mb-1">Bạn chưa nộp hồ sơ đăng ký ký túc xá</h5>
                <p class="mb-0">Vui lòng nộp hồ sơ để sử dụng đầy đủ các tính năng của hệ thống.</p>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Thống kê nhanh -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-door-open fa-3x text-primary mb-3"></i>
                <h5 class="card-title">Phòng</h5>
                <h3 class="text-primary">
                    {{ $stats['phong']->ten_phong ?? 'Chưa có phòng' }}
                </h3>
                @if($stats['phong'])
                    <small class="text-muted">{{ $stats['phong']->khu->ten_khu ?? '' }}</small>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-tools fa-3x text-warning mb-3"></i>
                <h5 class="card-title">Sự cố đã báo</h5>
                <h3 class="text-warning">{{ $stats['so_su_co'] }}</h3>
                <small class="text-muted">Tổng số sự cố</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-clock fa-3x text-info mb-3"></i>
                <h5 class="card-title">Đang xử lý</h5>
                <h3 class="text-info">{{ $stats['su_co_chua_xu_ly'] }}</h3>
                <small class="text-muted">Sự cố chưa xử lý</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-file-invoice fa-3x text-danger mb-3"></i>
                <h5 class="card-title">Hóa đơn</h5>
                <h3 class="text-danger">{{ $stats['hoa_don_chua_thanh_toan'] }}</h3>
                <small class="text-muted">Chưa thanh toán</small>
            </div>
        </div>
    </div>
</div>

<!-- Thông tin phòng -->
@if($stats['phong'])
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-door-open me-2"></i>
                    Thông tin phòng của bạn
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Tên phòng:</strong> {{ $stats['phong']->ten_phong }}</p>
                        <p><strong>Khu:</strong> {{ $stats['phong']->khu->ten_khu ?? 'N/A' }}</p>
                        <p><strong>Số người:</strong> 
                            {{ $stats['phong'] ? $stats['phong']->usedSlots() : 'N/A' }} người
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Trạng thái:</strong> 
                            <span class="badge bg-{{ in_array($stats['phong']->trang_thai ?? '', ['Đang sử dụng', 'Đã ở']) ? 'success' : 'secondary' }}">
                                {{ $stats['phong']->trang_thai ?? 'N/A' }}
                            </span>
                        </p>
                        <p><strong>Mô tả:</strong> {{ $stats['phong']->ghi_chu ?? ($stats['phong']->mo_ta ?? 'Không có mô tả') }}</p>
                    </div>
                </div>
                <a href="{{ route('client.phong') }}" class="btn btn-primary mt-2">
                    <i class="fas fa-eye me-2"></i> Xem chi tiết phòng
                </a>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Sự cố gần đây -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-tools me-2"></i>
                    Sự cố gần đây
                </h5>
                <a href="{{ route('client.suco.index') }}" class="btn btn-sm btn-primary">
                    Xem tất cả <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body">
                @if($suCoGanDay->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Ngày gửi</th>
                                    <th>Mô tả</th>
                                    <th>Phòng</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($suCoGanDay as $suCo)
                                <tr>
                                    <td>{{ $suCo->ngay_gui ? \Illuminate\Support\Carbon::parse($suCo->ngay_gui)->format('d/m/Y') : 'N/A' }}</td>
                                    <td>{{ Str::limit($suCo->mo_ta, 50) }}</td>
                                    <td>{{ $suCo->phong->ten_phong ?? 'N/A' }}</td>
                                    <td>
                                        @if($suCo->trang_thai == 'Tiếp nhận')
                                            <span class="badge bg-secondary">Tiếp nhận</span>
                                        @elseif($suCo->trang_thai == 'Đang xử lý')
                                            <span class="badge bg-warning text-dark">Đang xử lý</span>
                                        @elseif($suCo->trang_thai == 'Hoàn thành')
                                            <span class="badge bg-success">Hoàn thành</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $suCo->trang_thai }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center py-4">
                        <i class="fas fa-inbox fa-2x mb-3 d-block"></i>
                        Chưa có sự cố nào được báo cáo.
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
