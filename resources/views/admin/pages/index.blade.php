@extends('admin.layouts.admin')
@section('title', 'Bảng Điều Khiển Admin - Báo Cáo Thống Kê')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css">
<style>
    .dashboard-container {
        padding: 20px 0;
    }
    
    .month-selector {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .month-selector label {
        font-weight: 600;
        color: #2c3e50;
        margin-right: 10px;
    }
    
    .month-selector input[type="month"] {
        padding: 8px 12px;
        border: 2px solid #e8e8e8;
        border-radius: 6px;
        font-size: 14px;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-left: 4px solid;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }
    
    .stat-card.primary { border-left-color: #3498db; }
    .stat-card.success { border-left-color: #27ae60; }
    .stat-card.warning { border-left-color: #f39c12; }
    .stat-card.danger { border-left-color: #e74c3c; }
    .stat-card.info { border-left-color: #17a2b8; }
    .stat-card.purple { border-left-color: #9b59b6; }
    .stat-card.dark { border-left-color: #34495e; }
    
    .stat-card .stat-icon {
        font-size: 36px;
        margin-bottom: 15px;
        opacity: 0.8;
    }
    
    .stat-card.primary .stat-icon { color: #3498db; }
    .stat-card.success .stat-icon { color: #27ae60; }
    .stat-card.warning .stat-icon { color: #f39c12; }
    .stat-card.danger .stat-icon { color: #e74c3c; }
    .stat-card.info .stat-icon { color: #17a2b8; }
    .stat-card.purple .stat-icon { color: #9b59b6; }
    .stat-card.dark .stat-icon { color: #34495e; }
    
    .stat-card .stat-label {
        font-size: 14px;
        color: #7f8c8d;
        margin-bottom: 8px;
        font-weight: 500;
    }
    
    .stat-card .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 5px;
    }
    
    .stat-card .stat-subvalue {
        font-size: 12px;
        color: #95a5a6;
    }
    
    .chart-container {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .chart-title {
        font-size: 18px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e8e8e8;
    }
    
    .chart-title i {
        margin-right: 8px;
        color: #667eea;
    }
    
    .dashboard-section {
        margin-bottom: 25px;
    }
    
    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e8e8e8;
    }
    
    .section-title i {
        margin-right: 8px;
        color: #667eea;
    }
    
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .dashboard-card {
        background: white;
        border-radius: 10px;
        padding: 25px 20px;
        text-align: center;
        text-decoration: none;
        color: #333;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: 1px solid #e8e8e8;
        display: block;
        position: relative;
        overflow: hidden;
    }
    
    .dashboard-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--card-color);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }
    
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        text-decoration: none;
        color: #333;
    }
    
    .dashboard-card:hover::before {
        transform: scaleX(1);
    }
    
    .dashboard-card i {
        font-size: 36px;
        margin-bottom: 15px;
        color: var(--card-color);
        display: block;
    }
    
    .dashboard-card strong {
        font-size: 14px;
        font-weight: 600;
        color: #2c3e50;
        display: block;
        line-height: 1.4;
    }
    
    /* Color variants */
    .card-primary { --card-color: #3498db; }
    .card-success { --card-color: #27ae60; }
    .card-warning { --card-color: #f39c12; }
    .card-info { --card-color: #17a2b8; }
    .card-danger { --card-color: #e74c3c; }
    .card-purple { --card-color: #9b59b6; }
    .card-teal { --card-color: #1abc9c; }
    .card-dark { --card-color: #34495e; }
    .card-blue-gray { --card-color: #607d8b; }
    
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .dashboard-grid {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
        }
        
        .dashboard-card {
            padding: 20px 15px;
        }
        
        .dashboard-card i {
            font-size: 28px;
            margin-bottom: 10px;
        }
    }
    
    @media (max-width: 480px) {
        .dashboard-grid {
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
    }
</style>
@endpush

@section('content')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3><i class="fa fa-bar-chart"></i> Báo Cáo Thống Kê</h3>
        </div>
    </div>

    <div class="clearfix"></div>

    <div class="dashboard-container">
        <!-- Month Selector -->
        <div class="month-selector">
            <form method="GET" action="{{ route('admin.index') }}" style="display: flex; align-items: center; gap: 15px;">
                <label for="month">Chọn tháng:</label>
                <input type="month" name="month" id="month" value="{{ $selectedMonth }}" onchange="this.form.submit()">
                <span style="color: #7f8c8d; font-size: 14px;">Báo cáo định kỳ theo tháng</span>
            </form>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <i class="fa fa-exclamation-circle stat-icon"></i>
                <div class="stat-label">Số sự cố phát sinh</div>
                <div class="stat-value">{{ number_format($soSuCoPhatSinh) }}</div>
                <div class="stat-subvalue">Trong tháng {{ \Carbon\Carbon::parse($selectedMonth)->format('m/Y') }}</div>
            </div>

            <div class="stat-card success">
                <i class="fa fa-check-circle stat-icon"></i>
                <div class="stat-label">Số sự cố đã xử lý</div>
                <div class="stat-value">{{ number_format($soSuCoDaXuLy) }}</div>
                <div class="stat-subvalue">Tỷ lệ: {{ $tyLeXuLySuCo }}%</div>
            </div>

            <div class="stat-card info">
                <i class="fa fa-money stat-icon"></i>
                <div class="stat-label">Tổng tiền thu được</div>
                <div class="stat-value">{{ number_format($tongTienThuDuoc, 0, ',', '.') }} đ</div>
                <div class="stat-subvalue">Phòng: {{ number_format($tongTienPhong, 0, ',', '.') }} đ | Điện nước: {{ number_format($tongTienDienNuoc, 0, ',', '.') }} đ</div>
            </div>

            <div class="stat-card danger">
                <i class="fa fa-wrench stat-icon"></i>
                <div class="stat-label">Tổng chi phí bảo trì - sửa chữa</div>
                <div class="stat-value">{{ number_format($tongChiPhiBaoTriSuaChua, 0, ',', '.') }} đ</div>
                <div class="stat-subvalue">Sự cố: {{ number_format($tongChiPhiSuCo, 0, ',', '.') }} đ</div>
            </div>

            <div class="stat-card warning">
                <i class="fa fa-bed stat-icon"></i>
                <div class="stat-label">Tình trạng slot</div>
                <div class="stat-value">{{ number_format($soSlotCoNguoiO) }} / {{ number_format($tongSoSlot) }}</div>
                <div class="stat-subvalue">Trống: {{ number_format($soSlotTrong) }} slot</div>
            </div>

            <div class="stat-card purple">
                <i class="fa fa-line-chart stat-icon"></i>
                <div class="stat-label">Lợi nhuận ròng</div>
                <div class="stat-value" style="color: {{ $loiNhuanRong >= 0 ? '#27ae60' : '#e74c3c' }};">
                    {{ number_format($loiNhuanRong, 0, ',', '.') }} đ
                </div>
                <div class="stat-subvalue">Thu - Chi</div>
            </div>
        </div>

        <!-- Section: Thống kê hồ sơ sinh viên -->
        <div class="dashboard-section" style="margin-top: 40px;">
            <h3 class="section-title">
                <i class="fa fa-file-text-o"></i> Thống kê hồ sơ sinh viên
            </h3>
            <div class="stats-grid">
                <div class="stat-card dark">
                    <i class="fa fa-files-o stat-icon"></i>
                    <div class="stat-label">Tổng số hồ sơ được gửi</div>
                    <div class="stat-value">{{ number_format($tongHoSo) }}</div>
                    <div class="stat-subvalue">Tất cả hồ sơ trong hệ thống</div>
                </div>

                <div class="stat-card success">
                    <i class="fa fa-check-circle-o stat-icon"></i>
                    <div class="stat-label">Hồ sơ đã duyệt</div>
                    <div class="stat-value">{{ number_format($daDuyet) }}</div>
                    <div class="stat-subvalue">Tỷ lệ: {{ $tyLeDuyet }}%</div>
                </div>

                <div class="stat-card warning">
                    <i class="fa fa-clock-o stat-icon"></i>
                    <div class="stat-label">Hồ sơ chưa duyệt</div>
                    <div class="stat-value">{{ number_format($chuaDuyet) }}</div>
                    <div class="stat-subvalue">Trong đó: {{ number_format($choDuyet) }} đang chờ duyệt</div>
                </div>
            </div>
        </div>

        <!-- Section: Thống kê bảo trì tài sản -->
        <div class="dashboard-section" style="margin-top: 40px;">
            <h3 class="section-title">
                <i class="fa fa-wrench"></i> Thống kê bảo trì tài sản
            </h3>
            <div class="stats-grid">
                <div class="stat-card info">
                    <i class="fa fa-cog stat-icon"></i>
                    <div class="stat-label">Số tài sản đã bảo trì</div>
                    <div class="stat-value">{{ number_format($soTaiSanDaBaoTri) }}</div>
                    <div class="stat-subvalue">Trong tháng {{ \Carbon\Carbon::parse($selectedMonth)->format('m/Y') }}</div>
                </div>

                <div class="stat-card warning">
                    <i class="fa fa-spinner stat-icon"></i>
                    <div class="stat-label">Số tài sản đang bảo trì</div>
                    <div class="stat-value">{{ number_format($soTaiSanDangBaoTri) }}</div>
                    <div class="stat-subvalue">Đang trong quá trình bảo trì</div>
                </div>

                <div class="stat-card success">
                    <i class="fa fa-check-square-o stat-icon"></i>
                    <div class="stat-label">Số tài sản đã hoàn thành bảo trì</div>
                    <div class="stat-value">{{ number_format($soTaiSanHoanThanhBaoTri) }}</div>
                    <div class="stat-subvalue">Tỷ lệ: {{ $tyLeHoanThanhBaoTri }}%</div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row">
            <div class="col-md-12">
                <div class="chart-container">
                    <div class="chart-title">
                        <i class="fa fa-bar-chart"></i> Biểu đồ sự cố theo tháng (12 tháng gần nhất)
                    </div>
                    <canvas id="suCoChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="chart-container">
                    <div class="chart-title">
                        <i class="fa fa-money"></i> Biểu đồ thu chi theo tháng
                    </div>
                    <canvas id="thuChiChart" height="80"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <div class="chart-title">
                        <i class="fa fa-pie-chart"></i> Tỷ lệ slot
                    </div>
                    <canvas id="slotChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <!-- Quick Access Section -->
        <div class="dashboard-section">
            <h3 class="section-title">
                <i class="fa fa-bolt"></i> Truy cập nhanh
            </h3>
            <div class="dashboard-grid">
                <a href="{{ route('users.index') }}" class="dashboard-card card-primary">
                    <i class="fa fa-list"></i>
                    <strong>Danh sách tài khoản</strong>
                </a>
                <a href="{{ route('phong.index') }}" class="dashboard-card card-success">
                    <i class="fa fa-bed"></i>
                    <strong>Quản lý phòng</strong>
                </a>
                <a href="{{ route('khu.index') }}" class="dashboard-card card-success">
                    <i class="fa fa-building"></i>
                    <strong>Quản lý khu</strong>
                </a>
                <a href="{{ route('sinhvien.index') }}" class="dashboard-card card-success">
                    <i class="fa fa-users"></i>
                    <strong>Quản lý sinh viên</strong>
                </a>
                <a href="{{ route('vipham.index') }}" class="dashboard-card card-warning">
                    <i class="fa fa-exclamation-triangle"></i>
                    <strong>Quản lý vi phạm</strong>
                </a>
                <a href="{{ route('taisan.index') }}" class="dashboard-card card-info">
                    <i class="fa fa-cubes"></i>
                    <strong>Quản lý tài sản</strong>
                </a>
                <a href="{{ route('lichbaotri.index') }}" class="dashboard-card card-info">
                    <i class="fa fa-calendar-check-o"></i>
                    <strong>Lịch bảo trì</strong>
                </a>
                <a href="{{ route('kho.index') }}" class="dashboard-card card-info">
                    <i class="fa fa-archive"></i>
                    <strong>Kho đồ</strong>
                </a>
                <a href="{{ route('loaitaisan.index') }}" class="dashboard-card card-info">
                    <i class="fa fa-tags"></i>
                    <strong>Loại tài sản</strong>
                </a>
                <a href="{{ route('hoadon.index') }}" class="dashboard-card card-danger">
                    <i class="fa fa-file-text-o"></i>
                    <strong>Quản lý hóa đơn</strong>
                </a>
                <a href="{{ route('suco.index') }}" class="dashboard-card card-purple">
                    <i class="fa fa-wrench"></i>
                    <strong>Quản lý sự cố</strong>
                </a>
                <a href="{{ route('thongbao.index') }}" class="dashboard-card card-blue-gray">
                    <i class="fa fa-bullhorn"></i>
                    <strong>Thông báo sự cố</strong>
                </a>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Biểu đồ sự cố
    const suCoCtx = document.getElementById('suCoChart').getContext('2d');
    const suCoChart = new Chart(suCoCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($thongKeTheoThang, 'thang')) !!},
            datasets: [
                {
                    label: 'Sự cố phát sinh',
                    data: {!! json_encode(array_column($thongKeTheoThang, 'su_co_phat_sinh')) !!},
                    backgroundColor: 'rgba(52, 152, 219, 0.7)',
                    borderColor: 'rgba(52, 152, 219, 1)',
                    borderWidth: 2
                },
                {
                    label: 'Sự cố đã xử lý',
                    data: {!! json_encode(array_column($thongKeTheoThang, 'su_co_da_xu_ly')) !!},
                    backgroundColor: 'rgba(39, 174, 96, 0.7)',
                    borderColor: 'rgba(39, 174, 96, 1)',
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Biểu đồ thu chi
    const thuChiCtx = document.getElementById('thuChiChart').getContext('2d');
    const thuChiChart = new Chart(thuChiCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($thongKeTheoThang, 'thang')) !!},
            datasets: [
                {
                    label: 'Tổng thu (đ)',
                    data: {!! json_encode(array_column($thongKeTheoThang, 'tong_tien_thu')) !!},
                    backgroundColor: 'rgba(39, 174, 96, 0.2)',
                    borderColor: 'rgba(39, 174, 96, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Tổng chi (đ)',
                    data: {!! json_encode(array_column($thongKeTheoThang, 'tong_chi_phi')) !!},
                    backgroundColor: 'rgba(231, 76, 60, 0.2)',
                    borderColor: 'rgba(231, 76, 60, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
                        }
                    }
                }
            }
        }
    });

    // Biểu đồ slot
    const slotCtx = document.getElementById('slotChart').getContext('2d');
    const slotChart = new Chart(slotCtx, {
        type: 'doughnut',
        data: {
            labels: ['Slot có người ở', 'Slot trống'],
            datasets: [{
                data: [{{ $soSlotCoNguoiO }}, {{ $soSlotTrong }}],
                backgroundColor: [
                    'rgba(39, 174, 96, 0.8)',
                    'rgba(149, 165, 166, 0.8)'
                ],
                borderColor: [
                    'rgba(39, 174, 96, 1)',
                    'rgba(149, 165, 166, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.parsed + ' slot';
                            return label;
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
