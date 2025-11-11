@extends('admin.layouts.admin')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

@section('title', 'Danh sách thông báo')

@section('content')
<div class="container mt-4">

    {{-- Tiêu đề & mô tả --}}
    <div class="mb-4">
        <h3 class="room-page__title mb-2">📢 Danh sách thông báo</h3>
        <p class="text-muted fs-6 mb-0">Theo dõi toàn bộ thông báo, mức độ, phòng/khu và người viết.</p>
    </div>

    {{-- Ô tìm kiếm nhanh --}}
    <form method="GET" class="mb-3 search-bar">
        <div class="input-group">
            <input type="text" name="search" value="{{ request('search') ?? '' }}" class="form-control"
                placeholder="Tìm kiếm (tiêu đề, nội dung, phòng, khu, đối tượng)">
            <button type="submit" class="btn btn-outline-secondary">Tìm kiếm</button>
            @if (!empty(request('search')))
            <a href="{{ route('thongbao.index') }}" class="btn btn-outline-secondary">Xóa</a>
            @endif
            <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#filterModal">
                <i class="fa fa-filter mr-1"></i> Bộ lọc
            </button>
        </div>
    </form>

    <div class="d-flex gap-2">
        <a href="{{ route('thongbao.create') }}" class="btn btn-dergin btn-dergin--info"><i class="fa fa-plus"></i><span>Thêm thông báo</span></a>
    </div>

    {{-- Thông báo thành công --}}
    @if (session('success'))
    <div class="alert alert-success mt-2 shadow-sm rounded-pill px-4 py-2">{{ session('success') }}</div>
    @endif

    {{-- Bảng danh sách --}}
    <div class="room-table-wrapper mt-3">
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
                        <th class="fit">Tiêu đề</th>
                        <th class="fit">Nội dung</th>
                        <th class="fit">Ảnh</th>
                        <th class="fit text-center">Ngày đăng</th>
                        <th class="fit">Đối tượng</th>
                        <th class="fit text-center">Mức độ</th>
                        <th class="fit">Phòng</th>
                        <th class="fit">Khu</th>
                        <th class="fit">File</th>
                        <th class="fit">Người viết</th>
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
                    @endphp
                    <tr>
                        <td class="text-center">{{ $stt }}</td>
                        <td class="fw-semibold">{{ $tb->tieuDe->ten_tieu_de ?? '---' }}</td>
                        <td>
                            {{ \Illuminate\Support\Str::limit(strip_tags($tb->noi_dung ?? ''), 20, '...') }}
                            <a href="#" class="openModalBtn" data-id="{{ $tb->id }}">Xem thêm</a>
                        </td>
                        <td>
                            @if ($tb->anh)
                            <img src="{{ Storage::url($tb->anh) }}" class="img-thumb" alt="Ảnh #{{ $tb->id }}">
                            @else
                            <div class="img-placeholder"><i class="fa fa-image"></i></div>
                            @endif
                        </td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($tb->ngay_dang)->format('d/m/Y') }}</td>
                        <td>{{ $tb->doi_tuong ?? '---' }}</td>
                        <td class="text-center"><span class="badge {{ $badgeClass }}">{{ $mucDo ?: '---' }}</span></td>
                        <td>{{ $tb->phongs->pluck('ten_phong')->join(', ') ?: '---' }}</td>
                        <td>{{ $tb->khus->pluck('ten_khu')->join(', ') ?: '---' }}</td>
                        <td>
                            @if($tb->file)
                            <a href="{{ Storage::url($tb->file) }}" target="_blank" class="text-primary">
                                <i class="fa fa-download"></i> Tải
                            </a>
                            @else
                            <span class="text-muted">Không có</span>
                            @endif
                        </td>
                        <td>{{ $tb->user->name ?? '---' }}</td>
                        <!-- <td class="text-center">
                            <div class="btn-group">
                                <button type="button" data-id="{{ $tb->id }}" class="btn btn-sm btn-secondary openModalBtn">Xem</button>
                                <a href="{{ route('thongbao.edit', $tb->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                                <form action="{{ route('thongbao.destroy', $tb->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Xác nhận xóa thông báo này?')">Xóa</button>
                                </form>
                            </div>
                        </td> -->
                        <td class="text-end fit">
                            <div class="room-actions">
                                {{-- Xem chi tiết --}}
                                <a href="{{ route('thongbao.show', $tb->id) }}"class="btn btn-dergin btn-dergin--muted"title="Xem chi tiết">
                                    <i class="fa fa-eye"></i><span>Chi tiết</span>
                                </a>
                                {{-- Sửa --}}
                                <a href="{{ route('thongbao.edit', $tb->id) }}" class="btn btn-dergin" title="Sửa">
                                    <i class="fa fa-pencil"></i><span>Sửa</span>
                                </a>

                                {{-- Xóa --}}
                                <form action="{{ route('thongbao.destroy', $tb->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xác nhận xóa thông báo này?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-dergin btn-dergin--danger" title="Xóa">
                                        <i class="fa fa-trash"></i><span>Xóa</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="text-center text-muted py-4">
                            <img src="https://dummyimage.com/120x80/eff3f9/9aa8b8&text=No+data" class="mb-2" alt="">
                            <div>Chưa có thông báo nào</div>
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
{{-- Script Modal chi tiết --}}
<script>
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
</script>

@push('styles')
<style>
    html {
        scroll-behavior: auto !important
    }

    .room-page__title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1f2937
    }

    .room-table-wrapper {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        padding: 1.25rem
    }

    .room-table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0 12px
    }

    .room-table thead th {
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #6c757d;
        border: none;
        padding-bottom: .75rem
    }

    .room-table tbody tr {
        background: #f9fafc;
        border-radius: 16px;
        transition: transform .2s ease, box-shadow .2s ease
    }

    .room-table tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08)
    }

    .room-table tbody td {
        border: none;
        vertical-align: middle;
        padding: 1rem .95rem
    }

    .room-table tbody tr td:first-child {
        border-top-left-radius: 16px;
        border-bottom-left-radius: 16px
    }

    .room-table tbody tr td:last-child {
        border-top-right-radius: 16px;
        border-bottom-right-radius: 16px
    }

    .room-actions{display:flex;flex-wrap:wrap;justify-content:flex-end;gap:.4rem}
    .room-actions .btn-dergin{min-width:80px}
    .room-actions .btn-dergin span{line-height:1;white-space:normal}

    .img-thumb {
        height: 60px;
        width: 60px;
        object-fit: cover;
        border-radius: 5px;
    }

    .img-placeholder {
        height: 60px;
        width: 60px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 5px;
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
        transition: transform .2s ease, box-shadow .2s ease
    }

    .btn-dergin:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(78, 84, 200, .32);
        color: #fff
    }

    .btn-dergin i {
        font-size: .8rem
    }

    .btn-dergin--muted {
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%)
    }

    .btn-dergin--info {
        background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%)
    }

    .btn-dergin--danger {
        background: linear-gradient(135deg, #f43f5e 0%, #ef4444 100%)
    }

    .avatar-56 {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        object-fit: cover
    }

    @media (max-width:1400px){
        .room-actions .btn-dergin{min-width:72px;padding:.35rem .7rem}
    }
    @media (max-width:992px) {
        .room-table thead {
            display: none
        }

        .room-table tbody {
            display: block
        }

        .room-table tbody tr {
            display: flex;
            flex-direction: column;
            padding: 1rem
        }

        .room-table tbody td {
            display: flex;
            justify-content: space-between;
            padding: .35rem 0
        }
        .room-actions{justify-content:flex-start}
    }
</style>
@endpush
@endsection