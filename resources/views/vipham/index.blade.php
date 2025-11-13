@extends('admin.layouts.admin')

@section('content')
    <div class="container mt-4">

        <div>
            <h3 class="room-page__title mb-1">Vi phạm sinh viên</h3>
            <p class="text-muted mb-0">Ghi nhận, lọc và xử lý các vi phạm</p>
        </div>
        <div class="d-flex gap-2 mb-3">
            <a href="{{ route('vipham.create') }}" class="btn btn-dergin btn-dergin--info">
                <i class="fa fa-plus"></i><span>Ghi vi phạm</span>
            </a>
        </div>

        {{-- THANH TÌM KIẾM + BỘ LỌC (giống trang sinh viên) --}}
        <form method="GET" class="mb-3 search-bar">
            <div class="input-group">
                <input type="text" name="student_keyword" value="{{ $studentKeyword ?? '' }}" class="form-control"
                    placeholder="Tìm tên hoặc mã sinh viên">
                <button type="submit" class="btn btn-outline-secondary">Tìm kiếm</button>

                <button type="button" class="btn btn-outline-primary" id="openFilterModalBtn">
                    <i class="fa fa-filter mr-1"></i> Bộ lọc
                </button>

                @if (
                    !empty($studentKeyword) ||
                        request()->filled('q') ||
                        request()->filled('type_id') ||
                        request()->filled('status') ||
                        request()->filled('date_from') ||
                        request()->filled('date_to'))
                    <a href="{{ route('vipham.index') }}" class="btn btn-outline-secondary">Xóa</a>
                @endif
            </div>
        </form>

        @push('styles')
            <style>
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
                    transform: translateY(-2px);
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

                /* ACTION GEAR giống bên sinh viên */
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

                /* Riêng item Xóa giữ màu đỏ */
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
                    object-fit: cover;
                    border: 2px solid #e5e7eb;
                    background: #fff;
                }

                @media (max-width:992px) {
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

                    .room-actions {
                        justify-content: flex-start;
                    }
                }
            </style>
        @endpush

        {{-- BẢNG VI PHẠM (STT, Sinh viên, Hình ảnh, Mã SV, Loại, ...) --}}
        <div class="room-table-wrapper">
            <div class="table-responsive">
                @php
                    $perPage = $violations->perPage();
                    $currentPage = $violations->currentPage();
                    $sttBase = ($currentPage - 1) * $perPage;
                @endphp

                <table class="table table-hover mb-0 room-table">
                    <thead>
                        <tr>
                            <th class="fit text-center">STT</th>
                            <th>Sinh viên</th>
                            <th class="fit text-center">Hình ảnh</th>
                            <th class="fit text-center">Mã SV</th>
                            <th>Loại</th>
                            <th class="fit text-center">Trạng thái</th>
                            <th class="text-end fit">Tiền phạt</th>
                            <th class="fit text-center">Biên lai</th>
                            <th class="fit text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($violations as $v)
                            @php
                                $isResolved = $v->status === 'resolved';
                                $statusText = $isResolved ? 'Đã xử lý' : 'Chưa xử lý';
                                $statusBadge = $isResolved ? 'badge-soft-success' : 'badge-soft-warning';
                            @endphp
                            <tr>
                                {{-- STT --}}
                                <td class="fit text-center">{{ $sttBase + $loop->iteration }}</td>

                                {{-- Sinh viên --}}
                                <td>
                                    <div class="font-weight-600">{{ $v->student->ho_ten ?? 'N/A' }}</div>
                                </td>

                                {{-- Hình ảnh --}}
                                <td class="fit text-center">
                                    @if ($v->image)
                                        <img src="{{ asset('storage/' . $v->image) }}" alt="Ảnh vi phạm" class="avatar-56">
                                    @else
                                        <span class="text-muted small">Không có ảnh</span>
                                    @endif
                                </td>

                                {{-- Mã sinh viên --}}
                                <td class="fit text-center">
                                    <span class="text-muted small">{{ $v->student->ma_sinh_vien ?? '' }}</span>
                                </td>

                                {{-- Loại --}}
                                <td>{{ $v->type->name ?? 'N/A' }}</td>

                                {{-- Trạng thái --}}
                                <td class="fit text-center">
                                    <span class="badge {{ $statusBadge }}">{{ $statusText }}</span>
                                </td>

                                {{-- Tiền phạt --}}
                                <td class="text-end fit">
                                    {{ $v->penalty_amount ? number_format($v->penalty_amount, 0, ',', '.') : '-' }}
                                </td>

                                {{-- Biên lai --}}
                                <td class="fit text-center">
                                    {{ $v->receipt_no ?? '-' }}
                                </td>

                                {{-- Thao tác: răng cưa + dropdown --}}
                                <td class="fit text-center">
                                    <div class="room-actions dropdown position-relative">
                                        <button type="button" class="btn btn-dergin btn-dergin--muted action-gear"
                                            title="Thao tác">
                                            <i class="fa fa-gear"></i>
                                        </button>

                                        <ul class="dropdown-menu">
                                            {{-- Xem chi tiết --}}
                                            <li>
                                                <a href="{{ route('vipham.show', $v->id) }}"
                                                    class="dropdown-item d-flex align-items-center gap-2">
                                                    <i class="fa fa-eye text-muted"></i>
                                                    <span>Xem chi tiết</span>
                                                </a>
                                            </li>

                                            {{-- Sửa --}}
                                            <li>
                                                <a href="{{ route('vipham.edit', $v->id) }}"
                                                    class="dropdown-item d-flex align-items-center gap-2">
                                                    <i class="fa fa-pencil text-primary"></i>
                                                    <span>Sửa</span>
                                                </a>
                                            </li>

                                            {{-- Xử lý (chỉ hiện khi status = open) --}}
                                            @if ($v->status == 'open')
                                                <li>
                                                    <form action="{{ route('vipham.resolve', $v->id) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button class="dropdown-item d-flex align-items-center gap-2">
                                                            <i class="fa fa-check text-success"></i>
                                                            <span>Xử lý</span>
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif

                                            {{-- Xóa --}}
                                            <li>
                                                <form action="{{ route('vipham.destroy', $v->id) }}" method="POST"
                                                    class="d-inline" onsubmit="return confirm('Xóa vi phạm này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        class="dropdown-item d-flex align-items-center gap-2 text-danger">
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
                                <td colspan="9" class="text-center text-muted py-4">
                                    <img src="https://dummyimage.com/120x80/eff3f9/9aa8b8&text=No+data" alt=""
                                        class="mb-2">
                                    <div>Chưa có dữ liệu vi phạm</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- THỐNG KÊ + PHÂN TRANG --}}
        <div class="mt-3 d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Hiển thị {{ $violations->firstItem() ?? 0 }}–{{ $violations->lastItem() ?? 0 }} /
                {{ $violations->total() }} bản ghi
            </div>
            <div>
                {{ $violations->links() }}
            </div>
        </div>

        <div class="mt-3 d-flex justify-content-center">
            {{ $violations->links() }}
        </div>
    </div>

    {{-- MODAL BỘ LỌC --}}
    <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Bộ lọc vi phạm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>

                <form method="GET" id="filterForm">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                {{-- Hàng 1: Từ khóa ghi chú/biên lai + Tên/Mã sinh viên --}}
                                <div class="col-md-6 mb-3">
                                    <label class="small text-muted">Tìm ghi chú / biên lai</label>
                                    <input type="text" name="q" value="{{ request('q', $q ?? '') }}"
                                        class="form-control" placeholder="VD: BL-2025-001, nội dung ghi chú">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="small text-muted">Tên / Mã sinh viên</label>
                                    <input type="text" name="student_keyword"
                                        value="{{ request('student_keyword', $studentKeyword ?? '') }}"
                                        class="form-control" placeholder="VD: Nguyễn Văn A, PD05...">
                                </div>

                                {{-- Hàng 2: Loại, Trạng thái --}}
                                <div class="col-md-4 mb-3">
                                    <label class="small text-muted">Loại vi phạm</label>
                                    <select name="type_id" class="form-control">
                                        <option value="">-- Tất cả --</option>
                                        @foreach ($types as $t)
                                            <option value="{{ $t->id }}" @selected($typeId == $t->id)>
                                                {{ $t->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="small text-muted">Trạng thái</label>
                                    <select name="status" class="form-control">
                                        <option value="">-- Tất cả --</option>
                                        <option value="open" @selected($status == 'open')>Chưa xử lý</option>
                                        <option value="resolved" @selected($status == 'resolved')>Đã xử lý</option>
                                    </select>
                                </div>

                                {{-- Hàng 3: Từ ngày / Đến ngày --}}
                                <div class="col-md-2 mb-3">
                                    <label class="small text-muted">Từ ngày</label>
                                    <input type="date" name="date_from" value="{{ $dateFrom }}"
                                        class="form-control">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="small text-muted">Đến ngày</label>
                                    <input type="date" name="date_to" value="{{ $dateTo }}"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <a href="{{ route('vipham.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
                        <button type="submit" class="btn btn-primary">Áp dụng</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

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
        </script>

        <script>
            // Dropdown custom cho nút răng cưa (giống trang sinh viên)
            (function() {
                document.addEventListener('click', function(e) {
                    const menus = document.querySelectorAll('.room-actions .dropdown-menu');
                    const gearBtn = e.target.closest('.action-gear');
                    const insideMenu = e.target.closest('.room-actions .dropdown-menu');

                    // Click vào nút răng cưa
                    if (gearBtn) {
                        e.preventDefault();

                        const wrapper = gearBtn.closest('.room-actions');
                        const menu = wrapper ? wrapper.querySelector('.dropdown-menu') : null;
                        const isOpen = menu && menu.classList.contains('show');

                        // Đóng tất cả menu khác
                        menus.forEach(m => m.classList.remove('show'));

                        // Mở/đóng menu hiện tại
                        if (menu && !isOpen) {
                            menu.classList.add('show');
                        }
                        return;
                    }

                    // Click vào item trong menu thì đóng lại
                    if (insideMenu && (e.target.closest('.dropdown-item') || e.target.closest('button'))) {
                        menus.forEach(m => m.classList.remove('show'));
                        return;
                    }

                    // Click ra ngoài => đóng hết
                    if (!insideMenu) {
                        menus.forEach(m => m.classList.remove('show'));
                    }
                });
            })();
        </script>
    @endpush
@endsection
