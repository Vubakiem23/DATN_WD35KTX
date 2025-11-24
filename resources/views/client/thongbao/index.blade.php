@extends('client.layouts.app')

@section('title', 'Thông báo - Sinh viên')

@section('content')
<div class="container">

    @if($sinhVien && ($thongBaoSinhVien->count() > 0 ||
    $SuCo->count() > 0 ||
    $thongBaoPhongSv->count() > 0 ||
    $HoaDonSlotPayment->count() > 0 ||
    $HoaDonUtilitiesPayment->count() > 0))

    @php
    // Gom tất cả thông báo + gán thuộc tính time để sort
    $allNoti = collect()
    ->merge(
    $thongBaoSinhVien->map(function ($i) {
    $i->time = $i->created_at;
    $i->type = 'sv';
    return $i;
    })
    )
    ->merge(
    $SuCo->map(function ($i) {
    $i->time = $i->ngay_gui ? \Carbon\Carbon::parse($i->ngay_gui) : now();
    $i->type = 'suco';
    return $i;
    })
    )
    ->merge(
    $thongBaoPhongSv->map(function ($i) {
    $i->time = $i->created_at;
    $i->type = 'phong';
    return $i;
    })
    )
    // HÓA ĐƠN PHÒNG
    ->merge(
    $HoaDonSlotPayment->map(function($i) {
    $i->type = 'hoa_don_phong';
    $i->time = $i->created_at;
    return $i;
    })
    )

    // HÓA ĐƠN ĐIỆN NƯỚC
    ->merge(
    $HoaDonUtilitiesPayment->map(function($i) {
    $i->type = 'hoa_don_dien_nuoc';
    $i->time = $i->created_at;
    return $i;
    })
    )
    ->sortByDesc('time');
    @endphp

    <!-- Section Header -->
    <div class="notice-section-header mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-bell notice-section-icon"></i>
            <h2 class="notice-section-title mb-0">Thông báo của tôi</h2>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="notifications-container">

        {{-- ===== VÒNG LẶP DUY NHẤT ĐÃ SẮP XẾP ===== --}}
        @foreach($allNoti as $tb)

        {{-- ================== THÔNG BÁO SINH VIÊN ================== --}}
        @if($tb->type == 'sv')
        <div class="notice-card">
            <div class="notice-card-header">
                <div class="notice-status-badge">
                    @if($tb->trang_thai == 'Mới')
                    <i class="fas fa-bell notice-icon-new"></i>
                    <span class="notice-status-text">Mới</span>
                    @elseif($tb->trang_thai == 'Chờ duyệt')
                    <i class="fas fa-bell notice-icon-pending"></i>
                    <span class="notice-status-text">Chờ duyệt</span>
                    @else
                    <i class="fas fa-bell notice-icon-read"></i>
                    <span class="notice-status-text">{{ $tb->trang_thai }}</span>
                    @endif
                </div>
                <div class="notice-time">
                    <i class="far fa-clock"></i>
                    <span>{{ $tb->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
            <div class="notice-card-body">
                <p class="notice-content">{{ $tb->noi_dung }}</p>
            </div>
        </div>
        @endif

        {{-- ================== THÔNG BÁO SỰ CỐ ================== --}}
       @if($tb->type == 'suco')
<div class="notice-card">
    <div class="notice-card-header">
        <div class="notice-status-badge">
            <i class="fas fa-exclamation-triangle notice-icon-warning"></i>
            <span class="notice-status-text">Sự cố</span>

            {{-- Hiển thị phòng nếu có --}}
            @if($tb->phong)
                <span class="notice-room-badge">
                    <i class="fas fa-door-open"></i>
                    {{ $tb->phong->ten_phong }}
                </span>
            @endif

            {{-- Xác định class badge theo trạng thái --}}
            @php
                $trangThai = $tb->trang_thai ?? 'Chưa xử lý';
                $badgeClass = match($trangThai) {
                    'Tiếp nhận' => 'badge-soft-secondary',
                    'Đang xử lý' => 'badge-soft-warning',
                    'Hoàn thành' => 'badge-soft-success',
                    default => 'badge-soft-secondary',
                };
            @endphp

            <span class="badge {{ $badgeClass }}">{{ $trangThai }}</span>
        </div>

        {{-- Hiển thị thời gian --}}
        <div class="notice-time">
            <i class="far fa-clock"></i>
            @if($tb->trang_thai === 'Hoàn thành' && !empty($tb->ngay_hoan_thanh))
                <span>Hoàn thành: {{ \Carbon\Carbon::parse($tb->ngay_hoan_thanh)->format('d/m/Y H:i') }}</span>
            @else
                <span>{{ $tb->ngay_gui ? \Carbon\Carbon::parse($tb->ngay_gui)->format('d/m/Y H:i') : 'N/A' }}</span>
            @endif
        </div>
    </div>

    <div class="notice-card-body">
        <p class="notice-content">{{ $tb->mo_ta }}</p>
    </div>
</div>
@endif


        {{-- ================== THÔNG BÁO PHÒNG ================== --}}
        @if($tb->type == 'phong')
        <div class="notice-card">
            <div class="notice-card-header">
                <div class="notice-status-badge">
                    <i class="fas fa-home notice-icon-room"></i>
                    <span class="notice-status-text">Phòng</span>

                    @if($tb->phong)
                    <span class="notice-room-badge">
                        <i class="fas fa-door-open"></i>
                        {{ $tb->phong->ten_phong }}
                    </span>
                    @endif
                </div>

                <div class="notice-time">
                    <i class="far fa-clock"></i>
                    <span>{{ $tb->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
            <div class="notice-card-body">
                <p class="notice-content">{{ $tb->noi_dung }}</p>
            </div>
        </div>
        @endif
       {{-- ==================== 4. HÓA ĐƠN PHÒNG ==================== --}}
@if($tb->type == 'hoa_don_phong')
<a href="{{ url('client/hoadon/tien-phong') }}" style="text-decoration: none; color: inherit;">
    <div class="notice-card">
        <div class="notice-card-header">
            <div class="notice-status-badge">
                <i class="fas fa-file-invoice-dollar text-primary"></i>
                <span class="notice-status-text">Hóa đơn phòng</span>

                @if($tb->slot && $tb->slot->phong)
                <span class="notice-room-badge">
                    <i class="fas fa-door-open"></i>
                    {{ $tb->slot->phong->ten_phong }}
                </span>
                @endif
            </div>

            <div class="notice-time">
                <i class="far fa-clock"></i>
                <span>{{ $tb->created_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>

        <div class="notice-card-body">
            <p class="notice-content">
                Số tiền: <b>{{ number_format($tb->hoaDon->slot_unit_price) }}đ</b><br>
                Trạng thái: <b>{{ $tb->trang_thai }}</b>
            </p>
        </div>
    </div>
</a>
@endif
{{-- ==================== HÓA ĐƠN ĐIỆN NƯỚC ==================== --}}
@if($tb->type == 'hoa_don_dien_nuoc')
<a href="{{ url('client/hoadon/dien-nuoc') }}" style="text-decoration: none; color: inherit;">
    <div class="notice-card">
        <div class="notice-card-header">
            <div class="notice-status-badge">
                <i class="fas fa-file-invoice text-warning"></i>
                <span class="notice-status-text">Hóa đơn điện nước</span>

                {{-- Hiển thị phòng nếu có --}}
                @if($tb->slot && $tb->slot->phong)
                <span class="notice-room-badge">
                    <i class="fas fa-door-open"></i>
                    {{ $tb->slot->phong->ten_phong }}
                </span>
                @endif

            </div>

            <div class="notice-time">
                <i class="far fa-clock"></i>
                <span>{{ $tb->created_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>

        <div class="notice-card-body">
            <p class="notice-content">
                {{-- Hiển thị số tiền điện, nước và tổng tiền --}}
                Tiền điện: <b>{{ number_format($tb->tien_dien) }}đ</b><br>
                Tiền nước: <b>{{ number_format($tb->tien_nuoc) }}đ</b><br>
                Tổng tiền: <b>{{ number_format($tb->tong_tien) }}đ</b><br>
                Trạng thái: <b>{{ $tb->trang_thai }}</b>
            </p>
        </div>
    </div>
</a>
@endif


        @endforeach
    </div>

    @else
    <!-- Empty State -->
    <div class="notice-empty-state">
        <div class="notice-empty-icon">
            <i class="fas fa-bell-slash"></i>
        </div>
        <h3 class="notice-empty-title">Chưa có thông báo nào</h3>
        <p class="notice-empty-text">Bạn chưa có thông báo riêng nào. Các thông báo chung sẽ được hiển thị ở trang chủ.</p>
    </div>
    @endif
</div>

@push('styles')
<style>
    /* ===== HERO BANNER ===== */
    .notice-hero-banner {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 0;
        margin: -16px -15px 0 -15px;
        position: relative;
        overflow: hidden;
    }

    .notice-hero-banner::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.2), transparent 70%);
        pointer-events: none;
    }

    .notice-hero-icon {
        font-size: 48px;
        color: rgba(255, 255, 255, 0.95);
        margin-right: 16px;
        filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.2));
    }

    .notice-hero-title {
        font-size: 36px;
        font-weight: 700;
        color: #fff;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    /* ===== SECTION HEADER ===== */
    .notice-section-header {
        background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
        padding: 16px 24px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(29, 78, 216, 0.2);
    }

    .notice-section-icon {
        font-size: 24px;
        color: #fff;
        margin-right: 12px;
    }

    .notice-section-title {
        font-size: 20px;
        font-weight: 700;
        color: #fff;
    }

    /* ===== NOTIFICATIONS CONTAINER ===== */
    .notifications-container {
        display: flex;
        flex-direction: column;
        gap: 16px;
        margin-bottom: 40px;
    }

    /* ===== NOTICE CARD ===== */
    .notice-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .notice-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        border-color: #d1d5db;
    }

    .notice-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .notice-status-badge {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .notice-icon-new {
        color: #ef4444;
        font-size: 18px;
    }

    .notice-icon-pending {
        color: #f59e0b;
        font-size: 18px;
    }

    .notice-icon-read {
        color: #10b981;
        font-size: 18px;
    }

    .notice-icon-warning {
        color: #f59e0b;
        font-size: 18px;
    }

    .notice-icon-room {
        color: #3b82f6;
        font-size: 18px;
    }

    .notice-status-text {
        font-weight: 600;
        font-size: 14px;
        color: #111827;
    }

    .notice-room-badge {
        background: #e0e7ff;
        color: #4338ca;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .notice-room-badge i {
        font-size: 11px;
    }

    .notice-time {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #6b7280;
        font-size: 13px;
    }

    .notice-time i {
        font-size: 12px;
    }

    .notice-card-body {
        margin-top: 8px;
    }

    .notice-title {
        font-size: 16px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 8px;
    }

    .notice-content {
        font-size: 15px;
        color: #4b5563;
        line-height: 1.6;
        margin: 0;
    }

    /* ===== EMPTY STATE ===== */
    .notice-empty-state {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 60px 40px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .notice-empty-icon {
        width: 80px;
        height: 80px;
        background: #f3f4f6;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }

    .notice-empty-icon i {
        font-size: 36px;
        color: #9ca3af;
    }

    .notice-empty-title {
        font-size: 20px;
        font-weight: 700;
        color: #374151;
        margin-bottom: 12px;
    }

    .notice-empty-text {
        font-size: 15px;
        color: #6b7280;
        margin: 0;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .notice-hero-icon {
            font-size: 36px;
            margin-right: 12px;
        }

        .notice-hero-title {
            font-size: 28px;
        }

        .notice-section-title {
            font-size: 18px;
        }

        .notice-card {
            padding: 16px;
        }

        .notice-card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .notice-empty-state {
            padding: 40px 20px;
        }
    }

    .notice-status-badge .badge {
        color: #111827;
        /* chữ màu đen */
    }
</style>
@endpush
@endsection
