@extends('admin.layouts.admin')

@section('title', 'Danh sách hóa đơn sự cố')

@section('content')
<div class="container mt-4" style="padding-bottom: 3rem;">
    @push('styles')
    <style>
        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .page-title i {
            color: #4e54c8;
        }

        .page-subtitle {
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }

        .btn-dergin {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .35rem;
            padding: .5rem 1.2rem;
            border-radius: 999px;
            font-weight: 600;
            font-size: .85rem;
            border: none;
            color: #fff;
            background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
            box-shadow: 0 6px 16px rgba(78, 84, 200, .22);
            transition: transform .2s ease, box-shadow .2s ease;
            text-decoration: none;
        }

        .btn-dergin:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(78, 84, 200, .32);
            color: #fff;
        }

        .btn-dergin i {
            font-size: .8rem;
        }

        .btn-dergin--info {
            background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
            box-shadow: 0 6px 16px rgba(14, 165, 233, .22);
        }

        .btn-dergin--info:hover {
            box-shadow: 0 10px 22px rgba(14, 165, 233, .32);
        }

        .btn-dergin--muted {
            background: linear-gradient(135deg, #6b7280 0%, #9ca3af 100%);
            box-shadow: 0 6px 16px rgba(107, 114, 128, .22);
        }

        .btn-dergin--muted:hover {
            box-shadow: 0 10px 22px rgba(107, 114, 128, .32);
        }

        .btn-dergin--success {
            background: linear-gradient(135deg, #10b981 0%, #22c55e 100%);
            box-shadow: 0 6px 16px rgba(16, 185, 129, .22);
        }

        .btn-dergin--success:hover {
            box-shadow: 0 10px 22px rgba(16, 185, 129, .32);
        }

        .btn-dergin--warning {
            background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
            box-shadow: 0 6px 16px rgba(245, 158, 11, .22);
        }

        .btn-dergin--warning:hover {
            box-shadow: 0 10px 22px rgba(245, 158, 11, .32);
        }

        .btn-dergin.btn-sm {
            padding: .35rem .8rem;
            font-size: .75rem;
        }

        .stats-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            transition: all 0.2s ease;
            text-align: center;
        }

        .stats-card:hover {
            /* transform: translateY(-2px); */
            box-shadow: 0 12px 35px rgba(15, 23, 42, 0.1);
        }

        .stats-card h5 {
            font-size: 0.85rem;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stats-card h3 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stats-card .text-primary {
            color: #4e54c8 !important;
        }

        .stats-card .text-info {
            color: #0ea5e9 !important;
        }

        .stats-card .text-success {
            color: #10b981 !important;
        }

        .stats-card .text-warning {
            color: #f59e0b !important;
        }

        .stats-card small {
            font-size: 0.85rem;
            color: #6b7280;
            display: block;
            margin-top: 0.25rem;
        }

        .filter-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 20px 24px;
            margin-bottom: 24px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            transition: box-shadow 0.2s ease;
        }

        .filter-card:hover {
            box-shadow: 0 12px 35px rgba(15, 23, 42, 0.08);
        }

        .filter-card .form-label {
            display: block;
            font-weight: 600;
            color: #374151;
            font-size: 0.9rem;
            line-height: 1.3;
            height: auto;
            margin-bottom: 0.5rem;
            overflow: visible;
            white-space: normal;
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            padding: 0.65rem 1rem;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }

        /* Chiều cao đồng bộ cho ô tìm kiếm, select và nút lọc */
        .filter-card .form-control,
        .filter-card .form-select {
            height: 46px;
        }

        .filter-card .btn-dergin {
            height: 46px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .form-control:focus, .form-select:focus {
            border-color: #4e54c8;
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.1);
            outline: none;
        }

        .alert {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .alert-success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        .alert-danger {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        .alert-info {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
            border-left: 4px solid #3b82f6;
        }

        .pagination {
            margin-top: 1.5rem;
        }

        .pagination .page-link {
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            color: #4e54c8;
            margin: 0 4px;
            padding: 0.5rem 0.75rem;
            transition: all 0.2s ease;
        }

        .pagination .page-link:hover {
            background: #4e54c8;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(78, 84, 200, 0.2);
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
            border-color: #4e54c8;
            box-shadow: 0 4px 12px rgba(78, 84, 200, 0.3);
        }

        /* Đảm bảo các box thống kê cao bằng nhau */
        .stats-row > [class*="col-"] {
            display: flex;
        }

        .stats-row .stats-card {
            flex: 1;
        }
    </style>
    @endpush


        <div>
            <h4 class="page-title">
                <i class="fa fa-file-text-o"></i>
                Danh sách hóa đơn sự cố
            </h4>
            <p class="page-subtitle">Quản lý và theo dõi tất cả hóa đơn sự cố trong hệ thống</p>
      
        <a href="{{ route('suco.index') }}" class="btn-dergin btn-dergin--muted">
            <i class="fa fa-arrow-left"></i> Quay lại sự cố
        </a>
    </div>

    {{-- 📊 Thống kê --}}
    <div class="row mb-4 stats-row">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stats-card">
                <h5>Tổng hóa đơn</h5>
                <h3 class="text-primary">{{ number_format($tong_hoa_don, 0, ',', '.') }}</h3>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stats-card">
                <h5>Tổng tiền</h5>
                <h3 class="text-info">{{ number_format($tong_tien, 0, ',', '.') }} ₫</h3>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stats-card">
                <h5>Đã thanh toán</h5>
                <h3 class="text-success">{{ number_format($da_thanh_toan, 0, ',', '.') }}</h3>
                <small>{{ number_format($tong_tien_da_thu, 0, ',', '.') }} ₫</small>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stats-card">
                <h5>Chưa thanh toán</h5>
                <h3 class="text-warning">{{ number_format($chua_thanh_toan, 0, ',', '.') }}</h3>
                <small>{{ number_format($tong_tien_chua_thu, 0, ',', '.') }} ₫</small>
            </div>
        </div>
    </div>

    {{-- 🔍 Tìm kiếm và lọc --}}
    <form method="GET" action="{{ route('hoadonsuco.index') }}" class="filter-card">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Tìm kiếm</label>
                <input type="text" name="search" value="{{ request('search') ?? '' }}"
                    class="form-control" placeholder="MSSV hoặc Họ tên">
            </div>
            <div class="col-md-2">
                <label class="form-label">Trạng thái thanh toán</label>
                <select name="trang_thai_thanh_toan" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="da_thanh_toan"
                        {{ request('trang_thai_thanh_toan') == 'da_thanh_toan' ? 'selected' : '' }}>Đã thanh toán
                    </option>
                    <option value="chua_thanh_toan"
                        {{ request('trang_thai_thanh_toan') == 'chua_thanh_toan' ? 'selected' : '' }}>Chưa thanh toán</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Từ ngày</label>
                <input type="date" name="date_from" value="{{ request('date_from') ?? '' }}"
                    class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">Đến ngày</label>
                <input type="date" name="date_to" value="{{ request('date_to') ?? '' }}"
                    class="form-control">
            </div>
            <div class="col-md-3 d-flex gap-2 justify-content-end">
                <button type="submit" class="btn-dergin btn-dergin--info">
                    <i class="fa fa-search"></i> Tìm kiếm
                </button>
                @if (request('search') || request('trang_thai_thanh_toan') || request('date_from') || request('date_to'))
                    <a href="{{ route('hoadonsuco.index') }}" class="btn-dergin btn-dergin--muted">
                        <i class="fa fa-times"></i> Xóa lọc
                    </a>
                @endif
            </div>
        </div>
    </form>

    {{-- 🟢 Thông báo --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fa fa-info-circle"></i> {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- 📋 Bảng danh sách --}}
    <div class="room-table-wrapper">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 room-table text-center align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Sinh viên</th>
                                <th>Phòng / Khu</th>
                                <th>Ngày gửi</th>
                                <th>Hoàn thành</th>
                                <th>Mô tả</th>
                                <th>Trạng thái</th>
                                <th>Số tiền</th>
                                <th>Thanh toán</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hoa_dons as $hd)
                                @php
                                    $badge = match ($hd->trang_thai) {
                                        'Tiếp nhận' => 'badge-soft-secondary',
                                        'Đang xử lý' => 'badge-soft-warning',
                                        'Hoàn thành' => 'badge-soft-success',
                                        default => 'badge-soft-secondary',
                                    };
                                    $student = $hd->sinhVien ?? null;
                                    $phong = null;
                                    if ($student) {
                                        if ($student->slot && $student->slot->phong) {
                                            $phong = $student->slot->phong;
                                        } elseif ($student->phong) {
                                            $phong = $student->phong;
                                        } elseif ($hd->phong) {
                                            $phong = $hd->phong;
                                        }
                                    } elseif ($hd->phong) {
                                        $phong = $hd->phong;
                                    }
                                    $tenPhongDisplay = $phong->ten_phong ?? '-';
                                    $tenKhuDisplay = $phong->khu->ten_khu ?? '-';
                                @endphp

                                <tr>
                                    <td>{{ $hd->id }}</td>
                                    <td class="text-start">
                                        <div class="fw-semibold">{{ $student->ho_ten ?? '---' }}</div>
                                        <small class="text-muted">MSSV: {{ $student->ma_sinh_vien ?? '---' }}</small>
                                    </td>
                                    <td>
                                        <div>{{ $tenPhongDisplay }}</div>
                                        <small class="text-muted">Khu {{ $tenKhuDisplay }}</small>
                                    </td>
                                    <td>{{ $hd->ngay_gui ? \Carbon\Carbon::parse($hd->ngay_gui)->format('d/m/Y') : '-' }}
                                    </td>
                                    <td>{{ $hd->ngay_hoan_thanh ? \Carbon\Carbon::parse($hd->ngay_hoan_thanh)->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="text-start" style="max-width:220px;">
                                        <div class="text-truncate" title="{{ $hd->mo_ta }}">
                                            {{ Str::limit($hd->mo_ta, 50) }}
                                        </div>
                                    </td>
                                    <td><span class="badge {{ $badge }}">{{ $hd->trang_thai }}</span></td>
                                    <td><strong class="text-primary">{{ number_format($hd->payment_amount, 0, ',', '.') }}
                                            ₫</strong></td>
                                    <td>
                                        @if ($hd->is_paid)
                                            <span class="badge badge-soft-success"><i class="fa fa-check-circle"></i> Đã
                                                TT</span>
                                        @else
                                            <span class="badge badge-soft-warning text-dark"><i class="fa fa-clock-o"></i>
                                                Chưa TT</span>
                                        @endif
                                    </td>

                                    {{-- 🔹 Cột hành động --}}
                                    <td class="text-end">
                                        <div class="room-actions dropdown position-relative">
                                            <button type="button" class="btn btn-dergin btn-dergin--muted action-gear"
                                                title="Tác vụ">
                                                <i class="fa fa-gear"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="{{ route('suco.show', $hd->id) }}"
                                                        class="dropdown-item d-flex align-items-center gap-2">
                                                        <i class="fa fa-eye text-muted"></i>
                                                        <span>Xem chi tiết</span>
                                                    </a>
                                                </li>
                                                @if (!$hd->is_paid)
                                                    <li>
                                                        <button type="button"
                                                            class="dropdown-item d-flex align-items-center gap-2 text-info"
                                                            data-bs-toggle="modal" data-bs-target="#paymentModal"
                                                            data-id="{{ $hd->id }}"
                                                            data-url="{{ route('hoadonsuco.thanhtoan', $hd->id) }}"
                                                            data-amount="{{ $hd->payment_amount }}">
                                                            <i class="fa fa-money"></i>
                                                            <span>Thanh toán</span>
                                                        </button>
                                                    </li>
                                                    @if (auth()->user()->role === 'admin' || auth()->user()->role === 'nhanvien')
                                                        <li>
                                                            <form action="{{ route('hoadonsuco.xacnhan', $hd->id) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Xác nhận thanh toán {{ number_format($hd->payment_amount, 0, ',', '.') }} ₫?');">
                                                                @csrf
                                                                <button type="submit"
                                                                    class="dropdown-item d-flex align-items-center gap-2 text-success">
                                                                    <i class="fa fa-check"></i>
                                                                    <span>Xác nhận</span>
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                @else
                                                    @if (auth()->user()->role === 'admin')
                                                        <li>
                                                            <form action="{{ route('hoadonsuco.huy', $hd->id) }}" method="POST"
                                                                onsubmit="return confirm('Bạn có chắc muốn hủy xác nhận thanh toán?');">
                                                                @csrf
                                                                <button type="submit"
                                                                    class="dropdown-item d-flex align-items-center gap-2 text-danger">
                                                                    <i class="fa fa-undo"></i>
                                                                    <span>Hủy thanh toán</span>
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">
                                        <img src="https://dummyimage.com/120x80/eff3f9/9aa8b8&text=No+data" class="mb-2"
                                            alt="">
                                        <div>Chưa có hóa đơn sự cố nào</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

    @push('styles')
    <style>
                html {
                    scroll-behavior: auto !important
                }


                .room-page__title {
                    font-size: 1.75rem;
                    font-weight: 700;
                    color: #1f2937;
                }

                .room-table-wrapper {
                    background: #fff;
                    border-radius: 14px;
                    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
                    padding: 1.25rem;
                }

                .room-table {
                    margin-bottom: 0;
                    border-collapse: separate;
                    border-spacing: 0 12px;
                }

                .room-table thead th {
                    font-size: .78rem;
                    text-transform: uppercase;
                    letter-spacing: .05em;
                    color: #6c757d;
                    border: none;
                    padding-bottom: .75rem;
                }

                .room-table tbody tr {
                    background: #f9fafc;
                    border-radius: 16px;
                    transition: transform .2s ease, box-shadow .2s ease;
                }

                .room-table tbody tr:hover {
                    /* transform: translateY(-2px); */
                    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
                }

                .room-table tbody td {
                    border: none;
                    vertical-align: middle;
                    padding: 1rem .95rem;
                }

                .room-table tbody tr td:first-child {
                    border-top-left-radius: 16px;
                    border-bottom-left-radius: 16px;
                }

                .room-table tbody tr td:last-child {
                    border-top-right-radius: 16px;
                    border-bottom-right-radius: 16px;
                }

                .room-table .fit {
                    white-space: nowrap;
                    width: 1%;
                }

                .room-table th.text-center,
                .room-table td.text-center {
                    text-align: center;
                }

                .room-actions {
                    display: flex;
                    justify-content: center;
                }

                .room-actions.dropdown {
                    position: relative;
                }

                /* Nút răng cưa gọn, nằm giữa cột */
                .room-actions .action-gear {
                    min-width: 40px;
                    padding: .45rem .7rem;
                    border-radius: 999px;
                }

                /* MENU: bay ngang sang trái, canh giữa ô, không tràn xuống dòng dưới */
                .room-actions .dropdown-menu {
                    position: absolute;
                    top: 50% !important;
                    /* lấy mốc giữa ô Thao tác */
                    right: 110%;
                    /* bật ngang sang trái của nút răng cưa */
                    left: auto;
                    transform: translateY(-50%);
                    /* canh giữa theo chiều dọc */
                    z-index: 1050;

                    min-width: 190px;
                    border-radius: 16px;
                    padding: .4rem 0;
                    margin: 0;
                    border: 1px solid #e5e7eb;
                    box-shadow: 0 16px 40px rgba(15, 23, 42, .18);
                    font-size: .82rem;
                    background: #fff;
                }

                /* Item trong dropdown: icon + chữ đẹp, hover nhẹ */
                .room-actions .dropdown-item {
                    display: flex;
                    align-items: center;
                    gap: .55rem;
                    padding: .42rem .9rem;
                    color: #4b5563;
                }

                .room-actions .dropdown-item i {
                    width: 16px;
                    text-align: center;
                }

                .room-actions .dropdown-item:hover {
                    background: #eef2ff;
                    color: #111827;
                }

                /* Riêng nút Xóa giữ màu đỏ */
                .room-actions .dropdown-item.text-danger,
                .room-actions .dropdown-item.text-danger:hover {
                    color: #dc2626;
                    font-weight: 500;
                }


                .btn-dergin {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    gap: .35rem;
                    padding: .4rem .9rem;
                    border-radius: 999px;
                    font-weight: 600;
                    font-size: .72rem;
                    border: none;
                    color: #fff;
                    background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
                    box-shadow: 0 6px 16px rgba(78, 84, 200, .22);
                    transition: transform .2s ease, box-shadow .2s ease;
                }

                .btn-dergin:hover {
                    transform: translateY(-1px);
                    box-shadow: 0 10px 22px rgba(78, 84, 200, .32);
                    color: #fff;
                }

                .btn-dergin i {
                    font-size: .8rem;
                }

                .btn-dergin--muted {
                    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
                }

                .btn-dergin--info {
                    background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
                }

                .btn-dergin--danger {
                    background: linear-gradient(135deg, #f43f5e 0%, #ef4444 100%);
                }

                .avatar-56 {
                    width: 56px;
                    height: 56px;
                    border-radius: 12px;
                    /* bo góc, không tròn nữa */
                    object-fit: cover;
                    border: 2px solid #e5e7eb;
                    /* viền nhạt */
                    background: #fff;
                }



                @media (max-width: 992px) {
                    .room-table thead {
                        display: none;
                    }

                    .room-table tbody {
                        display: block;
                    }

                    .room-table tbody tr {
                        display: flex;
                        flex-direction: column;
                        padding: 1rem;
                    }

                    .room-table tbody td {
                        display: flex;
                        justify-content: space-between;
                        padding: .35rem 0;
                    }
                }
            </style>
    @endpush

    <div class="d-flex justify-content-center mt-3">
        {{ $hoa_dons->onEachSide(1)->links('pagination::bootstrap-4') }}
    </div>
</div>

    {{-- Modal thanh toán --}}
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">💳 Thanh toán hóa đơn sự cố</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <strong>Số tiền cần thanh toán:</strong>
                        <span id="paymentAmount" class="text-danger fs-5">0 ₫</span>
                    </div>

                    <div class="mb-3">
                        <label for="paymentMethod" class="form-label">Chọn phương thức thanh toán</label>
                        <select id="paymentMethod" class="form-select" required>
                            <option value="">-- Chọn hình thức --</option>
                            <option value="tien_mat">💵 Tiền mặt</option>
                            <option value="chuyen_khoan">🏦 Chuyển khoản</option>
                        </select>
                    </div>

                    <div id="bankInfo"
                        style="display: none; margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                        <h6 class="mb-3">Thông tin chuyển khoản:</h6>
                        <div class="row">
                            <div class="col-md-7">
                                <p class="mb-2"><strong>Tên tài khoản:</strong> Nguyễn Quang Thắng</p>
                                <p class="mb-2"><strong>Số tài khoản:</strong> T1209666</p>
                                <p class="mb-0"><strong>Ngân hàng:</strong> Techcombank - Chi nhánh Hà Nội</p>
                            </div>
                            <div class="col-md-5 text-center">
                                <img src="{{ asset('images/ma1qr.jpg') }}" alt="QR chuyển khoản"
                                    class="img-fluid rounded border" style="max-width: 120px;">
                                <p class="mt-2 text-muted" style="font-size: 0.85rem;">Quét mã để chuyển khoản</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label for="ghi_chu_thanh_toan" class="form-label">Ghi chú thanh toán</label>
                        <textarea name="ghi_chu_thanh_toan" id="ghi_chu_thanh_toan" class="form-control" rows="3"
                            placeholder="Vui lòng nhập thông tin thanh toán (ví dụ: tên phòng-khu, số điện thoại...)" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-success" id="confirmPaymentBtn">
                        <i class="fa fa-check"></i> Xác nhận thanh toán
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal đánh giá sau khi thanh toán --}}
    <div class="modal fade" id="ratingModal" tabindex="-1" aria-labelledby="ratingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ratingModalLabel">⭐ Đánh giá xử lý sự cố</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <form id="ratingForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="mb-2 text-muted">Vui lòng đánh giá chất lượng xử lý (1 - 5 sao)</p>
                        <div class="d-flex gap-2 justify-content-center fs-3" id="ratingStars">
                            <span data-value="1" role="button">★</span>
                            <span data-value="2" role="button">★</span>
                            <span data-value="3" role="button">★</span>
                            <span data-value="4" role="button">★</span>
                            <span data-value="5" role="button">★</span>
                        </div>
                        <input type="hidden" name="rating" id="ratingValue" value="5">
                        <div class="mt-3">
                            <label for="ratingFeedback" class="form-label">Góp ý (tùy chọn)</label>
                            <input type="text" class="form-control" id="ratingFeedback" name="feedback" placeholder="Nhập góp ý...">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Bỏ qua</button>
                        <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- CSRF token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentMethodSelect = document.getElementById('paymentMethod');
            const bankInfo = document.getElementById('bankInfo');
            const confirmBtn = document.getElementById('confirmPaymentBtn');
            const paymentModal = document.getElementById('paymentModal');
            const paymentAmount = document.getElementById('paymentAmount');
            const ratingModal = document.getElementById('ratingModal');
            const ratingForm = document.getElementById('ratingForm');
            const ratingStars = document.getElementById('ratingStars');
            const ratingValue = document.getElementById('ratingValue');

            const closeAllMenus = () => {
                document.querySelectorAll('.room-actions .dropdown-menu.show')
                    .forEach(menu => menu.classList.remove('show'));
            };

            // Fallback mở modal khi Bootstrap JS không hook tự động + dropdown gear
            document.addEventListener('click', function(e) {
                const gearBtn = e.target.closest('.action-gear');
                if (gearBtn) {
                    e.preventDefault();
                    const menu = gearBtn.closest('.room-actions')?.querySelector('.dropdown-menu');
                    const isOpen = menu && menu.classList.contains('show');
                    closeAllMenus();
                    if (menu && !isOpen) {
                        menu.classList.add('show');
                    }
                    return;
                }

                let handledDropdownAction = false;
                const paymentTrigger = e.target.closest('[data-bs-target="#paymentModal"][data-id]');
                if (paymentTrigger && paymentModal) {
                    handledDropdownAction = true;
                    const hoaDonId = paymentTrigger.getAttribute('data-id');
                    const actionUrl = paymentTrigger.getAttribute('data-url');
                    const amount = paymentTrigger.getAttribute('data-amount');
                    if (confirmBtn) {
                        if (hoaDonId) confirmBtn.setAttribute('data-id', hoaDonId);
                        if (actionUrl) confirmBtn.setAttribute('data-url', actionUrl);
                    }
                    if (paymentAmount && amount) {
                        paymentAmount.textContent = new Intl.NumberFormat('vi-VN').format(amount) + ' ₫';
                    }
                    // Reset fields
                    if (paymentMethodSelect) paymentMethodSelect.value = '';
                    const noteEl = document.getElementById('ghi_chu_thanh_toan');
                    if (noteEl) noteEl.value = '';
                    if (bankInfo) bankInfo.style.display = 'none';
                    // Cố gắng dùng Bootstrap nếu có, nếu không thì tự mở
                    try {
                        const bsModal = bootstrap.Modal.getOrCreateInstance(paymentModal);
                        bsModal.show();
                    } catch (_) {
                        paymentModal.classList.add('show');
                        paymentModal.style.display = 'block';
                        paymentModal.removeAttribute('aria-hidden');
                        paymentModal.setAttribute('aria-modal', 'true');
                        // Thêm backdrop đơn giản
                        if (!document.querySelector('.modal-backdrop')) {
                            const backdrop = document.createElement('div');
                            backdrop.className = 'modal-backdrop fade show';
                            document.body.appendChild(backdrop);
                        }
                        document.body.classList.add('modal-open');
                    }
                }

                if (handledDropdownAction || e.target.closest('.room-actions .dropdown-item')) {
                    closeAllMenus();
                } else if (!e.target.closest('.room-actions')) {
                    closeAllMenus();
                }
            });

            // Rating stars UI
            if (ratingStars && ratingValue) {
                const paint = (val) => {
                    [...ratingStars.querySelectorAll('span')].forEach(el => {
                        el.classList.toggle('text-warning', Number(el.getAttribute('data-value')) <= val);
                        el.classList.toggle('text-muted', Number(el.getAttribute('data-value')) > val);
                    });
                };
                ratingStars.addEventListener('click', (e) => {
                    const star = e.target.closest('span[data-value]');
                    if (!star) return;
                    ratingValue.value = star.getAttribute('data-value');
                    paint(Number(ratingValue.value));
                });
                paint(Number(ratingValue.value || 5));
            }

            // Hiển thị thông tin chuyển khoản nếu chọn "chuyen_khoan"
            function toggleBankInfo() {
                const method = paymentMethodSelect?.value;
                if (bankInfo) {
                    bankInfo.style.display = method === 'chuyen_khoan' ? 'block' : 'none';
                }
            }

            if (paymentMethodSelect) {
                paymentMethodSelect.addEventListener('change', toggleBankInfo);
            }

            // Gắn ID và số tiền hóa đơn vào modal khi mở
            if (paymentModal) {
                paymentModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const hoaDonId = button?.getAttribute('data-id');
                    const actionUrl = button?.getAttribute('data-url');
                    const amount = button?.getAttribute('data-amount');

                    if (confirmBtn) {
                        if (hoaDonId) {
                            confirmBtn.setAttribute('data-id', hoaDonId);
                        }
                        if (actionUrl) {
                            confirmBtn.setAttribute('data-url', actionUrl);
                        }
                    }

                    if (paymentAmount && amount) {
                        paymentAmount.textContent = new Intl.NumberFormat('vi-VN').format(amount) + ' ₫';
                    }

                    // Reset form
                    if (paymentMethodSelect) paymentMethodSelect.value = '';
                    if (document.getElementById('ghi_chu_thanh_toan')) {
                        document.getElementById('ghi_chu_thanh_toan').value = '';
                    }
                    toggleBankInfo();
                });
            }
            // Fallback đóng modal khi không có Bootstrap
            document.addEventListener('click', function(e) {
                const closeBtn = e.target.closest('[data-bs-dismiss="modal"]');
                if (!closeBtn) return;
                const parentModal = closeBtn.closest('.modal');
                if (!parentModal) return;
                try {
                    const bsModal = bootstrap.Modal.getOrCreateInstance(parentModal);
                    bsModal.hide();
                } catch (_) {
                    parentModal.classList.remove('show');
                    parentModal.style.display = 'none';
                    parentModal.setAttribute('aria-hidden', 'true');
                    document.body.classList.remove('modal-open');
                    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                }
            });

            // Gửi yêu cầu thanh toán
            if (confirmBtn) {
                confirmBtn.addEventListener('click', function() {
                    const hoaDonId = this?.getAttribute('data-id');
                    const targetUrl = this?.getAttribute('data-url') || (hoaDonId ? `/hoadonsuco/${hoaDonId}/thanh-toan` : '');
                    const hinhThuc = paymentMethodSelect?.value || '';
                    const ghiChu = document.getElementById('ghi_chu_thanh_toan')?.value || '';

                    if (!targetUrl) {
                        alert('❌ Không tìm thấy địa chỉ thanh toán! Vui lòng tải lại trang.');
                        return;
                    }

                    if (!hoaDonId || !hinhThuc) {
                        alert('⚠️ Vui lòng chọn hình thức thanh toán!');
                        return;
                    }

                    if (!ghiChu.trim()) {
                        alert('⚠️ Vui lòng nhập ghi chú thanh toán!');
                        return;
                    }

                    // Disable button để tránh double submit
                    this.disabled = true;
                    this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Đang xử lý...';

                    fetch(targetUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                hinh_thuc_thanh_toan: hinhThuc,
                                ghi_chu_thanh_toan: ghiChu
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                // Đóng modal thanh toán
                                try {
                                    const modalInstance = bootstrap.Modal.getInstance(paymentModal);
                                    if (modalInstance) modalInstance.hide();
                                } catch (_) {
                                    paymentModal.classList.remove('show');
                                    paymentModal.style.display = 'none';
                                    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                                    document.body.classList.remove('modal-open');
                                }
                                // Mở modal đánh giá ngay
                                if (ratingForm) {
                                    ratingForm.setAttribute('action', '/admin/suco/' + hoaDonId + '/danh-gia');
                                }
                                try {
                                    const bsModal = bootstrap.Modal.getOrCreateInstance(ratingModal);
                                    bsModal.show();
                                } catch (_) {
                                    if (ratingModal) {
                                        ratingModal.classList.add('show');
                                        ratingModal.style.display = 'block';
                                        ratingModal.removeAttribute('aria-hidden');
                                        ratingModal.setAttribute('aria-modal', 'true');
                                        if (!document.querySelector('.modal-backdrop')) {
                                            const backdrop = document.createElement('div');
                                            backdrop.className = 'modal-backdrop fade show';
                                            document.body.appendChild(backdrop);
                                        }
                                        document.body.classList.add('modal-open');
                                    }
                                }
                            } else {
                                alert('❌ ' + (data.message || 'Có lỗi xảy ra!'));
                                this.disabled = false;
                                this.innerHTML = '<i class="fa fa-check"></i> Xác nhận thanh toán';
                            }
                        })
                        .catch(err => {
                            console.error('Lỗi gửi yêu cầu:', err);
                            alert('❌ Không thể gửi yêu cầu. Vui lòng thử lại!');
                            this.disabled = false;
                            this.innerHTML = '<i class="fa fa-check"></i> Xác nhận thanh toán';
                        });
                });
            }

            // Gửi đánh giá sau thanh toán
            if (ratingForm) {
                ratingForm.addEventListener('submit', function(ev) {
                    ev.preventDefault();
                    const action = ratingForm.getAttribute('action');
                    const formData = new FormData(ratingForm);
                    fetch(action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(res => {
                        // Nếu server trả redirect/html, vẫn tiếp tục
                        try { return res.json(); } catch (_) { return {}; }
                    })
                    .then(() => {
                        // Đóng modal và refresh danh sách
                        try {
                            const bsModal = bootstrap.Modal.getInstance(ratingModal);
                            if (bsModal) bsModal.hide();
                        } catch (_) {
                            if (ratingModal) {
                                ratingModal.classList.remove('show');
                                ratingModal.style.display = 'none';
                            }
                        }
                        window.location.reload();
                    })
                    .catch(() => {
                        window.location.reload();
                    });
                });
            }
        });
    </script>
@endsection
