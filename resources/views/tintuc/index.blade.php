@extends('admin.layouts.admin')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

@section('title', 'Danh sách tin tức')

@section('content')
<div class="container mt-4">

    {{-- Tiêu đề & mô tả --}}
    <div class="mb-4">
        <h3 class="room-page__title mb-2">📰 Danh sách tin tức</h3>
        <p class="text-muted fs-6 mb-0">Theo dõi toàn bộ tin tức, hashtags, ngày đăng và hình ảnh.</p>
    </div>

    {{-- Ô tìm kiếm --}}
    <form method="GET" class="mb-3 search-bar">
        <div class="input-group">
            <input type="text" name="search" value="{{ request('search') ?? '' }}" class="form-control"
                placeholder="Tìm kiếm (tiêu đề, nội dung, hashtags)">
            <button type="submit" class="btn btn-outline-secondary">Tìm kiếm</button>
            @if (!empty(request('search')))
            <a href="{{ route('tintuc.index') }}" class="btn btn-outline-secondary">Xóa</a>
            @endif
        </div>
    </form>

    <div class="d-flex gap-2 mb-3">
        <a href="{{ route('tintuc.create') }}" class="btn btn-dergin btn-dergin--info">
            <i class="fa fa-plus"></i><span>Thêm tin tức</span>
        </a>
    </div>

    {{-- Thông báo thành công --}}
    @if (session('success'))
    <div class="alert alert-success mt-2 shadow-sm rounded-pill px-4 py-2">
        {{ session('success') }}
    </div>
    @endif

    {{-- Bảng danh sách --}}
    <div class="room-table-wrapper mt-3">
        <div class="table-responsive">
            @php
            $perPage = $tintucs->perPage();
            $currentPage = $tintucs->currentPage();
            $sttBase = ($currentPage - 1) * $perPage;
            @endphp

            <table class="table table-hover mb-0 room-table">
                <thead>
                    <tr>
                        <th class="fit text-center">STT</th>
                        <th class="fit">Hình ảnh</th>
                        <th class="fit">Tiêu đề</th>
                        <th class="fit">Nội dung</th>
                        <th class="fit">Ngày tạo</th>
                        <th class="fit">Hashtags</th>
                        <th class="fit text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tintucs as $tintuc)
                    @php
                    $stt = $sttBase + $loop->iteration;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $stt }}</td>

                        {{-- Cột hình ảnh --}}
                        <td class="fit text-center">
                            @if($tintuc->hinh_anh && file_exists(public_path($tintuc->hinh_anh)))
                                <img src="{{ asset($tintuc->hinh_anh) }}" alt="Ảnh tin tức" style="width:80px; height:60px; object-fit:cover; border-radius:6px;">
                            @else
                                <img src="https://dummyimage.com/80x60/eff3f9/9aa8b8&text=No+Image" alt="No Image" style="border-radius:6px;">
                            @endif
                        </td>

                        <td class="fw-semibold">{{ $tintuc->tieu_de }}</td>
                        <td>
                            {{ \Illuminate\Support\Str::limit(strip_tags($tintuc->noi_dung ?? ''), 50, '...') }}
                            <a href="{{ route('tintuc.show', $tintuc->id) }}">Xem thêm</a>
                        </td>
                        <td class="text-center">{{ $tintuc->created_at?->format('d/m/Y') ?? '---' }}</td>
                        <td>
                            @foreach($tintuc->hashtags as $hashtag)
                            <span class="badge bg-secondary">{{ $hashtag->ten }}</span>
                            @endforeach
                        </td>
                        <td class="fit text-center">
                            <div class="room-actions dropdown position-relative">
                                <button type="button" class="btn btn-dergin btn-dergin--muted action-gear">
                                    <i class="fa fa-gear"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('tintuc.show', $tintuc->id) }}" class="dropdown-item">
                                            <i class="fa fa-eye text-muted"></i> Chi tiết
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('tintuc.edit', $tintuc->id) }}" class="dropdown-item">
                                            <i class="fa fa-pencil text-primary"></i> Sửa
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('tintuc.destroy', $tintuc->id) }}" method="POST" onsubmit="return confirm('Xác nhận xóa tin tức này?')">
                                            @csrf
                                            @method('DELETE')
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
                        <td colspan="7" class="text-center text-muted py-4">
                            <img src="https://dummyimage.com/120x80/eff3f9/9aa8b8&text=No+data" class="mb-2" alt="">
                            <div>Chưa có tin tức nào</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Phân trang --}}
    <div class="d-flex justify-content-center mt-3">
        {{ $tintucs->onEachSide(1)->links() }}
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
@endsection
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