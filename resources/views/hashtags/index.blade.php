@extends('admin.layouts.admin')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

@section('title', 'Danh sách Hashtag')

@section('content')
<div class="container mt-4">

    {{-- Tiêu đề & mô tả --}}
    <div class="mb-4">
        <h3 class="room-page__title mb-2">🏷️ Danh sách Hashtag</h3>
        <p class="text-muted fs-6 mb-0">Theo dõi, thêm và chỉnh sửa các hashtag trong hệ thống.</p>
    </div>

    {{-- Nút thêm mới (ngay dưới tiêu đề giống trang vi phạm sinh viên) --}}
    <div class="d-flex gap-2 mb-3">
        <a href="{{ route('hashtags.create') }}" class="btn btn-dergin btn-dergin--info">
            <i class="fa fa-plus"></i><span>Thêm Hashtag</span>
        </a>
    </div>

    {{-- Ô tìm kiếm --}}
    <form method="GET" class="mb-4 search-bar">
        <div class="input-group shadow-sm">
            <input type="text" name="search" value="{{ request('search') ?? '' }}" class="form-control"
                placeholder="🔍 Tìm kiếm hashtag...">
            <button type="submit" class="btn btn-outline-secondary px-4">Tìm kiếm</button>
            @if (!empty(request('search')))
            <a href="{{ route('hashtags.index') }}" class="btn btn-outline-secondary px-4">Xóa</a>
            @endif
        </div>
    </form>

    {{-- Thông báo thành công --}}
    @if (session('success'))
    <div class="alert alert-success mt-2 shadow-sm rounded-pill px-4 py-2">{{ session('success') }}</div>
    @endif

    {{-- Bảng danh sách --}}
    <div class="room-table-wrapper mt-3">
        <div class="table-responsive">
            @php
            $perPage = $hashtags->perPage();
            $currentPage = $hashtags->currentPage();
            $sttBase = ($currentPage - 1) * $perPage;
            @endphp

            <table class="table table-hover mb-0 room-table align-middle">
                <thead>
                    <tr>
                        <th class="fit text-center">#</th>
                        <th class="fit text-center">Tên Hashtag</th>
                        <th class="fit text-center">Ngày tạo</th>
                        <th class="fit text-center">Ngày cập nhật</th>
                        <th class="fit text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($hashtags as $hashtag)
                    <tr>
                        <td class="text-center fw-semibold">{{ $sttBase + $loop->iteration }}</td>
                        <td class="fw-semibold text-primary text-center fw-semibold ">#{{ $hashtag->ten }}</td>
                        <td class="text-center text-muted">{{ optional($hashtag->created_at)->format('d/m/Y') ?? '---' }}</td>
                        <td class="text-center text-muted">{{ optional($hashtag->updated_at)->format('d/m/Y') ?? '---' }}</td>
                        <td class="fit text-center">
                            <div class="room-actions dropdown position-relative">
                                <button type="button" class="btn btn-dergin btn-dergin--muted action-gear">
                                    <i class="fa fa-gear"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('hashtags.edit', $hashtag->id) }}" class="dropdown-item">
                                            <i class="fa fa-pencil text-primary"></i> Sửa
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('hashtags.destroy', $hashtag->id) }}" method="POST" onsubmit="return confirm('Xác nhận xóa hashtag này?')">
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
                        <td colspan="5" class="text-center text-muted py-5">
                            <img src="https://dummyimage.com/120x80/eff3f9/9aa8b8&text=No+data" class="mb-3" alt="">
                            <div>Chưa có hashtag nào</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Phân trang --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $hashtags->onEachSide(1)->links() }}
    </div>
</div>

{{-- CSS cải thiện giao diện --}}
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
@push('scripts')
<script>
document.addEventListener('click', function(e){
    const menus = document.querySelectorAll('.room-actions .dropdown-menu');
    const gearBtn = e.target.closest('.action-gear');
    const insideMenu = e.target.closest('.room-actions .dropdown-menu');

    if(gearBtn){
        e.preventDefault();
        const wrapper = gearBtn.closest('.room-actions');
        const menu = wrapper.querySelector('.dropdown-menu');
        const isOpen = menu.classList.contains('show');
        menus.forEach(m => m.classList.remove('show'));
        if(!isOpen) menu.classList.add('show');
    } else if(!insideMenu){
        menus.forEach(m => m.classList.remove('show'));
    }
});
</script>
@endpush
