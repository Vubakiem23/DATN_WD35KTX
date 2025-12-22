@extends('admin.layouts.admin')
@section('title', 'Danh sách phản hồi sinh viên')
@section('content')
    <div class="container mt-4">
        <!-- Header -->
        <div class="mb-3">
            <h3 class="page-title mb-1">
                <i class="fa fa-comments me-2"></i>Danh sách phản hồi sinh viên
            </h3>
            <p class="text-muted mb-0">Quản lý và xử lý các phản hồi từ sinh viên</p>
        </div>

        <!-- Thông báo -->
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Thanh tìm kiếm và lọc -->
        <form method="GET" class="mb-3 search-bar">
            <div class="input-group">
                <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control" 
                       placeholder="Tìm theo tiêu đề, nội dung hoặc tên sinh viên...">
                <button type="submit" class="btn btn-outline-secondary">Tìm kiếm</button>
                <button type="button" class="btn btn-outline-primary" id="openFilterModalBtn">
                    <i class="fa fa-filter mr-1"></i> Bộ lọc
                </button>
                @if(request()->hasAny(['keyword', 'trang_thai']))
                    <a href="{{ route('admin.phan_hoi.list') }}" class="btn btn-outline-secondary">Xóa</a>
                @endif
            </div>
        </form>

        <!-- Thống kê nhanh -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card stat-card--total">
                    <div class="stat-card__icon">
                        <i class="fa fa-comments"></i>
                    </div>
                    <div class="stat-card__content">
                        <div class="stat-card__value">{{ $phanHois->total() }}</div>
                        <div class="stat-card__label">Tổng phản hồi</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-card--pending">
                    <div class="stat-card__icon">
                        <i class="fa fa-clock-o"></i>
                    </div>
                    <div class="stat-card__content">
                        <div class="stat-card__value">{{ $phanHois->where('trang_thai', 0)->count() }}</div>
                        <div class="stat-card__label">Chờ xử lý</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-card--done">
                    <div class="stat-card__icon">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div class="stat-card__content">
                        <div class="stat-card__value">{{ $phanHois->where('trang_thai', 1)->count() }}</div>
                        <div class="stat-card__label">Đã xử lý</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-card--rejected">
                    <div class="stat-card__icon">
                        <i class="fa fa-ban"></i>
                    </div>
                    <div class="stat-card__content">
                        <div class="stat-card__value">{{ $phanHois->where('trang_thai', 2)->count() }}</div>
                        <div class="stat-card__label">Đã từ chối</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bảng danh sách -->
        <div class="table-wrapper">
            <div class="table-responsive">
                <table class="table table-hover mb-0 data-table">
                    <thead>
                        <tr>
                            <th class="fit text-center">STT</th>
                            <th>Sinh viên</th>
                            <th>Tiêu đề</th>
                            <th>Nội dung</th>
                            <th class="fit text-center">Ngày gửi</th>
                            <th class="fit text-center">Trạng thái</th>
                            <th class="fit text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($phanHois as $index => $phanHoi)
                            @php
                                $sinhVien = \App\Models\User::where('id', $phanHoi->sinh_vien_id)->first();
                                $isResolved = $phanHoi->trang_thai == 1;
                            @endphp
                            <tr>
                                <td class="fit text-center">{{ $phanHois->firstItem() + $index }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-circle">
                                            {{ strtoupper(substr($sinhVien->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $sinhVien->name ?? 'N/A' }}</div>
                                            <div class="text-muted small">{{ $sinhVien->email ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('admin.phan_hoi.show', $phanHoi) }}" class="text-primary fw-semibold text-decoration-none">
                                        {{ Str::limit($phanHoi->tieu_de, 40) }}
                                    </a>
                                </td>
                                <td>
                                    <div class="text-truncate-2">
                                        {{ Str::limit($phanHoi->noi_dung, 60) }}
                                    </div>
                                </td>
                                <td class="fit text-center">
                                    <div class="small text-muted">
                                        {{ $phanHoi->created_at ? $phanHoi->created_at->format('d/m/Y') : 'N/A' }}
                                    </div>
                                    <div class="small text-muted">
                                        {{ $phanHoi->created_at ? $phanHoi->created_at->format('H:i') : '' }}
                                    </div>
                                </td>
                                <td class="fit text-center">
                                    @if($phanHoi->trang_thai == 1)
                                        <span class="badge badge-soft-success">
                                            <i class="fa fa-check me-1"></i>Đã xử lý
                                        </span>
                                    @elseif($phanHoi->trang_thai == 2)
                                        <span class="badge badge-soft-danger">
                                            <i class="fa fa-times me-1"></i>Đã từ chối
                                        </span>
                                    @else
                                        <span class="badge badge-soft-warning">
                                            <i class="fa fa-clock-o me-1"></i>Chờ xử lý
                                        </span>
                                    @endif
                                </td>
                                <td class="fit text-center">
                                    <div class="action-dropdown">
                                        <button type="button" class="btn btn-action action-gear" title="Thao tác">
                                            <i class="fa fa-cog"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="{{ route('admin.phan_hoi.show', $phanHoi) }}" class="dropdown-item d-flex align-items-center gap-2">
                                                    <i class="fa fa-eye text-muted"></i>
                                                    <span>Xem chi tiết</span>
                                                </a>
                                            </li>
                                            @if($phanHoi->trang_thai == 0)
                                                <li>
                                                    <form action="{{ route('admin.phan_hoi.resolve', $phanHoi) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2">
                                                            <i class="fa fa-check text-success"></i>
                                                            <span>Đánh dấu đã xử lý</span>
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.phan_hoi.reject', $phanHoi) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2">
                                                            <i class="fa fa-ban text-danger"></i>
                                                            <span>Từ chối</span>
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                            <li>
                                                <form action="{{ route('admin.phan_hoi.delete', $phanHoi) }}" method="POST" class="form-delete">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-danger">
                                                        <i class="fa fa-trash"></i>
                                                        <span>Xóa</span>
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">Chưa có phản hồi nào từ sinh viên</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Phân trang -->
        @if($phanHois->hasPages())
            <div class="mt-3 d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Hiển thị {{ $phanHois->firstItem() ?? 0 }}–{{ $phanHois->lastItem() ?? 0 }} / {{ $phanHois->total() }} phản hồi
                </div>
                <div>
                    {{ $phanHois->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Modal Bộ lọc -->
    <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Bộ lọc phản hồi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <form method="GET" id="filterForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="small text-muted">Từ khóa</label>
                                <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control" 
                                       placeholder="Tìm theo tiêu đề, nội dung...">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small text-muted">Trạng thái</label>
                                <select name="trang_thai" class="form-control">
                                    <option value="">-- Tất cả --</option>
                                    <option value="0" {{ request('trang_thai') === '0' ? 'selected' : '' }}>Chờ xử lý</option>
                                    <option value="1" {{ request('trang_thai') === '1' ? 'selected' : '' }}>Đã xử lý</option>
                                    <option value="2" {{ request('trang_thai') === '2' ? 'selected' : '' }}>Đã từ chối</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('admin.phan_hoi.list') }}" class="btn btn-outline-secondary">Xóa lọc</a>
                        <button type="submit" class="btn btn-primary">Áp dụng</button>
                    </div>
                </form>
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
            padding: 1.25rem;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(15, 23, 42, 0.1);
        }

        .stat-card__icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-card--total .stat-card__icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
        }

        .stat-card--pending .stat-card__icon {
            background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
            color: #fff;
        }

        .stat-card--done .stat-card__icon {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #fff;
        }

        .stat-card--rejected .stat-card__icon {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #fff;
        }

        .stat-card__value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1f2937;
            line-height: 1;
        }

        .stat-card__label {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }

        /* Table Wrapper */
        .table-wrapper {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            padding: 1.25rem;
        }

        .data-table {
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .data-table thead th {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            border: none;
            padding: 0.75rem 1rem;
            font-weight: 600;
        }

        .data-table tbody tr {
            background: #f9fafb;
            border-radius: 12px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .data-table tbody tr:hover {
            background: #f3f4f6;
            box-shadow: 0 8px 25px rgba(15, 23, 42, 0.08);
        }

        .data-table tbody td {
            border: none;
            vertical-align: middle;
            padding: 1rem;
        }

        .data-table tbody tr td:first-child {
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }

        .data-table tbody tr td:last-child {
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        .fit {
            width: 1%;
            white-space: nowrap;
        }

        /* Avatar Circle */
        .avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1rem;
        }

        /* Badges */
        .badge-soft-success {
            background: #d1fae5;
            color: #065f46;
            border-radius: 999px;
            padding: 0.4rem 0.75rem;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .badge-soft-warning {
            background: #fef3c7;
            color: #92400e;
            border-radius: 999px;
            padding: 0.4rem 0.75rem;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .badge-soft-danger {
            background: #fee2e2;
            color: #dc2626;
            border-radius: 999px;
            padding: 0.4rem 0.75rem;
            font-weight: 600;
            font-size: 0.75rem;
        }

        /* Text truncate 2 lines */
        .text-truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            color: #4b5563;
            font-size: 0.875rem;
        }

        /* Action Dropdown */
        .action-dropdown {
            position: relative;
        }

        .btn-action {
            width: 38px;
            height: 38px;
            padding: 0;
            border-radius: 10px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border: none;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-action:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
            color: #fff;
        }

        .action-dropdown {
            position: relative;
        }

        .action-dropdown .dropdown-menu {
            position: absolute;
            top: 50%;
            right: 110%;
            left: auto;
            transform: translateY(-50%);
            z-index: 1050;
            min-width: 200px;
            border-radius: 16px;
            padding: 0.4rem 0;
            margin: 0;
            border: 1px solid #e5e7eb;
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.18);
            font-size: 0.82rem;
            background: #fff;
            display: none;
        }

        .action-dropdown .dropdown-menu.show {
            display: block;
        }

        .action-dropdown .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            color: #4b5563;
            transition: background 0.15s ease;
            white-space: nowrap;
        }

        .action-dropdown .dropdown-item:hover {
            background: #eef2ff;
            color: #111827;
        }

        .action-dropdown .dropdown-item i {
            width: 18px;
            text-align: center;
            font-size: 0.9rem;
        }

        .action-dropdown .dropdown-item.text-danger,
        .action-dropdown .dropdown-item.text-danger:hover {
            color: #dc2626;
            font-weight: 500;
        }

        .action-dropdown .dropdown-item.text-danger:hover {
            background: #fef2f2;
        }

        .action-dropdown .dropdown-menu li {
            list-style: none;
        }

        .action-dropdown .dropdown-menu form {
            margin: 0;
        }

        .action-dropdown .dropdown-menu form button.dropdown-item {
            width: 100%;
            text-align: left;
            background: none;
            border: none;
            cursor: pointer;
        }

        /* Empty State */
        .empty-state {
            padding: 2rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stat-card {
                padding: 1rem;
            }

            .stat-card__value {
                font-size: 1.5rem;
            }

            .data-table thead {
                display: none;
            }

            .data-table tbody tr {
                display: block;
                margin-bottom: 1rem;
                padding: 1rem;
            }

            .data-table tbody td {
                display: flex;
                justify-content: space-between;
                padding: 0.5rem 0;
            }

            .data-table tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                color: #6b7280;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        // Mở modal bộ lọc (chạy được cả BS4 và BS5)
        (function() {
            document.addEventListener('DOMContentLoaded', function() {
                var btn = document.getElementById('openFilterModalBtn');
                if (!btn) return;

                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var modalEl = document.getElementById('filterModal');
                    if (!modalEl) return;

                    try {
                        if (window.bootstrap && bootstrap.Modal) {
                            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                            modal.show();
                        } else if (window.$ && $('#filterModal').modal) {
                            $('#filterModal').modal('show');
                        }
                    } catch (err) {
                        if (window.$ && $('#filterModal').modal) {
                            $('#filterModal').modal('show');
                        }
                    }
                });
            });
        })();

        document.addEventListener('DOMContentLoaded', function () {
            // Xác nhận xóa
            const forms = document.getElementsByClassName('form-delete');
            for (let i = 0; i < forms.length; i++) {
                forms[i].addEventListener('submit', function (e) {
                    if (!confirm('Bạn có chắc chắn muốn xóa phản hồi này không?')) {
                        e.preventDefault();
                    }
                });
            }

            // Dropdown custom cho nút thao tác
            document.addEventListener('click', function(e) {
                const menus = document.querySelectorAll('.action-dropdown .dropdown-menu');
                const gearBtn = e.target.closest('.action-gear');
                const insideMenu = e.target.closest('.action-dropdown .dropdown-menu');

                if (gearBtn) {
                    e.preventDefault();
                    const wrapper = gearBtn.closest('.action-dropdown');
                    const menu = wrapper ? wrapper.querySelector('.dropdown-menu') : null;
                    const isOpen = menu && menu.classList.contains('show');

                    menus.forEach(m => m.classList.remove('show'));

                    if (menu && !isOpen) {
                        menu.classList.add('show');
                    }
                    return;
                }

                if (insideMenu && (e.target.closest('.dropdown-item') || e.target.closest('button'))) {
                    menus.forEach(m => m.classList.remove('show'));
                    return;
                }

                if (!insideMenu) {
                    menus.forEach(m => m.classList.remove('show'));
                }
            });
        });
    </script>
    @endpush
@endsection
