@extends('admin.layouts.admin')

@section('title', 'Báo cáo thống kê')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <div>
            <h3 class="page-title mb-1">
                <i class="fa fa-bar-chart me-2"></i>Báo cáo - Thống kê
            </h3>
            <p class="text-muted mb-0">Tổng quan hoạt động ký túc xá tháng {{ $month }}/{{ $year }}</p>
        </div>
        <div class="d-flex gap-2 align-items-end flex-wrap">
            <form method="GET" action="{{ route('dashboard.index') }}" class="d-flex gap-2 align-items-end">
                <div>
                    <label class="form-label small mb-1">Tháng</label>
                    <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                                Tháng {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="form-label small mb-1">Năm</label>
                    <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                        @for($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
            </form>
            <a href="{{ route('dashboard.export', ['month' => $month, 'year' => $year]) }}" 
               class="btn btn-success btn-sm">
                <i class="fa fa-file-excel-o me-1"></i> Xuất Excel
            </a>
        </div>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="row g-3 mb-4">
        <!-- Tổng sinh viên -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card--primary">
                <div class="stat-card__icon">
                    <i class="fa fa-users"></i>
                </div>
                <div class="stat-card__content">
                    <div class="stat-card__value">{{ number_format($stats['slot_dang_co_nguoi_o']) }}</div>
                    <div class="stat-card__label">Sinh viên đang ở</div>
                    <div class="stat-card__sub">{{ $stats['phan_tram_slot_co_nguoi'] }}% công suất</div>
                </div>
            </div>
        </div>

        <!-- Slot trống -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card--warning">
                <div class="stat-card__icon">
                    <i class="fa fa-bed"></i>
                </div>
                <div class="stat-card__content">
                    <div class="stat-card__value">{{ number_format($stats['slot_dang_trong']) }}</div>
                    <div class="stat-card__label">Slot còn trống</div>
                    <div class="stat-card__sub">Tổng {{ number_format($stats['tong_slot']) }} slot</div>
                </div>
            </div>
        </div>

        <!-- Doanh thu -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card--success">
                <div class="stat-card__icon">
                    <i class="fa fa-money"></i>
                </div>
                <div class="stat-card__content">
                    <div class="stat-card__value">{{ number_format($stats['tong_tien_thu_duoc'] / 1000000, 1) }}M</div>
                    <div class="stat-card__label">Doanh thu tháng</div>
                    <div class="stat-card__sub">{{ number_format($stats['tong_tien_thu_duoc'], 0, ',', '.') }} đ</div>
                </div>
            </div>
        </div>

        <!-- Lợi nhuận -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card--info">
                <div class="stat-card__icon">
                    <i class="fa fa-line-chart"></i>
                </div>
                <div class="stat-card__content">
                    @php $loiNhuan = $stats['tong_tien_thu_duoc'] - $stats['tong_chi_phi_bao_tri']; @endphp
                    <div class="stat-card__value">{{ number_format($loiNhuan / 1000000, 1) }}M</div>
                    <div class="stat-card__label">Lợi nhuận ròng</div>
                    <div class="stat-card__sub">Thu - Chi bảo trì</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section: Sự cố & Tài chính -->
    <div class="row g-4 mb-4">
        <!-- Thống kê sự cố -->
        <div class="col-lg-6">
            <div class="card-section">
                <div class="card-section__header">
                    <h5 class="card-section__title">
                        <i class="fa fa-exclamation-triangle text-danger me-2"></i>Thống kê sự cố
                    </h5>
                    <span class="badge bg-light text-dark">Tháng {{ $month }}/{{ $year }}</span>
                </div>
                <div class="card-section__body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="mini-stat mini-stat--danger">
                                <div class="mini-stat__value">{{ number_format($stats['so_su_co_phat_sinh']) }}</div>
                                <div class="mini-stat__label">Sự cố phát sinh</div>
                                <div class="mini-stat__icon"><i class="fa fa-exclamation-circle"></i></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mini-stat mini-stat--success">
                                <div class="mini-stat__value">{{ number_format($stats['so_su_co_da_xu_ly']) }}</div>
                                <div class="mini-stat__label">Đã xử lý</div>
                                <div class="mini-stat__icon"><i class="fa fa-check-circle"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="progress-section mt-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small text-muted">Tỷ lệ xử lý</span>
                            <span class="small fw-bold">{{ $stats['ty_le_xu_ly'] }}%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ $stats['ty_le_xu_ly'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê tài chính -->
        <div class="col-lg-6">
            <div class="card-section">
                <div class="card-section__header">
                    <h5 class="card-section__title">
                        <i class="fa fa-money text-success me-2"></i>Thống kê tài chính
                    </h5>
                    <span class="badge bg-light text-dark">Tháng {{ $month }}/{{ $year }}</span>
                </div>
                <div class="card-section__body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="mini-stat mini-stat--info">
                                <div class="mini-stat__value">{{ number_format($stats['tong_tien_thu_duoc'] / 1000000, 1) }}M</div>
                                <div class="mini-stat__label">Tổng thu</div>
                                <div class="mini-stat__icon"><i class="fa fa-arrow-up"></i></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mini-stat mini-stat--warning">
                                <div class="mini-stat__value">{{ number_format($stats['tong_chi_phi_bao_tri'] / 1000000, 1) }}M</div>
                                <div class="mini-stat__label">Chi bảo trì</div>
                                <div class="mini-stat__icon"><i class="fa fa-arrow-down"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="finance-detail mt-3">
                        <div class="finance-item">
                            <span>Tiền phòng + Điện + Nước</span>
                            <span class="text-success fw-bold">+{{ number_format($stats['tong_tien_thu_duoc'], 0, ',', '.') }} đ</span>
                        </div>
                        <div class="finance-item">
                            <span>Chi phí sửa chữa, bảo trì</span>
                            <span class="text-danger fw-bold">-{{ number_format($stats['tong_chi_phi_bao_tri'], 0, ',', '.') }} đ</span>
                        </div>
                        <hr class="my-2">
                        <div class="finance-item">
                            <span class="fw-bold">Lợi nhuận ròng</span>
                            <span class="fw-bold {{ $loiNhuan >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $loiNhuan >= 0 ? '+' : '' }}{{ number_format($loiNhuan, 0, ',', '.') }} đ
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section: Hồ sơ sinh viên -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card-section">
                <div class="card-section__header">
                    <h5 class="card-section__title">
                        <i class="fa fa-file-text-o text-primary me-2"></i>Thống kê hồ sơ sinh viên
                    </h5>
                </div>
                <div class="card-section__body">
                    <div class="row g-3">
                        <div class="col-lg-3 col-md-6">
                            <div class="mini-stat mini-stat--dark">
                                <div class="mini-stat__value">{{ number_format($stats['tong_ho_so']) }}</div>
                                <div class="mini-stat__label">Tổng hồ sơ</div>
                                <div class="mini-stat__icon"><i class="fa fa-files-o"></i></div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="mini-stat mini-stat--success">
                                <div class="mini-stat__value">{{ number_format($stats['ho_so_da_duyet']) }}</div>
                                <div class="mini-stat__label">Đã duyệt</div>
                                <div class="mini-stat__sub">{{ $stats['ty_le_duyet'] }}%</div>
                                <div class="mini-stat__icon"><i class="fa fa-check-circle-o"></i></div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="mini-stat mini-stat--warning">
                                <div class="mini-stat__value">{{ number_format($stats['ho_so_cho_duyet']) }}</div>
                                <div class="mini-stat__label">Chờ duyệt</div>
                                <div class="mini-stat__icon"><i class="fa fa-clock-o"></i></div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="mini-stat mini-stat--info">
                                <div class="mini-stat__value">{{ number_format($stats['ho_so_cho_xac_nhan']) }}</div>
                                <div class="mini-stat__label">Chờ xác nhận</div>
                                <div class="mini-stat__icon"><i class="fa fa-hourglass-half"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ -->
    <div class="row g-4 mb-4">
        <!-- Biểu đồ sự cố -->
        <div class="col-lg-6">
            <div class="card-section">
                <div class="card-section__header">
                    <h5 class="card-section__title">
                        <i class="fa fa-line-chart me-2"></i>Biểu đồ sự cố (12 tháng)
                    </h5>
                </div>
                <div class="card-section__body">
                    <canvas id="suCoChart" height="120"></canvas>
                </div>
            </div>
        </div>

        <!-- Biểu đồ tài chính -->
        <div class="col-lg-6">
            <div class="card-section">
                <div class="card-section__header">
                    <h5 class="card-section__title">
                        <i class="fa fa-area-chart me-2"></i>Biểu đồ doanh thu (12 tháng)
                    </h5>
                </div>
                <div class="card-section__body">
                    <canvas id="taiChinhChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Biểu đồ slot -->
        <div class="col-lg-4">
            <div class="card-section">
                <div class="card-section__header">
                    <h5 class="card-section__title">
                        <i class="fa fa-pie-chart me-2"></i>Phân bố slot
                    </h5>
                </div>
                <div class="card-section__body text-center">
                    <canvas id="slotChart" height="180"></canvas>
                </div>
            </div>
        </div>

        <!-- Biểu đồ thu chi -->
        <div class="col-lg-8">
            <div class="card-section">
                <div class="card-section__header">
                    <h5 class="card-section__title">
                        <i class="fa fa-bar-chart me-2"></i>So sánh Thu - Chi (12 tháng)
                    </h5>
                </div>
                <div class="card-section__body">
                    <canvas id="thuChiChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1f2937;
    }

    /* Stat Cards */
    .stat-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.08);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12);
    }

    .stat-card__icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        flex-shrink: 0;
    }

    .stat-card--primary .stat-card__icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
    }

    .stat-card--success .stat-card__icon {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #fff;
    }

    .stat-card--warning .stat-card__icon {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #fff;
    }

    .stat-card--info .stat-card__icon {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: #fff;
    }

    .stat-card__value {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        line-height: 1.2;
    }

    .stat-card__label {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
    }

    .stat-card__sub {
        font-size: 0.75rem;
        color: #9ca3af;
        margin-top: 0.25rem;
    }

    /* Card Section */
    .card-section {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.08);
        overflow: hidden;
        height: 100%;
    }

    .card-section__header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f3f4f6;
        background: #fafbfc;
    }

    .card-section__title {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }

    .card-section__body {
        padding: 1.25rem;
    }

    /* Mini Stat */
    .mini-stat {
        position: relative;
        padding: 1.25rem;
        border-radius: 12px;
        overflow: hidden;
        height: 100%;
    }

    .mini-stat__value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #fff;
        line-height: 1.2;
    }

    .mini-stat__label {
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.85);
        margin-top: 0.25rem;
    }

    .mini-stat__sub {
        font-size: 0.7rem;
        color: rgba(255, 255, 255, 0.7);
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

    .mini-stat--danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .mini-stat--success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .mini-stat--warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .mini-stat--info {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }

    .mini-stat--dark {
        background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
    }

    .mini-stat--primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    /* Finance Detail */
    .finance-detail {
        background: #f9fafb;
        border-radius: 10px;
        padding: 1rem;
    }

    .finance-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        font-size: 0.875rem;
    }

    /* Progress */
    .progress {
        border-radius: 999px;
        background: #e5e7eb;
    }

    .progress-bar {
        border-radius: 999px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .stat-card {
            padding: 1rem;
        }

        .stat-card__icon {
            width: 50px;
            height: 50px;
            font-size: 1.25rem;
        }

        .stat-card__value {
            font-size: 1.5rem;
        }

        .mini-stat__value {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart === 'undefined') {
        console.error('Chart.js chưa được load!');
        return;
    }

    const monthlyData = @json($monthlyData);
    const stats = @json($stats);
    const isChartJSv3 = typeof Chart.defaults !== 'undefined' && Chart.defaults.plugins;

    // Biểu đồ sự cố
    const suCoCtx = document.getElementById('suCoChart');
    if (suCoCtx) {
        new Chart(suCoCtx, {
            type: 'line',
            data: {
                labels: monthlyData.map(d => d.thang_label),
                datasets: [{
                    label: 'Sự cố phát sinh',
                    data: monthlyData.map(d => d.su_co_phat_sinh),
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2
                }, {
                    label: 'Đã xử lý',
                    data: monthlyData.map(d => d.su_co_da_xu_ly),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: isChartJSv3 ? {
                    y: { min: 0, ticks: { stepSize: 1 } }
                } : {
                    yAxes: [{ ticks: { beginAtZero: true, stepSize: 1 } }]
                }
            }
        });
    }

    // Biểu đồ tài chính
    const taiChinhCtx = document.getElementById('taiChinhChart');
    if (taiChinhCtx) {
        new Chart(taiChinhCtx, {
            type: 'line',
            data: {
                labels: monthlyData.map(d => d.thang_label),
                datasets: [{
                    label: 'Doanh thu (VND)',
                    data: monthlyData.map(d => d.tien_thu_duoc),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + 
                                    new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' đ';
                            }
                        }
                    }
                },
                scales: isChartJSv3 ? {
                    y: {
                        min: 0,
                        ticks: {
                            callback: function(value) {
                                return (value / 1000000).toFixed(1) + 'M';
                            }
                        }
                    }
                } : {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            callback: function(value) {
                                return (value / 1000000).toFixed(1) + 'M';
                            }
                        }
                    }]
                }
            }
        });
    }

    // Biểu đồ slot (Doughnut)
    const slotCtx = document.getElementById('slotChart');
    if (slotCtx) {
        new Chart(slotCtx, {
            type: 'doughnut',
            data: {
                labels: ['Đang có người ở', 'Còn trống'],
                datasets: [{
                    data: [stats.slot_dang_co_nguoi_o || 0, stats.slot_dang_trong || 0],
                    backgroundColor: ['#3b82f6', '#e5e7eb'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '65%',
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    // Biểu đồ thu chi
    const thuChiCtx = document.getElementById('thuChiChart');
    if (thuChiCtx) {
        new Chart(thuChiCtx, {
            type: 'bar',
            data: {
                labels: monthlyData.map(d => d.thang_label),
                datasets: [{
                    label: 'Doanh thu',
                    data: monthlyData.map(d => d.tien_thu_duoc),
                    backgroundColor: 'rgba(16, 185, 129, 0.7)',
                    borderColor: '#10b981',
                    borderWidth: 1,
                    borderRadius: 4
                }, {
                    label: 'Chi phí bảo trì',
                    data: monthlyData.map(d => d.chi_phi_bao_tri),
                    backgroundColor: 'rgba(245, 158, 11, 0.7)',
                    borderColor: '#f59e0b',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + 
                                    new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' đ';
                            }
                        }
                    }
                },
                scales: isChartJSv3 ? {
                    y: {
                        min: 0,
                        ticks: {
                            callback: function(value) {
                                return (value / 1000000).toFixed(1) + 'M';
                            }
                        }
                    }
                } : {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            callback: function(value) {
                                return (value / 1000000).toFixed(1) + 'M';
                            }
                        }
                    }]
                }
            }
        });
    }
});
</script>
@endpush
@endsection
