@extends('admin.layouts.admin')

@section('content')
<div class="x_panel">
    <div class="x_title d-flex justify-content-between align-items-center flex-wrap">
        <h2><i class="fa fa-bar-chart text-primary"></i> Báo Cáo - Thống Kê</h2>
        <div class="d-flex gap-2">
            <form method="GET" action="{{ route('dashboard.index') }}" class="d-flex gap-2 align-items-end">
                <div>
                    <label class="form-label small">Tháng</label>
                    <select name="month" class="form-control form-control-sm" onchange="this.form.submit()">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                                Tháng {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="form-label small">Năm</label>
                    <select name="year" class="form-control form-control-sm" onchange="this.form.submit()">
                        @for($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
            </form>
            <a href="{{ route('dashboard.export', ['month' => $month, 'year' => $year]) }}" 
               class="btn btn-sm btn-success">
                <i class="fa fa-file-excel-o"></i> Xuất Excel
            </a>
        </div>
    </div>

    <div class="x_content">
        <!-- Section: Thống kê sự cố -->
        <div class="row mt-4">
            <div class="col-12">
                <h4 class="mb-3"><i class="fa fa-exclamation-triangle text-danger"></i> Thống kê sự cố</h4>
            </div>
        </div>

        <div class="row">
            <!-- Card 1: Số sự cố phát sinh -->
            <div class="col-md-4 col-sm-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Số sự cố phát sinh</h6>
                                <h3 class="mb-0 text-primary">{{ number_format($stats['so_su_co_phat_sinh']) }}</h3>
                                <small class="text-muted">Trong tháng {{ $month }}/{{ $year }}</small>
                            </div>
                            <div class="text-primary" style="font-size: 3rem; opacity: 0.3;">
                                <i class="fa fa-exclamation-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Số sự cố đã xử lý -->
            <div class="col-md-4 col-sm-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Số sự cố đã xử lý</h6>
                                <h3 class="mb-0 text-success">{{ number_format($stats['so_su_co_da_xu_ly']) }}</h3>
                                <small class="text-muted">Tỷ lệ: {{ $stats['ty_le_xu_ly'] }}%</small>
                            </div>
                            <div class="text-success" style="font-size: 3rem; opacity: 0.3;">
                                <i class="fa fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section: Thống kê tài chính -->
        <div class="row mt-4">
            <div class="col-12">
                <h4 class="mb-3"><i class="fa fa-money text-info"></i> Thống kê tài chính</h4>
            </div>
        </div>

        <div class="row">
            <!-- Card 3: Tổng tiền thu được -->
            <div class="col-md-4 col-sm-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Tổng tiền thu được</h6>
                                <h3 class="mb-0 text-info">{{ number_format($stats['tong_tien_thu_duoc'], 0, ',', '.') }} đ</h3>
                                <small class="text-muted">Phí phòng + điện + nước</small>
                            </div>
                            <div class="text-info" style="font-size: 3rem; opacity: 0.3;">
                                <i class="fa fa-money"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 4: Tổng chi phí bảo trì -->
            <div class="col-md-4 col-sm-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Tổng chi phí bảo trì</h6>
                                <h3 class="mb-0 text-warning">{{ number_format($stats['tong_chi_phi_bao_tri'], 0, ',', '.') }} đ</h3>
                                <small class="text-muted">Sửa chữa - bảo trì</small>
                            </div>
                            <div class="text-warning" style="font-size: 3rem; opacity: 0.3;">
                                <i class="fa fa-wrench"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Lợi nhuận ròng -->
            <div class="col-md-4 col-sm-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Lợi nhuận ròng</h6>
                                <h3 class="mb-0" style="color: #9b59b6;">{{ number_format($stats['tong_tien_thu_duoc'] - $stats['tong_chi_phi_bao_tri'], 0, ',', '.') }} đ</h3>
                                <small class="text-muted">Thu - Chi</small>
                            </div>
                            <div style="font-size: 3rem; opacity: 0.3; color: #9b59b6;">
                                <i class="fa fa-line-chart"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section: Thống kê slot -->
        <div class="row mt-4">
            <div class="col-12">
                <h4 class="mb-3"><i class="fa fa-bed text-secondary"></i> Thống kê slot</h4>
            </div>
        </div>

        <div class="row">
            <!-- Card 5: Slot đang trống -->
            <div class="col-md-4 col-sm-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Slot đang trống</h6>
                                <h3 class="mb-0 text-secondary">{{ number_format($stats['slot_dang_trong']) }}</h3>
                                <small class="text-muted">{{ $stats['phan_tram_slot_trong'] }}% tổng slot</small>
                            </div>
                            <div class="text-secondary" style="font-size: 3rem; opacity: 0.3;">
                                <i class="fa fa-bed"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 6: Slot đang có người ở -->
            <div class="col-md-4 col-sm-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Slot đang có người ở</h6>
                                <h3 class="mb-0 text-primary">{{ number_format($stats['slot_dang_co_nguoi_o']) }}</h3>
                                <small class="text-muted">{{ $stats['phan_tram_slot_co_nguoi'] }}% tổng slot</small>
                            </div>
                            <div class="text-primary" style="font-size: 3rem; opacity: 0.3;">
                                <i class="fa fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Phân chia section: Thống kê hồ sơ -->
        <div class="row mt-4">
            <div class="col-12">
                <h4 class="mb-3"><i class="fa fa-file-text-o text-primary"></i> Thống kê hồ sơ sinh viên</h4>
            </div>
        </div>

        <div class="row">
            <!-- Card 7: Tổng số hồ sơ được gửi -->
            <div class="col-md-4 col-sm-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Tổng số hồ sơ được gửi</h6>
                                <h3 class="mb-0 text-dark">{{ number_format($stats['tong_ho_so']) }}</h3>
                                <small class="text-muted">Tất cả hồ sơ trong hệ thống</small>
                            </div>
                            <div class="text-dark" style="font-size: 3rem; opacity: 0.3;">
                                <i class="fa fa-files-o"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 8: Hồ sơ đã duyệt -->
            <div class="col-md-4 col-sm-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Hồ sơ đã duyệt</h6>
                                <h3 class="mb-0 text-success">{{ number_format($stats['ho_so_da_duyet']) }}</h3>
                                <small class="text-muted">Tỷ lệ: {{ $stats['ty_le_duyet'] }}%</small>
                            </div>
                            <div class="text-success" style="font-size: 3rem; opacity: 0.3;">
                                <i class="fa fa-check-circle-o"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 9: Hồ sơ chưa duyệt -->
            <div class="col-md-4 col-sm-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Hồ sơ chưa duyệt</h6>
                                <h3 class="mb-0 text-warning">{{ number_format($stats['ho_so_chua_duyet']) }}</h3>
                                <small class="text-muted">
                                    Chờ duyệt: {{ number_format($stats['ho_so_cho_duyet']) }} |
                                    Chờ xác nhận: {{ number_format($stats['ho_so_cho_xac_nhan']) }}
                                </small>
                            </div>
                            <div class="text-warning" style="font-size: 3rem; opacity: 0.3;">
                                <i class="fa fa-clock-o"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Biểu đồ -->
        <div class="row mt-4">
            <!-- Biểu đồ sự cố -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-line-chart"></i> Biểu đồ sự cố (12 tháng gần nhất)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="suCoChart" height="80"></canvas>
                    </div>
                </div>
            </div>

            <!-- Biểu đồ tài chính -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-line-chart"></i> Biểu đồ tài chính (12 tháng gần nhất)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="taiChinhChart" height="80"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Biểu đồ slot -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-pie-chart"></i> Phân bố slot</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="slotChart" height="80"></canvas>
                    </div>
                </div>
            </div>

            <!-- Biểu đồ so sánh thu chi -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-bar-chart"></i> So sánh thu - chi (12 tháng gần nhất)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="thuChiChart" height="80"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Đợi DOM và Chart.js sẵn sàng
    document.addEventListener('DOMContentLoaded', function() {
        // Kiểm tra Chart.js đã được load chưa
        if (typeof Chart === 'undefined') {
            console.error('Chart.js chưa được load!');
            return;
        }

        // Dữ liệu từ controller
        const monthlyData = @json($monthlyData);
        const stats = @json($stats);

        // Kiểm tra dữ liệu
        if (!monthlyData || monthlyData.length === 0) {
            console.warn('Không có dữ liệu monthlyData');
        }

        // Xác định version Chart.js (v2 hoặc v3+)
        const isChartJSv3 = typeof Chart.defaults !== 'undefined' && Chart.defaults.plugins;

        // Biểu đồ sự cố
        const suCoCtx = document.getElementById('suCoChart');
        if (suCoCtx) {
            try {
                const suCoConfig = {
                    type: 'line',
                    data: {
                        labels: monthlyData.map(d => d.thang_label),
                        datasets: [{
                            label: 'Sự cố phát sinh',
                            data: monthlyData.map(d => d.su_co_phat_sinh),
                            borderColor: 'rgb(255, 99, 132)',
                            backgroundColor: 'rgba(255, 99, 132, 0.1)',
                            tension: 0.4,
                            fill: true
                        }, {
                            label: 'Sự cố đã xử lý',
                            data: monthlyData.map(d => d.su_co_da_xu_ly),
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            }
                        },
                        scales: {}
                    }
                };

                // Cấu hình scales theo version
                if (isChartJSv3) {
                    suCoConfig.options.scales.y = {
                        min: 0,
                        ticks: {
                            stepSize: 1
                        }
                    };
                } else {
                    suCoConfig.options.scales.yAxes = [{
                        ticks: {
                            beginAtZero: true,
                            stepSize: 1
                        }
                    }];
                }

                new Chart(suCoCtx, suCoConfig);
            } catch (error) {
                console.error('Lỗi khi vẽ biểu đồ sự cố:', error);
            }
        }

        // Biểu đồ tài chính
        const taiChinhCtx = document.getElementById('taiChinhChart');
        if (taiChinhCtx) {
            try {
                const taiChinhConfig = {
                    type: 'line',
                    data: {
                        labels: monthlyData.map(d => d.thang_label),
                        datasets: [{
                            label: 'Tiền thu được (VND)',
                            data: monthlyData.map(d => d.tien_thu_duoc),
                            borderColor: 'rgb(54, 162, 235)',
                            backgroundColor: 'rgba(54, 162, 235, 0.1)',
                            tension: 0.4,
                            fill: true
                        }, {
                            label: 'Chi phí bảo trì (VND)',
                            data: monthlyData.map(d => d.chi_phi_bao_tri),
                            borderColor: 'rgb(255, 159, 64)',
                            backgroundColor: 'rgba(255, 159, 64, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + 
                                            new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' đ';
                                    }
                                }
                            }
                        },
                        scales: {}
                    }
                };

                // Cấu hình scales theo version
                if (isChartJSv3) {
                    taiChinhConfig.options.scales.y = {
                        min: 0,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
                            }
                        }
                    };
                } else {
                    taiChinhConfig.options.scales.yAxes = [{
                        ticks: {
                            beginAtZero: true,
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
                            }
                        }
                    }];
                }

                new Chart(taiChinhCtx, taiChinhConfig);
            } catch (error) {
                console.error('Lỗi khi vẽ biểu đồ tài chính:', error);
            }
        }

        // Biểu đồ slot (Doughnut)
        const slotCtx = document.getElementById('slotChart');
        if (slotCtx) {
            try {
                new Chart(slotCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Slot đang trống', 'Slot đang có người ở'],
                        datasets: [{
                            data: [stats.slot_dang_trong || 0, stats.slot_dang_co_nguoi_o || 0],
                            backgroundColor: [
                                'rgb(108, 117, 125)',
                                'rgb(54, 162, 235)'
                            ],
                            hoverBackgroundColor: [
                                'rgb(73, 80, 87)',
                                'rgb(33, 150, 243)'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return label + ': ' + value + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Lỗi khi vẽ biểu đồ slot:', error);
            }
        }

        // Biểu đồ so sánh thu chi
        const thuChiCtx = document.getElementById('thuChiChart');
        if (thuChiCtx) {
            try {
                const thuChiConfig = {
                    type: 'bar',
                    data: {
                        labels: monthlyData.map(d => d.thang_label),
                        datasets: [{
                            label: 'Tiền thu được',
                            data: monthlyData.map(d => d.tien_thu_duoc),
                            backgroundColor: 'rgba(75, 192, 192, 0.6)',
                            borderColor: 'rgb(75, 192, 192)',
                            borderWidth: 1
                        }, {
                            label: 'Chi phí bảo trì',
                            data: monthlyData.map(d => d.chi_phi_bao_tri),
                            backgroundColor: 'rgba(255, 159, 64, 0.6)',
                            borderColor: 'rgb(255, 159, 64)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + 
                                            new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' đ';
                                    }
                                }
                            }
                        },
                        scales: {}
                    }
                };

                // Cấu hình scales theo version
                if (isChartJSv3) {
                    thuChiConfig.options.scales.y = {
                        min: 0,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
                            }
                        }
                    };
                } else {
                    thuChiConfig.options.scales.yAxes = [{
                        ticks: {
                            beginAtZero: true,
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
                            }
                        }
                    }];
                }

                new Chart(thuChiCtx, thuChiConfig);
            } catch (error) {
                console.error('Lỗi khi vẽ biểu đồ thu chi:', error);
            }
        }
    });
</script>
@endpush
@endsection

