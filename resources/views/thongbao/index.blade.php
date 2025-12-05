@extends('admin.layouts.admin')
@section('title', 'Danh sách thông báo')
@section('content')
<div class="container mt-4">

    {{-- Tiêu đề --}}
    <div class="mb-4">
        <h3 class="room-page__title mb-2">📢 Danh sách thông báo</h3>
        <p class="text-muted mb-0">Theo dõi toàn bộ thông báo, mức độ, phòng/khu và người viết.</p>
    </div>

    {{-- Nút thêm (ngay dưới tiêu đề giống trang vi phạm sinh viên) --}}
    <div class="d-flex gap-2 mb-3">
        <a href="{{ route('thongbao.create') }}" class="btn btn-dergin btn-dergin--info">
            <i class="fa fa-plus"></i><span>Thêm thông báo</span>
        </a>
    </div>

    {{-- Tìm kiếm & Bộ lọc --}}
    <form method="GET" class="mb-3 search-bar">
        <div class="input-group">
            <input type="text" name="search" value="{{ request('search') ?? '' }}" class="form-control"
                placeholder="Tìm kiếm tiêu đề, nội dung, phòng, khu">
            <button type="submit" class="btn btn-outline-secondary">Tìm kiếm</button>
            @if (!empty(request('search')))
            <a href="{{ route('thongbao.index') }}" class="btn btn-outline-secondary">Xóa</a>
            @endif
            <button type="button" class="btn btn-outline-primary" id="openFilterModalBtn">
                <i class="fa fa-filter mr-1"></i> Bộ lọc
            </button>
        </div>
    </form>

    {{-- Bảng danh sách --}}
    <div class="room-table-wrapper">
        <div class="table-responsive">
            @php
            $perPage = $thongbaos->perPage();
            $currentPage = $thongbaos->currentPage();
            $sttBase = ($currentPage - 1) * $perPage;
            @endphp

            <table class="table table-hover mb-0 room-table">
                <thead>
                    <tr>
                        <th class="fit text-center">STT</th>
                        <th>Tiêu đề</th>
                        <th class="fit text-center">Ảnh</th>
                        <th class="fit text-center">Ngày đăng</th>
                        <th>Đối tượng</th>
                        <th class="fit text-center">Mức độ</th>
                        <th>Phòng</th>
                        <th>Khu</th>
                        <th>File</th>
                        <th>Người viết</th>
                        <th class="fit text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($thongbaos as $tb)
                    @php
                    $stt = $sttBase + $loop->iteration;
                    $mucDo = $tb->mucDo->ten_muc_do ?? '';
                    $badgeClass = match($mucDo) {
                    'Cao' => 'badge-soft-danger',
                    'Trung bình' => 'badge-soft-warning',
                    default => 'badge-soft-secondary',
                    };
                    $imgUrl = $tb->anh ? Storage::url($tb->anh) : asset('images/default-avatar.png');
                    @endphp
                    <tr>
                        <td class="fit text-center">{{ $stt }}</td>
                        <td>{{ $tb->tieuDe->ten_tieu_de ?? '---' }}</td>
                        <td class="fit text-center">
                            <img src="{{ $imgUrl }}" alt="Ảnh {{ $tb->tieuDe->ten_tieu_de ?? '' }}" class="avatar-56">
                        </td>
                        <td class="fit text-center">{{ \Carbon\Carbon::parse($tb->ngay_dang)->format('d/m/Y') }}</td>
                        <td>{{ $tb->doi_tuong ?? '---' }}</td>
                        <td class="fit text-center">
                            <span class="badge {{ $badgeClass }}">{{ $mucDo ?: '---' }}</span>
                        </td>
                        <td>
                            @if ($tb->phongs->isEmpty())
                            ---
                            @else
                            {{ $tb->phongs->first()->ten_phong }}
                            @if ($tb->phongs->count() > 1)
                            , ...
                            @endif
                            @endif
                        </td>

                        <td>
                            @if ($tb->khus->isEmpty())
                            ---
                            @else
                            {{ $tb->khus->first()->ten_khu }}
                            @if ($tb->khus->count() > 1)
                            , ...
                            @endif
                            @endif
                        </td>

                        <td>
                            @if($tb->file)
                            <a href="{{ Storage::url($tb->file) }}" target="_blank"><i class="fa fa-download"></i></a>
                            @else
                            <span class="text-muted">Không có</span>
                            @endif
                        </td>
                        <td>{{ $tb->user->name ?? '---' }}</td>
                        <td class="fit text-center">
                            <div class="room-actions dropdown position-relative">
                                <button type="button" class="btn btn-dergin btn-dergin--muted action-gear">
                                    <i class="fa fa-gear"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('thongbao.show', $tb->id) }}" class="dropdown-item">
                                            <i class="fa fa-eye text-muted"></i> Xem chi tiết
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('thongbao.edit', $tb->id) }}" class="dropdown-item">
                                            <i class="fa fa-pencil text-primary"></i> Sửa
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('thongbao.destroy', $tb->id) }}" method="POST"
                                            onsubmit="return confirm('Xác nhận xóa thông báo này?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fa fa-trash"></i> Xóa
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center py-4 text-muted">
                            <img src="https://dummyimage.com/120x80/eff3f9/9aa8b8&text=No+data" class="mb-2">
                            <div>Chưa có thông báo</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Phân trang --}}
    <div class="d-flex justify-content-center mt-3">
        {{ $thongbaos->onEachSide(1)->links() }}
    </div>
