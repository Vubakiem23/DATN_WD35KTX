@extends('admin.layouts.admin')
@section('title', 'Báo Cáo Thống Kê')

@push('styles')
<style>
    /* Override admin-main padding for this page - remove all top spacing */
    .right_col, .right_col.admin-main {
        padding: 15px 20px 20px 20px !important;
        margin-top: 0 !important;
        overflow-x: hidden !important;
    }
    
    /* Page Header */
    .report-header {
        display: flex !important;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    /* Month Selector */
    .month-selector {
        display: flex;
        align-items: center;
        gap: 1rem;
        background: #fff;
        padding: 0.75rem 1.25rem;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .month-selector label {
        font-weight: 600;
        color: #374151;
        font-size: 0.9rem;
        white-space: nowrap;
    }

    .month-selector input[type="month"] {
        padding: 0.5rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 0.9rem;
        transition: border-color 0.2s;
    }

    .month-selector input[type="month"]:focus {
        outline: none;
        border-color: #6366f1;
    }

    /* Stats Overview */
    .stats-overview {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    @media (max-width: 1200px) {
        .stats-overview { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 576px) {
        .stats-overview { grid-template-columns: 1fr; }
    }

    .stat-box {
        background: #fff;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-box:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    .stat-box__icon {
        width: 60px;
        height: 60px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .stat-box--primary .stat-box__icon { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; }
    .stat-box--success .stat-box__icon { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff; }
    .stat-box--warning .stat-box__icon { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #fff; }
    .stat-box--danger .stat-box__icon { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: #fff; }
    .stat-box--info .stat-box__icon { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: #fff; }

    .stat-box__content { flex: 1; }

    .stat-box__value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1f2937;
        line-height: 1.2;
    }

    .stat-box__label {
        font-size: 0.85rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }

    .stat-box__sub {
        font-size: 0.75rem;
        color: #9ca3af;
        margin-top: 0.25rem;
    }

    /* Section Card */
    .section-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .section-card__header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-bottom: 1px solid #e5e7eb;
    }

    .section-card__title {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-card__title i {
        color: #6366f1;
    }

    .section-card__body {
        padding: 1.5rem;
    }

    /* Mini Stats Grid */
    .mini-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .mini-stat {
        position: relative;
        padding: 1.25rem;
        border-radius: 12px;
        overflow: hidden;
    }

    .mini-stat__value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #fff;
        line-height: 1.2;
    }

    .mini-stat__label {
        font-size: 0.8rem;
        color: rgba(255,255,255,0.85);
        margin-top: 0.25rem;
    }

    .mini-stat__sub {
        font-size: 0.7rem;
        color: rgba(255,255,255,0.7);
    }

    .mini-stat__icon {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 2.5rem;
        opacity: 0.2;
        color: #fff;
    }

    .mini-stat--primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .mini-stat--success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .mini-stat--warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .mini-stat--danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
    .mini-stat--info { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
    .mini-stat--dark { background: linear-gradient(135deg, #374151 0%, #1f2937 100%); }

    /* Progress Bar */
    .progress-mini {
        height: 6px;
        background: rgba(255,255,255,0.3);
        border-radius: 999px;
        margin-top: 0.75rem;
        overflow: hidden;
    }

    .progress-mini__bar {
        height: 100%;
        background: #fff;
        border-radius: 999px;
    }

    /* Chart Container */
    .chart-box {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .chart-box__header {
        padding: 1rem 1.5rem;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-bottom: 1px solid #e5e7eb;
    }

    .chart-box__title {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .chart-box__title i {
        color: #6366f1;
    }

    .chart-box__body {
        padding: 1.5rem;
    }

    /* Quick Access */
    .quick-access {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 1rem;
    }

    .quick-link {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1.5rem 1rem;
        background: #fff;
        border-radius: 12px;
        text-decoration: none;
        color: #374151;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        transition: all 0.2s;
        border: 2px solid transparent;
    }

    .quick-link:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        border-color: #6366f1;
        color: #6366f1;
        text-decoration: none;
    }

    .quick-link i {
        font-size: 2rem;
        margin-bottom: 0.75rem;
        color: #6366f1;
    }

    .quick-link span {
        font-size: 0.85rem;
        font-weight: 600;
        text-align: center;
    }

    /* Two Column Layout */
    .two-col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    @media (max-width: 992px) {
        .two-col { grid-template-columns: 1fr; }
    }

    /* Finance Summary */
    .finance-summary {
        background: #f9fafb;
        border-radius: 10px;
        padding: 1rem;
        margin-top: 1rem;
    }

    .finance-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        font-size: 0.9rem;
    }

    .finance-row--total {
        border-top: 2px solid #e5e7eb;
        margin-top: 0.5rem;
        padding-top: 0.75rem;
        font-weight: 700;
    }
</style>
@endpush

@section('content')
<div class="report-header" style="margin-bottom: 1rem;">
    <div>
        <h3 style="font-size: 1.5rem; font-weight: 700; color: #1f2937; margin: 0;">
            <i class="fa fa-bar-chart"></i> Báo Cáo Thống Kê
        </h3>
        <p style="color: #6b7280; font-size: 0.9rem; margin: 0.25rem 0 0 0;">Tổng quan hoạt động ký túc xá tháng {{ \Carbon\Carbon::parse($selectedMonth)->format('m/Y') }}</p>
    </div>
    <div class="month-selector">
        <form method="GET" action="{{ route('admin.index') }}" style="display: flex; align-items: center; gap: 10px;">
            <label for="month"><i class="fa fa-calendar"></i> Chọn tháng:</label>
            <input type="month" name="month" id="month" value="{{ $selectedMonth }}" onchange="this.form.submit()">
        </form>
    </div>
</div>

<!-- Stats Overview -->
<div class="stats-overview">
        <div class="stat-box stat-box--info">
            <div class="stat-box__icon"><i class="fa fa-users"></i></div>
            <div class="stat-box__content">
                <div class="stat-box__value">{{ number_format($soSlotCoNguoiO) }}/{{ number_format($tongSoSlot) }}</div>
                <div class="stat-box__label">Sinh viên đang ở</div>
                <div class="stat-box__sub">{{ $tongSoSlot > 0 ? round(($soSlotCoNguoiO / $tongSoSlot) * 100, 1) : 0 }}% công suất</div>
            </div>
        </div>

        <div class="stat-box stat-box--success">
            <div class="stat-box__icon"><i class="fa fa-money"></i></div>
            <div class="stat-box__content">
                <div class="stat-box__value">{{ number_format($tongTienThuDuoc / 1000000, 1) }}M</div>
                <div class="stat-box__label">Doanh thu tháng</div>
                <div class="stat-box__sub">{{ number_format($tongTienThuDuoc, 0, ',', '.') }} đ</div>
            </div>
        </div>

        <div class="stat-box stat-box--warning">
            <div class="stat-box__icon"><i class="fa fa-wrench"></i></div>
            <div class="stat-box__content">
                <div class="stat-box__value">{{ number_format($tongChiPhiBaoTriSuaChua / 1000000, 1) }}M</div>
                <div class="stat-box__label">Chi phí bảo trì</div>
                <div class="stat-box__sub">{{ number_format($tongChiPhiBaoTriSuaChua, 0, ',', '.') }} đ</div>
            </div>
        </div>

        <div class="stat-box stat-box--primary">
            <div class="stat-box__icon"><i class="fa fa-line-chart"></i></div>
            <div class="stat-box__content">
                @php $loiNhuan = $tongTienThuDuoc - $tongChiPhiBaoTriSuaChua; @endphp
                <div class="stat-box__value" style="color: {{ $loiNhuan >= 0 ? '#10b981' : '#ef4444' }}">
                    {{ number_format($loiNhuan / 1000000, 1) }}M
                </div>
                <div class="stat-box__label">Lợi nhuận ròng</div>
                <div class="stat-box__sub">Thu - Chi</div>
            </div>
        </div>
    </div>

    <!-- Sự cố & Tài chính -->
    <div class="two-col">
        <!-- Thống kê sự cố -->
        <div class="section-card">
            <div class="section-card__header">
                <h5 class="section-card__title"><i class="fa fa-exclamation-triangle"></i> Thống kê sự cố</h5>
                <span class="badge bg-light text-dark">Tháng {{ \Carbon\Carbon::parse($selectedMonth)->format('m/Y') }}</span>
            </div>
            <div class="section-card__body">
                <div class="mini-stats">
                    <div class="mini-stat mini-stat--danger">
                        <div class="mini-stat__value">{{ number_format($soSuCoPhatSinh) }}</div>
                        <div class="mini-stat__label">Sự cố phát sinh</div>
                        <div class="mini-stat__icon"><i class="fa fa-exclamation-circle"></i></div>
                    </div>
                    <div class="mini-stat mini-stat--success">
                        <div class="mini-stat__value">{{ number_format($soSuCoDaXuLy) }}</div>
                        <div class="mini-stat__label">Đã xử lý</div>
                        <div class="mini-stat__sub">Tỷ lệ: {{ $tyLeXuLySuCo }}%</div>
                        <div class="progress-mini">
                            <div class="progress-mini__bar" style="width: {{ $tyLeXuLySuCo }}%"></div>
                        </div>
                        <div class="mini-stat__icon"><i class="fa fa-check-circle"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê tài chính -->
        <div class="section-card">
            <div class="section-card__header">
                <h5 class="section-card__title"><i class="fa fa-money"></i> Chi tiết tài chính</h5>
            </div>
            <div class="section-card__body">
                <div class="finance-summary">
                    <div class="finance-row">
                        <span><i class="fa fa-home me-2 text-primary"></i>Tiền phòng</span>
                        <span class="text-success fw-bold">+{{ number_format($tongTienPhong, 0, ',', '.') }} đ</span>
                    </div>
                    <div class="finance-row">
                        <span><i class="fa fa-bolt me-2 text-warning"></i>Tiền điện nước</span>
                        <span class="text-success fw-bold">+{{ number_format($tongTienDienNuoc, 0, ',', '.') }} đ</span>
                    </div>
                    <div class="finance-row">
                        <span><i class="fa fa-exclamation-triangle me-2 text-danger"></i>Tiền vi phạm</span>
                        <span class="text-success fw-bold">+{{ number_format($tongTienViPham ?? 0, 0, ',', '.') }} đ</span>
                    </div>
                    <div class="finance-row">
                        <span><i class="fa fa-wrench me-2 text-info"></i>SV đền bù bảo trì</span>
                        <span class="text-success fw-bold">+{{ number_format($tongThuNhapBaoTriClient ?? 0, 0, ',', '.') }} đ</span>
                    </div>
                    <div class="finance-row">
                        <span><i class="fa fa-exclamation-circle me-2 text-info"></i>SV đền bù sự cố</span>
                        <span class="text-success fw-bold">+{{ number_format($tongThuNhapSuCoClient ?? 0, 0, ',', '.') }} đ</span>
                    </div>
                    <div class="finance-row">
                        <span><i class="fa fa-cogs me-2 text-secondary"></i>Chi phí bảo trì (KTX)</span>
                        <span class="text-danger fw-bold">-{{ number_format($tongChiPhiBaoTri ?? 0, 0, ',', '.') }} đ</span>
                    </div>
                    <div class="finance-row">
                        <span><i class="fa fa-ambulance me-2 text-secondary"></i>Chi phí sự cố (KTX)</span>
                        <span class="text-danger fw-bold">-{{ number_format($tongChiPhiSuCo ?? 0, 0, ',', '.') }} đ</span>
                    </div>
                    <div class="finance-row finance-row--total">
                        <span>Lợi nhuận ròng</span>
                        <span class="{{ $loiNhuan >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $loiNhuan >= 0 ? '+' : '' }}{{ number_format($loiNhuan, 0, ',', '.') }} đ
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hồ sơ sinh viên -->
    <div class="section-card">
        <div class="section-card__header">
            <h5 class="section-card__title"><i class="fa fa-file-text-o"></i> Thống kê hồ sơ sinh viên</h5>
        </div>
        <div class="section-card__body">
            <div class="mini-stats">
                <div class="mini-stat mini-stat--dark">
                    <div class="mini-stat__value">{{ number_format($tongHoSo) }}</div>
                    <div class="mini-stat__label">Tổng hồ sơ</div>
                    <div class="mini-stat__icon"><i class="fa fa-files-o"></i></div>
                </div>
                <div class="mini-stat mini-stat--success">
                    <div class="mini-stat__value">{{ number_format($daDuyet) }}</div>
                    <div class="mini-stat__label">Đã duyệt</div>
                    <div class="mini-stat__sub">{{ $tyLeDuyet }}%</div>
                    <div class="mini-stat__icon"><i class="fa fa-check-circle-o"></i></div>
                </div>
                <div class="mini-stat mini-stat--warning">
                    <div class="mini-stat__value">{{ number_format($choDuyet) }}</div>
                    <div class="mini-stat__label">Chờ duyệt</div>
                    <div class="mini-stat__icon"><i class="fa fa-clock-o"></i></div>
                </div>
                <div class="mini-stat mini-stat--info">
                    <div class="mini-stat__value">{{ number_format($choXacNhan) }}</div>
                    <div class="mini-stat__label">Chờ xác nhận</div>
                    <div class="mini-stat__icon"><i class="fa fa-hourglass-half"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bảo trì tài sản -->
    <div class="section-card">
        <div class="section-card__header">
            <h5 class="section-card__title"><i class="fa fa-wrench"></i> Thống kê bảo trì tài sản</h5>
        </div>
        <div class="section-card__body">
            <div class="mini-stats">
                <div class="mini-stat mini-stat--info">
                    <div class="mini-stat__value">{{ number_format($soTaiSanDaBaoTri) }}</div>
                    <div class="mini-stat__label">Đã bảo trì</div>
                    <div class="mini-stat__icon"><i class="fa fa-cog"></i></div>
                </div>
                <div class="mini-stat mini-stat--warning">
                    <div class="mini-stat__value">{{ number_format($soTaiSanDangBaoTri) }}</div>
                    <div class="mini-stat__label">Đang bảo trì</div>
                    <div class="mini-stat__icon"><i class="fa fa-spinner"></i></div>
                </div>
                <div class="mini-stat mini-stat--success">
                    <div class="mini-stat__value">{{ number_format($soTaiSanHoanThanhBaoTri) }}</div>
                    <div class="mini-stat__label">Hoàn thành</div>
                    <div class="mini-stat__sub">{{ $tyLeHoanThanhBaoTri }}%</div>
                    <div class="mini-stat__icon"><i class="fa fa-check-square-o"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ -->
    <div class="two-col">
        <div class="chart-box">
            <div class="chart-box__header">
                <h5 class="chart-box__title"><i class="fa fa-bar-chart"></i> Biểu đồ sự cố (12 tháng)</h5>
            </div>
            <div class="chart-box__body">
                <canvas id="suCoChart" height="120"></canvas>
            </div>
        </div>

        <div class="chart-box">
            <div class="chart-box__header">
                <h5 class="chart-box__title"><i class="fa fa-line-chart"></i> Biểu đồ thu chi (12 tháng)</h5>
            </div>
            <div class="chart-box__body">
                <canvas id="thuChiChart" height="120"></canvas>
            </div>
        </div>
    </div>

    <div class="two-col">
        <div class="chart-box">
            <div class="chart-box__header">
                <h5 class="chart-box__title"><i class="fa fa-pie-chart"></i> Phân bố slot</h5>
            </div>
            <div class="chart-box__body text-center">
                <canvas id="slotChart" height="150"></canvas>
            </div>
        </div>

        <!-- Truy cập nhanh -->
        <div class="section-card">
            <div class="section-card__header">
                <h5 class="section-card__title"><i class="fa fa-bolt"></i> Truy cập nhanh</h5>
            </div>
            <div class="section-card__body">
                <div class="quick-access">
                    <a href="{{ route('sinhvien.index') }}" class="quick-link">
                        <i class="fa fa-users"></i>
                        <span>Sinh viên</span>
                    </a>
                    <a href="{{ route('phong.index') }}" class="quick-link">
                        <i class="fa fa-bed"></i>
                        <span>Phòng</span>
                    </a>
                    <a href="{{ route('hoadon.index') }}" class="quick-link">
                        <i class="fa fa-file-text-o"></i>
                        <span>Hóa đơn</span>
                    </a>
                    <a href="{{ route('suco.index') }}" class="quick-link">
                        <i class="fa fa-wrench"></i>
                        <span>Sự cố</span>
                    </a>
                    <a href="{{ route('vipham.index') }}" class="quick-link">
                        <i class="fa fa-exclamation-triangle"></i>
                        <span>Vi phạm</span>
                    </a>
                    <a href="{{ route('taisan.index') }}" class="quick-link">
                        <i class="fa fa-cubes"></i>
                        <span>Tài sản</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Biểu đồ sự cố
    new Chart(document.getElementById('suCoChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($thongKeTheoThang, 'thang')) !!},
            datasets: [{
                label: 'Phát sinh',
                data: {!! json_encode(array_column($thongKeTheoThang, 'su_co_phat_sinh')) !!},
                backgroundColor: 'rgba(239, 68, 68, 0.7)',
                borderColor: '#ef4444',
                borderWidth: 1,
                borderRadius: 4
            }, {
                label: 'Đã xử lý',
                data: {!! json_encode(array_column($thongKeTheoThang, 'su_co_da_xu_ly')) !!},
                backgroundColor: 'rgba(16, 185, 129, 0.7)',
                borderColor: '#10b981',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    // Biểu đồ thu chi
    new Chart(document.getElementById('thuChiChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($thongKeTheoThang, 'thang')) !!},
            datasets: [{
                label: 'Tổng thu',
                data: {!! json_encode(array_column($thongKeTheoThang, 'tong_tien_thu')) !!},
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }, {
                label: 'Tổng chi',
                data: {!! json_encode(array_column($thongKeTheoThang, 'tong_chi_phi')) !!},
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            return ctx.dataset.label + ': ' + new Intl.NumberFormat('vi-VN').format(ctx.parsed.y) + ' đ';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return (value / 1000000).toFixed(1) + 'M';
                        }
                    }
                }
            }
        }
    });

    // Biểu đồ slot
    new Chart(document.getElementById('slotChart'), {
        type: 'doughnut',
        data: {
            labels: ['Có người ở', 'Còn trống'],
            datasets: [{
                data: [{{ $soSlotCoNguoiO }}, {{ $soSlotTrong }}],
                backgroundColor: ['#3b82f6', '#e5e7eb'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '60%',
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            const pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                            return ctx.label + ': ' + ctx.parsed + ' slot (' + pct + '%)';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
