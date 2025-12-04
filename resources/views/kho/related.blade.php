@extends('admin.layouts.admin')
@section('title', 'Tài sản cùng loại')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="container mt-4">

    <h3 class="asset-page__title mb-3">📦 Tài sản loại: {{ $loai->ten_loai }}</h3>

    <div class="mb-3 d-flex gap-2">
        <a href="{{ route('kho.index') }}" class="btn btn-dergin btn-dergin--muted" title="Quay về kho đồ">
            <i class="fa fa-warehouse"></i><span>Kho đồ</span>
    </a>
        <a href="{{ route('kho.create', $loai->id) }}" class="btn btn-dergin btn-dergin--info">
            <i class="fa fa-plus"></i><span>Thêm tài sản mới</span>
    </a>
    </div>
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Form lọc --}}
    <div class="filter-card mb-3">
        <form action="{{ route('kho.related', $loai->id) }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label"><i class="fa fa-magnifying-glass text-primary"></i> Tìm theo mã tài sản</label>
                <input type="text" name="ma_tai_san" class="form-control"
                    placeholder="Nhập mã..."
                    value="{{ request('ma_tai_san') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label"><i class="fa fa-circle-info text-primary"></i> Tình trạng</label>
                <select name="tinh_trang" class="form-select form-control">
                    <option value="">-- Chọn tình trạng --</option>
                    @foreach(['Mới', 'Hỏng', 'Cũ', 'Bảo trì', 'Bình thường'] as $status)
                    <option value="{{ $status }}" {{ request('tinh_trang') == $status ? 'selected' : '' }}>
                        {{ $status }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2 filter-btns">
                <button type="submit" class="btn btn-success flex-fill">
                    <i class="fa fa-filter"></i> Lọc
                </button>
                @if(request('ma_tai_san') || request('tinh_trang'))
                <a href="{{ route('kho.related', $loai->id) }}" class="btn btn-outline-secondary flex-fill">
                    <i class="fa fa-rotate-left"></i> Làm mới
                </a>
                @endif
            </div>
        </form>
        </div>

    {{-- Bảng tài sản --}}
    <div class="asset-table-wrapper">
        <div class="table-responsive">
            <table class="table align-middle asset-table mb-0">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Hình ảnh</th>
                        <th>Mã tài sản</th>
                        <th>Tên tài sản</th>
                        <th>Tình trạng</th>
                        <th>Vị trí</th>
                        <th>Đơn vị</th>
                        <th>Ghi chú</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($taiSan as $item)
                    <tr class="asset-row">
                        <td class="text-center">{{ $taiSan->firstItem() + $loop->index }}</td>
                        <td class="text-center asset-thumb-cell">
                            @if($item->hinh_anh)
                            <div class="asset-thumb mx-auto">
                            <img src="{{ asset('storage/' . $item->hinh_anh) }}"
                                    alt="{{ $item->ten_tai_san }}">
                            </div>
                            @else
                            <div class="asset-thumb mx-auto bg-light text-muted d-flex align-items-center justify-content-center">
                                <small class="small">Không ảnh</small>
                            </div>
                            @endif
                        </td>
                        <td>{{ $item->ma_tai_san }}</td>
                        <td>{{ $item->ten_tai_san }}</td>
                        <td>{{ $item->tinh_trang ?? '-' }}</td>
                        <td>
                            @php
                            // Lấy danh sách phòng từ các bản ghi tài sản con đã gán phòng
                            $roomNames = optional($item->taiSans)
                            ->whereNotNull('phong_id')
                            ->pluck('phong.ten_phong')
                            ->filter()
                            ->unique()
                            ->values();
                            @endphp
                            {{ ($roomNames && $roomNames->count() > 0) ? $roomNames->join(', ') : 'Chưa gán phòng' }}
                        </td>


                        <td>{{ $item->don_vi_tinh ?? '-' }}</td>
                        <td>{{ $item->ghi_chu ?? '-' }}</td>
                        <td class="action-cell">
                            <a href="{{ route('kho.edit', $item->id) }}" class="btn btn-dergin" title="Sửa">
                                <i class="fa fa-pencil"></i><span>Sửa</span>
                            </a>
                            <form action="{{ route('kho.destroy', $item->id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Bạn có chắc muốn xóa tài sản này không?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-dergin btn-dergin--danger" title="Xóa">
                                    <i class="fa fa-trash"></i><span>Xóa</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-3">Chưa có tài sản nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Phân trang --}}
    <div class="d-flex justify-content-center mt-3">
        {{ $taiSan->links('pagination::bootstrap-5') }}
    </div>
</div>

{{-- Style giống trang khu --}}
@push('styles')
<style>
    .asset-page__title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
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

    .asset-table-wrapper {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        padding: 1.25rem
    }

    .asset-table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0 12px
    }

    .asset-table thead th {
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #6c757d;
        border: none;
        padding-bottom: .75rem
    }

    .asset-table tbody tr {
        background: #f9fafc;
        border-radius: 16px;
        transition: transform .2s ease, box-shadow .2s ease
    }

    .asset-table tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08)
    }

    .asset-table tbody td {
        border: none;
        vertical-align: middle;
        padding: 1rem .95rem
    }

    .asset-table tbody tr td:first-child {
        border-top-left-radius: 16px;
        border-bottom-left-radius: 16px
    }

    .asset-table tbody tr td:last-child {
        border-top-right-radius: 16px;
        border-bottom-right-radius: 16px
    }

    .asset-thumb-cell {
        width: 96px
    }

    .asset-thumb {
        width: 64px;
        height: 64px;
        border-radius: 14px;
        overflow: hidden;
        flex: 0 0 64px;
        background: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center
    }

    .asset-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover
    }

    .action-cell {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: .5rem;
        white-space: nowrap
    }

    .action-cell form {
        margin: 0
    }

    .action-cell .btn {
        line-height: 1
    }

    .action-cell .btn-dergin {
        min-width: 92px
    }

    .action-cell .btn-dergin span {
        line-height: 1;
        white-space: nowrap
    }

    .filter-card {
        background: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 12px;
        padding: 15px 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .08)
    }

    .filter-card .form-label {
        display: block;
        font-weight: 600;
        color: #333;
        font-size: .9rem;
        line-height: 1.3;
        height: auto;
        margin-bottom: .35rem;
        overflow: visible;
        white-space: normal;
    }

    .filter-btns .btn {
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center
    }

    .filter-btns i {
        margin-right: 5px
    }
</style>
@endpush
@endsection