</div>

{{-- Modal chi tiết --}}
<div class="modal fade" id="thongBaoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết thông báo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Modal chi tiết
    $(document).ready(function() {
        $('.openModalBtn').click(function() {
            let id = $(this).data('id');
            let url = `{{ route('thongbao.show', ':id') }}`.replace(':id', id);
            $.get(url, function(res) {
                $('#modalBody').html(res);
                $('#thongBaoModal').modal('show');
            }).fail(function() {
                $('#modalBody').html('<p class="text-danger text-center py-3">Không thể tải chi tiết thông báo.</p>');
                $('#thongBaoModal').modal('show');
            });
        });
    });

    // Dropdown răng cưa
    document.addEventListener('click', function(e) {
        const menus = document.querySelectorAll('.room-actions .dropdown-menu');
        const gearBtn = e.target.closest('.action-gear');
        const insideMenu = e.target.closest('.room-actions .dropdown-menu');

        if (gearBtn) {
            e.preventDefault();
            const wrapper = gearBtn.closest('.room-actions');
            const menu = wrapper.querySelector('.dropdown-menu');
            const isOpen = menu.classList.contains('show');
            menus.forEach(m => m.classList.remove('show'));
            if (!isOpen) menu.classList.add('show');
        } else if (!insideMenu) {
            menus.forEach(m => m.classList.remove('show'));
        }
    });
</script>
@endpush
@endsection
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

{{-- MODAL BỘ LỌC --}}
<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Bộ lọc thông báo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <form method="GET" id="filterForm">
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="small text-muted">Tìm kiếm</label>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    class="form-control" placeholder="Tiêu đề, nội dung, phòng, khu">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="small text-muted">Mức độ</label>
                                <select name="muc_do" class="form-control">
                                    <option value="">-- Tất cả --</option>
                                    @if(isset($mucDos))
                                    @foreach($mucDos as $md)
                                    <option value="{{ $md->id }}" @selected(request('muc_do')==$md->id)>
                                        {{ $md->ten_muc_do }}
                                    </option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="small text-muted">Đối tượng</label>
                                <select name="doi_tuong" class="form-control">
                                    <option value="">-- Tất cả --</option>
                                    <option value="Sinh viên" @selected(request('doi_tuong')=='Sinh viên' )>Sinh viên</option>
                                    <option value="Tất cả" @selected(request('doi_tuong')=='Tất cả' )>Tất cả</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="{{ route('thongbao.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
                    <button type="submit" class="btn btn-primary">Áp dụng</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Mở modal bộ lọc thông báo (chạy được cho cả Bootstrap 4 và 5)
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
@endpush