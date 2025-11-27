@extends('client.layouts.app')

@section('title', 'Thông báo - Sinh viên')

@section('content')
<div class="container">

    @php
        $limit = 4; // Số lượng thông báo hiển thị ban đầu
    @endphp

    <!-- Section Header -->
    <div class="notice-section-header mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-bell notice-section-icon"></i>
            <h2 class="notice-section-title mb-0">Thông báo của tôi</h2>
        </div>
    </div>

    <div class="notifications-container">

        {{-- ================= SINH VIÊN ================= --}}
        <h4 class="mb-2 mt-3">🔔 Thông báo sinh viên</h4>
        <div id="noti-sinhvien-container">
            @foreach($thongBaoSinhVien->sortByDesc('created_at')->take($limit) as $tb)
                <div class="notice-card">
                    <div class="notice-card-header">
                        <div class="notice-status-badge">
                            @if($tb->trang_thai == 'Mới')
                                <i class="fas fa-bell notice-icon-new"></i>
                            @elseif($tb->trang_thai == 'Chờ duyệt')
                                <i class="fas fa-bell notice-icon-pending"></i>
                            @else
                                <i class="fas fa-bell notice-icon-read"></i>
                            @endif
                            <span class="notice-status-text">{{ $tb->trang_thai }}</span>
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
            @endforeach
        </div>
        @if($thongBaoSinhVien->count() > $limit)
    <div class="text-center mt-2">
        <button class="btn btn-outline-primary load-more-btn" 
                data-type="sinhvien" 
                data-offset="{{ $limit }}">
            Xem thêm
        </button>
    </div>
