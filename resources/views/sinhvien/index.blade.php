@extends('admin.layouts.admin')
@section('content')
    <div class="container mt-4">

        <div>
            <h3 class="room-page__title mb-1">Danh sách sinh viên</h3>
            <p class="text-muted mb-0">Theo dõi hồ sơ sinh viên và trạng thái duyệt</p>
        </div>
        <div class="d-flex gap-2 mb-3">
            <a href="{{ route('sinhvien.create') }}" class="btn btn-dergin btn-dergin--info">
                <i class="fa fa-plus"></i><span>Thêm sinh viên</span>
            </a>
        </div>

        <!-- Ô tìm kiếm -->
        <form method="GET" class="mb-3 search-bar">
            <div class="input-group">
                <input type="text" name="search" value="{{ $keyword ?? '' }}" class="form-control"
                    placeholder="Tìm kiếm tên sinh viên">
                <button type="submit" class="btn btn-outline-secondary">Tìm kiếm</button>
                <button type="button" class="btn btn-outline-primary" id="openFilterModalBtn">
                    <i class="fa fa-filter mr-1"></i> Bộ lọc
                </button>

                @if (!empty($keyword))
                    <a href="{{ route('sinhvien.index') }}" class="btn btn-outline-secondary">Xóa</a>
                @endif
            </div>
        </form>

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

        {{-- Bảng danh sách sinh viên --}}
        <div class="room-table-wrapper">
            <div class="table-responsive">
                @php
                    $perPage = $sinhviens->perPage();
                    $currentPage = $sinhviens->currentPage();
                    $sttBase = ($currentPage - 1) * $perPage;
                @endphp

                <table class="table table-hover mb-0 room-table">
                        <thead>
                            <tr>
                                <th class="fit text-center">STT</th>
                                <th>Họ và tên</th>
                            <th class="fit text-center">Hình ảnh</th>
                            <th class="fit text-center">Mã SV</th>
                            <th class="fit text-center">Phòng</th>
                            <th class="fit text-center">Trạng thái</th>
                            <th class="fit text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sinhviens as $sv)
                                @php
                                    $status = $sv->trang_thai_ho_so ?? 'Khác';
                                    $badgeClass =
                                        $status === 'Đã duyệt'
                                            ? 'badge-soft-success'
                                            : ($status === 'Chờ duyệt'
                                                ? 'badge-soft-warning'
                                                : 'badge-soft-secondary');
                                    $imgUrl = $sv->anh_sinh_vien
                                        ? asset('storage/' . $sv->anh_sinh_vien)
                                        : asset('images/default-avatar.png');

                                // Ưu tiên lấy phòng từ slot (nếu có), nếu không thì lấy từ phong_id trực tiếp
                                $phongHienTai = $sv->slot && $sv->slot->phong ? $sv->slot->phong : $sv->phong ?? null;
                                @endphp
                                <tr>
                                    <td class="fit text-center">{{ $sttBase + $loop->iteration }}</td>

                                    <td class="font-weight-600">{{ $sv->ho_ten }}</td>

                                <td class="fit text-center">
                                        <img src="{{ $imgUrl }}" alt="Ảnh {{ $sv->ho_ten }}" class="avatar-56">
                                    </td>

                                <td class="fit text-center">
                                    {{ $sv->ma_sinh_vien }}
                                </td>

                                <td class="fit text-center">
                                    {{ $phongHienTai ? $phongHienTai->ten_phong : '-' }}
                                </td>

                                <td class="fit text-center">
                                    <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                    </td>

                                <td class="fit text-center">
                                    <div class="room-actions dropdown position-relative">
                                        <button type="button" class="btn btn-dergin btn-dergin--muted action-gear">
                                            <i class="fa fa-gear"></i>
                                        </button>

                                        <ul class="dropdown-menu">
                                            <li>
                                                <button type="button"
                                                    class="dropdown-item d-flex align-items-center gap-2 openModalBtn"
                                                    data-id="{{ $sv->id }}">
                                                    <i class="fa fa-eye text-muted"></i>
                                                    <span>Xem chi tiết</span>
                                            </button>
                                            </li>

                                            <li>
                                            <a href="{{ route('sinhvien.edit', $sv->id) }}"
                                                    class="dropdown-item d-flex align-items-center gap-2">
                                                    <i class="fa fa-pencil text-primary"></i>
                                                    <span>Sửa</span>
                                            </a>
                                            </li>

                                            @if (($sv->trang_thai_ho_so ?? '') !== 'Đã duyệt')
                                                <li>
                                                <form action="{{ route('sinhvien.approve', $sv->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf @method('PATCH')
                                                        <button class="dropdown-item d-flex align-items-center gap-2">
                                                            <i class="fa fa-check text-success"></i>
                                                            <span>Duyệt</span>
                                                    </button>
                                                </form>
                                                </li>
                                            @endif

                                            <li>
                                            <form action="{{ route('sinhvien.destroy', $sv->id) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Xác nhận xóa sinh viên này?')">
                                                @csrf @method('DELETE')
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
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <img src="https://dummyimage.com/120x80/eff3f9/9aa8b8&text=No+data" class="mb-2"
                                            alt="">
                                        <div>Chưa có sinh viên phù hợp</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        {{-- MODAL BỘ LỌC --}}
        <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="filterModalLabel">Bộ lọc sinh viên</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>

                    <form method="GET" id="filterForm">
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="row">
                                    {{-- Hàng 1: Tìm nhanh – Giới tính – Tình trạng – Phòng – Khu --}}
                                    <div class="col-md-4 mb-3">
                                        <label class="small text-muted">Tìm nhanh</label>
                                        <input type="text" name="q" value="{{ request('q', $keyword ?? '') }}"
                                            class="form-control" placeholder="Mã SV, Họ tên, SĐT, Email">
                                    </div>

                                    <div class="col-md-2 mb-3">
                                        <label class="small text-muted">Giới tính</label>
                                        <select name="gender" class="form-control">
                                            <option value="">-- Tất cả --</option>
                                            <option value="Nam" @selected(request('gender') == 'Nam')>Nam</option>
                                            <option value="Nữ" @selected(request('gender') == 'Nữ')>Nữ</option>
                                            <option value="Khác" @selected(request('gender') == 'Khác')>Khác</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="small text-muted">Tình trạng hồ sơ</label>
                                        <select name="status" class="form-control">
                                            <option value="">-- Tất cả --</option>
                                            <option value="Đã duyệt" @selected(request('status') == 'Đã duyệt')>Đã duyệt</option>
                                            <option value="Chờ duyệt" @selected(request('status') == 'Chờ duyệt')>Chờ duyệt</option>
                                        </select>
                                    </div>

                                    <div class="col-md-2 mb-3">
                                        <label class="small text-muted">Phòng</label>
                                        <select name="room_id" class="form-control">
                                            <option value="">-- Tất cả --</option>
                                            @isset($phongs)
                                                @foreach ($phongs as $p)
                                                    <option value="{{ $p->id }}" @selected(request('room_id') == $p->id)>
                                                        {{ $p->ten_phong ?? 'P' . $p->id }}
                                                    </option>
                                                @endforeach
                                            @endisset
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="small text-muted">Khu</label>
                                        <select name="khu" class="form-control">
                                            <option value="">-- Tất cả --</option>
                                            @isset($dsKhu)
                                                @foreach ($dsKhu as $k)
                                                    <option value="{{ $k }}" @selected(request('khu') == $k)>
                                                        {{ $k }}</option>
                                                @endforeach
                                            @endisset
                                        </select>
                                    </div>

                                    {{-- Hàng 2: Lớp – Ngành – Niên khóa --}}
                                    <div class="col-md-4 mb-3">
                                        <label class="small text-muted">Lớp</label>
                                        <input type="text" name="class_id" class="form-control"
                                            value="{{ request('class_id') }}" placeholder="VD: CNTT01">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="small text-muted">Ngành</label>
                                        <input type="text" name="major_id" class="form-control"
                                            value="{{ request('major_id') }}" placeholder="VD: CNTT">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="small text-muted">Niên khóa</label>
                                        <input type="text" name="intake_year" class="form-control"
                                            value="{{ request('intake_year') }}" placeholder="VD: 2022/K17">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <a href="{{ route('sinhvien.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
                            <button type="submit" class="btn btn-primary">Áp dụng</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <!-- Phân trang -->
        <div class="d-flex justify-content-center mt-3">
            {{ $sinhviens->onEachSide(1)->links() }}
        </div>
    </div>

    <!-- Modal chi tiết -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Thông tin sinh viên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalBody">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Mở modal bộ lọc sinh viên (chạy được cho cả Bootstrap 4 và 5)
            (function() {
                document.addEventListener('DOMContentLoaded', function() {
                    var btn = document.getElementById('openFilterModalBtn');
                    if (!btn) return;

                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        var modalEl = document.getElementById('filterModal');
                        if (!modalEl) return;

                        try {
                            // Nếu có Bootstrap 5
                            if (window.bootstrap && bootstrap.Modal) {
                                var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                                modal.show();
                            } else if (window.$ && $('#filterModal').modal) {
                                // Fallback cho Bootstrap 4
                                $('#filterModal').modal('show');
                            }
                        } catch (err) {
                            // Fallback cuối cùng
                            if (window.$ && $('#filterModal').modal) {
                                $('#filterModal').modal('show');
                            }
                        }
                    });
                });
            })();
        </script>

    <script>
            // Mở modal chi tiết sinh viên
            (function() {
                $(function() {
            $('.openModalBtn').on('click', function() {
                        var id = $(this).data('id');
                get_sinh_vien(id);
                        try {
                            var modalEl = document.getElementById('exampleModal');
                            var modal = window.bootstrap ? new bootstrap.Modal(modalEl) : null;
                            if (modal) {
                                modal.show();
                            } else {
                                $('#exampleModal').modal('show');
                            }
                        } catch (e) {
                $('#exampleModal').modal('show');
                        }
            });
        });

                window.get_sinh_vien = function(id) {
                    var url = `{{ route('sinhvien.show.modal', ['id' => ':id']) }}`.replace(':id', id);
            $.ajax({
                url: url,
                type: 'GET',
                        success: function(res) {
                            var response = res.data ?? '';
                    renderSinhvien(response);
                },
                        error: function(request) {
                            try {
                                var data = JSON.parse(request.responseText);
                                alert(data.message || 'Có lỗi xảy ra');
                            } catch (e) {
                                alert('Có lỗi xảy ra');
                            }
                        }
                    });
                };

                window.renderSinhvien = function(html) {
                    $('#modalBody').html(html);
                };
            })();
        </script>

        <script>
            // Dropdown custom cho nút răng cưa trong bảng sinh viên
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
