@extends('client.layouts.app')

@section('title', 'Trang chủ - Sinh viên')

@section('content')
<style>
    /* Welcome Section */
    .welcome-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 30px;
        color: white;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }

    .welcome-section h2 {
        color: white;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .welcome-section p {
        color: rgba(255, 255, 255, 0.9);
        margin: 0;
        font-size: 16px;
    }

    /* Stats Cards */
    .stats-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        text-align: center;
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        position: relative;
        overflow: hidden;
        height: 100%;
    }

    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, transparent, currentColor, transparent);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }

    .stats-card:hover::before {
        opacity: 1;
    }

    .stats-card.primary {
        color: #0066cc;
    }

    .stats-card.primary:hover {
        box-shadow: 0 12px 30px rgba(0, 102, 204, 0.3);
    }

    .stats-card.warning {
        color: #ff9800;
    }

    .stats-card.warning:hover {
        box-shadow: 0 12px 30px rgba(255, 152, 0, 0.3);
    }

    .stats-card.info {
        color: #17a2b8;
    }

    .stats-card.info:hover {
        box-shadow: 0 12px 30px rgba(23, 162, 184, 0.3);
    }

    .stats-card.danger {
        color: #dc3545;
    }

    .stats-card.danger:hover {
        box-shadow: 0 12px 30px rgba(220, 53, 69, 0.3);
    }

    .stats-icon {
        font-size: 48px;
        margin-bottom: 15px;
        display: block;
        transition: transform 0.3s ease;
    }

    .stats-card:hover .stats-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .stats-title {
        font-size: 14px;
        font-weight: 600;
        color: #666;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stats-value {
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 8px;
        line-height: 1;
    }

    .stats-subtitle {
        font-size: 12px;
        color: #999;
        margin: 0;
    }

    /* Room Info Card */
    .room-info-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        margin-bottom: 30px;
        border: none;
    }

    .room-info-header {
        background: linear-gradient(135deg, #0066cc 0%, #004d99 100%);
        color: white;
        padding: 20px 25px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .room-info-header i {
        font-size: 24px;
    }

    .room-info-header h5 {
        margin: 0;
        font-weight: 600;
        font-size: 18px;
    }

    .room-info-body {
        padding: 25px;
    }

    .room-info-content {
        display: grid;
        grid-template-columns: 300px 1fr;
        gap: 25px;
        margin-bottom: 20px;
    }

    .room-image-container {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        height: fit-content;
    }

    .room-image-container img {
        width: 100%;
        height: auto;
        display: block;
        object-fit: cover;
    }

    .room-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .room-info-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .room-info-label {
        font-size: 12px;
        color: #666;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .room-info-value {
        font-size: 16px;
        color: #333;
        font-weight: 500;
    }

    .room-status-badge {
        display: inline-block;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        background: #10b981;
        color: white;
    }

    .room-view-btn {
        background: linear-gradient(135deg, #0066cc 0%, #004d99 100%);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .room-view-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 102, 204, 0.3);
        color: white;
    }

    /* Recent Issues Card */
    .recent-issues-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: none;
    }

    .recent-issues-header {
        background: #f8f9fa;
        padding: 20px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #e9ecef;
    }

    .recent-issues-header h5 {
        margin: 0;
        font-weight: 600;
        font-size: 18px;
        color: #333;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .recent-issues-header i {
        color: #0066cc;
        font-size: 20px;
    }

    .view-all-btn {
        background: #0066cc;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .view-all-btn:hover {
        background: #0052a3;
        transform: translateX(5px);
        color: white;
    }

    .recent-issues-body {
        padding: 25px;
    }

    .issues-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .issue-item {
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
        transition: all 0.3s ease;
        border-radius: 8px;
        margin-bottom: 10px;
    }

    .issue-item:hover {
        background: #f8f9fa;
        transform: translateX(5px);
    }

    .issue-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .issue-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 8px;
    }

    .issue-date {
        font-size: 13px;
        color: #666;
        font-weight: 500;
    }

    .issue-status {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .issue-status.pending {
        background: #6c757d;
        color: white;
    }

    .issue-status.processing {
        background: #ffc107;
        color: #000;
    }

    .issue-status.completed {
        background: #28a745;
        color: white;
    }

    .issue-description {
        font-size: 14px;
        color: #333;
        margin-bottom: 5px;
    }

    .issue-room {
        font-size: 12px;
        color: #999;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }

    .empty-state i {
        font-size: 64px;
        margin-bottom: 20px;
        opacity: 0.3;
    }

    .empty-state p {
        font-size: 16px;
        margin: 0;
    }

    /* Alert */
    .info-alert {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        border: none;
        border-radius: 12px;
        padding: 20px;
        color: white;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);
    }

    .info-alert i {
        font-size: 32px;
        margin-right: 15px;
    }

    .info-alert h5 {
        color: white;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .info-alert p {
        color: rgba(255, 255, 255, 0.9);
        margin: 0;
    }

    .price-info {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 15px;
        border-radius: 10px;
        margin-top: 15px;
    }

    .price-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .price-item:last-child {
        border-bottom: none;
    }

    .price-label {
        font-size: 13px;
        color: #666;
        font-weight: 600;
    }

    .price-value {
        font-size: 16px;
        color: #0066cc;
        font-weight: 700;
    }

    .gender-badge {
        display: inline-block;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }

    .gender-badge.nam {
        background: #4a90e2;
        color: white;
    }

    .gender-badge.nu {
        background: #e91e63;
        color: white;
    }

    .gender-badge.cả-hai {
        background: #9c27b0;
        color: white;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .welcome-section {
            padding: 20px;
        }

        .room-info-content {
            grid-template-columns: 1fr;
        }

        .room-info-grid {
            grid-template-columns: 1fr;
        }

        .recent-issues-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        .stats-value {
            font-size: 28px;
        }
    }
</style>

<!-- Welcome Section -->
<div class="welcome-section">
    <h2>
        <i class="fas fa-home me-2"></i>
            Chào mừng, {{ $sinhVien->ho_ten ?? ($user->name ?? 'Sinh viên') }}!
        </h2>
    <p>Thông tin tổng quan về ký túc xá của bạn</p>
</div>

@if(!$sinhVien)
<!-- Thông báo chưa nộp hồ sơ -->
<div class="info-alert d-flex align-items-center">
    <i class="fas fa-info-circle"></i>
            <div>
        <h5>Bạn chưa nộp hồ sơ đăng ký ký túc xá</h5>
        <p>Vui lòng nộp hồ sơ để sử dụng đầy đủ các tính năng của hệ thống.</p>
    </div>
</div>
@endif

<!-- Thống kê nhanh -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card primary">
            <i class="fas fa-door-open stats-icon"></i>
            <div class="stats-title">Phòng</div>
            <div class="stats-value">
                {{ $stats['phong']->ten_phong ?? 'N/A' }}
            </div>
            @if($stats['phong'])
                <div class="stats-subtitle">{{ $stats['phong']->khu->ten_khu ?? '' }}</div>
            @else
                <div class="stats-subtitle">Chưa có phòng</div>
            @endif
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card warning">
            <i class="fas fa-tools stats-icon"></i>
            <div class="stats-title">Sự cố đã báo</div>
            <div class="stats-value">{{ $stats['so_su_co'] }}</div>
            <div class="stats-subtitle">Tổng số sự cố</div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card info">
            <i class="fas fa-clock stats-icon"></i>
            <div class="stats-title">Đang xử lý</div>
            <div class="stats-value">{{ $stats['su_co_chua_xu_ly'] }}</div>
            <div class="stats-subtitle">Sự cố chưa xử lý</div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card danger">
            <i class="fas fa-file-invoice stats-icon"></i>
            <div class="stats-title">Hóa đơn</div>
            <div class="stats-value">{{ $stats['hoa_don_chua_thanh_toan'] }}</div>
            <div class="stats-subtitle">Chưa thanh toán</div>
        </div>
    </div>
</div>

<!-- Thông tin phòng -->
@if($stats['phong'])
@php
    $phong = $stats['phong'];
    $totalSlots = $phong->totalSlots();
    $giaMoiSlot = null;
    if ($phong->gia_phong && $totalSlots > 0) {
        $giaMoiSlot = (int) round($phong->gia_phong / $totalSlots);
    }
    $gioiTinh = strtolower($phong->gioi_tinh ?? '');
    $gioiTinhClass = match($gioiTinh) {
        'nam' => 'nam',
        'nữ' => 'nu',
        'cả hai' => 'cả-hai',
        default => ''
    };
@endphp
<div class="room-info-card">
    <div class="room-info-header">
        <i class="fas fa-door-open"></i>
        <h5>Thông tin phòng của bạn</h5>
    </div>
    <div class="room-info-body">
        <div class="room-info-content">
            <!-- Ảnh phòng -->
            @if($phong->hinh_anh)
            <div class="room-image-container">
                <img src="{{ asset('storage/'.$phong->hinh_anh) }}" alt="{{ $phong->ten_phong }}">
            </div>
            @else
            <div class="room-image-container" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; min-height: 200px;">
                <i class="fas fa-door-open" style="font-size: 64px; color: white; opacity: 0.5;"></i>
            </div>
            @endif

            <!-- Thông tin chi tiết -->
            <div>
                <div class="room-info-grid">
                    <div class="room-info-item">
                        <div class="room-info-label">Tên phòng</div>
                        <div class="room-info-value">{{ $phong->ten_phong }}</div>
                    </div>
                    <div class="room-info-item">
                        <div class="room-info-label">Khu</div>
                        <div class="room-info-value">{{ $phong->khu->ten_khu ?? 'N/A' }}</div>
                    </div>
                    <div class="room-info-item">
                        <div class="room-info-label">Giới tính</div>
                        <div class="room-info-value">
                            <span class="gender-badge {{ $gioiTinhClass }}">
                                {{ $phong->gioi_tinh ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                    <div class="room-info-item">
                        <div class="room-info-label">Sức chứa</div>
                        <div class="room-info-value">
                            {{ $totalSlots }} người
                        </div>
                    </div>
                    <div class="room-info-item">
                        <div class="room-info-label">Số người đang ở</div>
                        <div class="room-info-value">
                            {{ $phong->usedSlots() }} / {{ $totalSlots }} người
                        </div>
                    </div>
                    <div class="room-info-item">
                        <div class="room-info-label">Trạng thái</div>
                        <div class="room-info-value">
                            <span class="room-status-badge">
                                {{ $phong->trang_thai ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Thông tin giá -->
                @if($phong->gia_phong)
                <div class="price-info">
                    <div class="price-item">
                        <span class="price-label">
                            <i class="fas fa-money-bill-wave me-1"></i>
                            Giá tổng tiền phòng
                        </span>
                        <span class="price-value">
                            {{ number_format($phong->gia_phong, 0, ',', '.') }} VND/tháng
                        </span>
                    </div>
                    @if($giaMoiSlot)
                    <div class="price-item">
                        <span class="price-label">
                            <i class="fas fa-user me-1"></i>
                            Giá tiền mỗi slot
                        </span>
                        <span class="price-value">
                            {{ number_format($giaMoiSlot, 0, ',', '.') }} VND/tháng
                        </span>
                    </div>
                    @endif
                </div>
                @endif

                @if($phong->ghi_chu || $phong->mo_ta)
                <div class="room-info-item mt-3">
                    <div class="room-info-label">Mô tả</div>
                    <div class="room-info-value">{{ $phong->ghi_chu ?? ($phong->mo_ta ?? 'Không có mô tả') }}</div>
                </div>
                @endif

                <a href="{{ route('client.phong') }}" class="room-view-btn mt-3">
                    <i class="fas fa-eye"></i>
                    Xem chi tiết phòng
                </a>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Sự cố gần đây -->
<div class="recent-issues-card">
    <div class="recent-issues-header">
        <h5>
            <i class="fas fa-tools"></i>
                    Sự cố bạn gửi gần đây
                </h5>
        <a href="{{ route('client.suco.index') }}" class="view-all-btn">
            Xem tất cả
            <i class="fas fa-arrow-right"></i>
                </a>
            </div>
    <div class="recent-issues-body">
                @if($suCoGanDay->count() > 0)
            <ul class="issues-list">
                                @foreach($suCoGanDay as $suCo)
                <li class="issue-item">
                    <div class="issue-header">
                        <div class="issue-date">
                            <i class="fas fa-calendar-alt me-1"></i>
                            {{ $suCo->ngay_gui ? \Illuminate\Support\Carbon::parse($suCo->ngay_gui)->format('d/m/Y') : 'N/A' }}
                        </div>
                        <span class="issue-status 
                            @if($suCo->trang_thai == 'Tiếp nhận') pending
                            @elseif($suCo->trang_thai == 'Đang xử lý') processing
                            @elseif($suCo->trang_thai == 'Hoàn thành') completed
                            @else pending
                            @endif">
                            {{ $suCo->trang_thai }}
                        </span>
                    </div>
                    <div class="issue-description">
                        {{ Str::limit($suCo->mo_ta, 100) }}
                    </div>
                    <div class="issue-room">
                        <i class="fas fa-door-open me-1"></i>
                        Phòng: {{ $suCo->phong->ten_phong ?? 'N/A' }}
                    </div>
                </li>
                @endforeach
            </ul>
                @else
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>Chưa có sự cố nào được báo cáo.</p>
            </div>
        @endif
    </div>
</div>
@endsection