@endif


        {{-- ================= SỰ CỐ ================= --}}
        <h4 class="mb-2 mt-4">⚠️ Thông báo sự cố</h4>
        <div id="noti-suco-container">
            @foreach($SuCo->sortByDesc(fn($i) => $i->ngay_gui ?? now())->take($limit) as $tb)
            <a href="{{ url('client/suco') }}" style="text-decoration: none; color: inherit;">
                <div class="notice-card">
                    <div class="notice-card-header">
                        <div class="notice-status-badge">
                            <i class="fas fa-exclamation-triangle notice-icon-warning"></i>
                            <span class="notice-status-text">Sự cố</span>
                            @if($tb->phong)
                                <span class="notice-room-badge">
                                    <i class="fas fa-door-open"></i>
                                    {{ $tb->phong->ten_phong }}
                                </span>
                            @endif
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
                </a>
            @endforeach
        </div>
        @if($SuCo->count() > $limit)
            <div class="text-center mt-2">
                <button class="btn btn-outline-primary load-more-btn" data-type="suco" data-offset="{{ $limit }}">Xem thêm</button>
            </div>
        @endif

        {{-- ================= HÓA ĐƠN PHÒNG ================= --}}
        <h4 class="mb-2 mt-4">💰 Hóa đơn phòng</h4>
        <div id="noti-slot-container">
            @foreach($HoaDonSlotPayment->sortByDesc('created_at')->take($limit) as $tb)
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
                                Số tiền: <b>{{ number_format($tb->hoaDon->slot_unit_price ?? 0) }}đ</b><br>
                                Trạng thái: <b>{{ $tb->trang_thai }}</b>
                            </p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        @if($HoaDonSlotPayment->count() > $limit)
            <div class="text-center mt-2">
                <button class="btn btn-outline-primary load-more-btn" data-type="slot" data-offset="{{ $limit }}">Xem thêm</button>
            </div>
        @endif

        {{-- ================= HÓA ĐƠN ĐIỆN NƯỚC ================= --}}
        <h4 class="mb-2 mt-4">🌊 Hóa đơn điện nước</h4>
        <div id="noti-utilities-container">
            @foreach($HoaDonUtilitiesPayment->sortByDesc('created_at')->take($limit) as $tb)
                <a href="{{ url('client/hoadon/dien-nuoc') }}" style="text-decoration: none; color: inherit;">
                    <div class="notice-card">
                        <div class="notice-card-header">
                            <div class="notice-status-badge">
                                <i class="fas fa-file-invoice text-warning"></i>
                                <span class="notice-status-text">Hóa đơn điện nước</span>
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
                                Tiền điện: <b>{{ number_format($tb->tien_dien ?? 0) }}đ</b><br>
                                Tiền nước: <b>{{ number_format($tb->tien_nuoc ?? 0) }}đ</b><br>
                                Tổng tiền: <b>{{ number_format($tb->tong_tien ?? 0) }}đ</b><br>
                                Trạng thái: <b>{{ $tb->trang_thai }}</b>
                            </p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        @if($HoaDonUtilitiesPayment->count() > $limit)
            <div class="text-center mt-2">
                <button class="btn btn-outline-primary load-more-btn" data-type="utilities" data-offset="{{ $limit }}">Xem thêm</button>
            </div>
        @endif

    </div>

    {{-- EMPTY STATE --}}
    @if($thongBaoSinhVien->count() == 0 && $SuCo->count() == 0 && $thongBaoPhongSv->count() == 0 && $HoaDonSlotPayment->count() == 0 && $HoaDonUtilitiesPayment->count() == 0)
        <div class="notice-empty-state">
            <div class="notice-empty-icon">
                <i class="fas fa-bell-slash"></i>
            </div>
            <h3 class="notice-empty-title">Chưa có thông báo nào</h3>
            <p class="notice-empty-text">Bạn chưa có thông báo riêng nào.</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.load-more-btn');

    buttons.forEach(btn => {
        btn.addEventListener('click', function() {
            const type = this.dataset.type;
            let offset = parseInt(this.dataset.offset);
            const limit = 4;
            const container = document.getElementById(`noti-${type}-container`);

            fetch(`{{ url('client/thongbao/load-more') }}?type=${type}&offset=${offset}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.length > 0) {
                    data.forEach(item => {
                        let html = '';

                        switch (type) {
                            case 'sinhvien':
                                html = `
                                <div class="notice-card">
                                    <div class="notice-card-header">
                                        <div class="notice-status-badge">
                                            <i class="fas fa-bell ${item.trang_thai == 'Mới' ? 'notice-icon-new' : 'notice-icon-read'}"></i>
                                            <span class="notice-status-text">${item.trang_thai}</span>
                                        </div>
                                        <div class="notice-time">
                                            <i class="far fa-clock"></i>
                                            <span>${new Date(item.created_at).toLocaleString('vi-VN')}</span>
                                        </div>
                                    </div>
                                    <div class="notice-card-body">
                                        <p class="notice-content">${item.noi_dung}</p>
                                    </div>
                                </div>`;
                                break;

                            case 'suco':
                                const badgeClass = {
                                    'Tiếp nhận': 'badge-soft-secondary',
                                    'Đang xử lý': 'badge-soft-warning',
                                    'Hoàn thành': 'badge-soft-success'
                                }[item.trang_thai] || 'badge-soft-secondary';

                                html = `
                                <div class="notice-card">
                                    <div class="notice-card-header">
                                        <div class="notice-status-badge">
                                            <i class="fas fa-exclamation-triangle notice-icon-warning"></i>
                                            <span class="notice-status-text">Sự cố</span>
                                            ${item.su_co && item.su_co.phong ? 
                                                `<span class="notice-room-badge"><i class="fas fa-door-open"></i>${item.su_co.phong.ten_phong}</span>` 
                                                : ''
                                            }
                                            <span class="badge ${badgeClass}">${item.trang_thai}</span>
                                        </div>
                                        <div class="notice-time">
                                            <i class="far fa-clock"></i>
                                            <span>${new Date(item.ngay_tao).toLocaleString('vi-VN')}</span>
                                        </div>
                                    </div>
                                    <div class="notice-card-body">
                                        <p class="notice-content">${item.mo_ta}</p>
                                    </div>
                                </div>`;
                                break;

                            case 'phong':
                                html = `
                                <div class="notice-card">
                                    <div class="notice-card-header">
                                        <div class="notice-status-badge">
                                            <i class="fas fa-home notice-icon-room"></i>
                                            <span class="notice-status-text">Phòng</span>
                                            ${item.phong ? 
                                                `<span class="notice-room-badge"><i class="fas fa-door-open"></i>${item.phong.ten_phong}</span>` 
                                                : ''
                                            }
                                        </div>
                                        <div class="notice-time">
                                            <i class="far fa-clock"></i>
                                            <span>${new Date(item.created_at).toLocaleString('vi-VN')}</span>
                                        </div>
                                    </div>
                                    <div class="notice-card-body">
                                        <p class="notice-content">${item.noi_dung}</p>
                                    </div>
                                </div>`;
                                break;

                            case 'slot':
                                html = `
                                <a href="{{ url('client/hoadon/tien-phong') }}" style="text-decoration: none; color: inherit;">
                                    <div class="notice-card">
                                        <div class="notice-card-header">
                                            <div class="notice-status-badge">
                                                <i class="fas fa-file-invoice-dollar text-primary"></i>
                                                <span class="notice-status-text">Hóa đơn phòng</span>
                                                ${item.slot && item.slot.phong ? 
                                                    `<span class="notice-room-badge"><i class="fas fa-door-open"></i>${item.slot.phong.ten_phong}</span>` 
                                                    : ''
                                                }
                                            </div>
                                            <div class="notice-time">
                                                <i class="far fa-clock"></i>
                                                <span>${new Date(item.created_at).toLocaleString('vi-VN')}</span>
                                            </div>
                                        </div>
                                        <div class="notice-card-body">
                                            <p class="notice-content">
                                                Số tiền: <b>${new Intl.NumberFormat('vi-VN').format(item.slot_unit_price ?? 0)}đ</b><br>
                                                Trạng thái: <b>${item.trang_thai}</b>
                                            </p>
                                        </div>
                                    </div>
                                </a>`;
                                break;

                            case 'utilities':
                                html = `
                                <a href="{{ url('client/hoadon/dien-nuoc') }}" style="text-decoration: none; color: inherit;">
                                    <div class="notice-card">
                                        <div class="notice-card-header">
                                            <div class="notice-status-badge">
                                                <i class="fas fa-file-invoice text-warning"></i>
                                                <span class="notice-status-text">Hóa đơn điện nước</span>
                                                ${item.slot && item.slot.phong ? 
                                                    `<span class="notice-room-badge"><i class="fas fa-door-open"></i>${item.slot.phong.ten_phong}</span>` 
                                                    : ''
                                                }
                                            </div>
                                            <div class="notice-time">
                                                <i class="far fa-clock"></i>
                                                <span>${new Date(item.created_at).toLocaleString('vi-VN')}</span>
                                            </div>
                                        </div>
                                        <div class="notice-card-body">
                                            <p class="notice-content">
                                                Tiền điện: <b>${new Intl.NumberFormat('vi-VN').format(item.tien_dien ?? 0)}đ</b><br>
                                                Tiền nước: <b>${new Intl.NumberFormat('vi-VN').format(item.tien_nuoc ?? 0)}đ</b><br>
                                                Tổng tiền: <b>${new Intl.NumberFormat('vi-VN').format(item.tong_tien ?? 0)}đ</b><br>
                                                Trạng thái: <b>${item.trang_thai}</b>
                                            </p>
                                        </div>
                                    </div>
                                </a>`;
                                break;
                        }

                        container.insertAdjacentHTML('beforeend', html);
                    });

                    offset += limit;
                    this.dataset.offset = offset;

                    // Không ẩn nút nữa, luôn giữ hiện
                }
            });
        });
    });
});


</script>

@endpush

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
