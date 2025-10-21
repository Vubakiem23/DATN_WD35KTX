@extends('admin.layouts.admin')

@section('title', 'Kho tài sản')

@section('content')
<style>
    .pagination-info,
    .small.text-muted {
        display: none !important;
    }
</style>

<div class="container-fluid">

    {{-- 🔎 Thanh công cụ tìm kiếm và thêm --}}
    <form method="GET" action="{{ route('kho.index') }}" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                placeholder="Tìm theo mã hoặc tên tài sản...">
        </div>
        <div class="col-md-2 text-end d-flex align-items-center justify-content-end">
            <div>
                <button type="submit" class="btn btn-secondary me-2">Tìm kiếm</button>
                <a href="{{ route('kho.create') }}" class="btn btn-primary">+ Thêm</a>
            </div>
        </div>
    </form>

    {{-- 🔔 Thông báo --}}
    @if(session('success'))

    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- 🧾 Bảng danh sách --}}
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Mã tài sản</th>
                    <th>Tên tài sản</th>
                    <th>Ảnh</th>
                    <th>Đơn vị tính</th>
                    <th>Số lượng</th>
                    <th>Ghi chú</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kho as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->ma_tai_san }}</td>
                    <td>{{ $item->ten_tai_san }}</td>
                    <td>
                        @if($item->hinh_anh)
                        <img src="{{ asset('uploads/kho/'.$item->hinh_anh) }}" alt="Ảnh" width="80" class="rounded">
                        @else
                        <span class="badge bg-secondary">Không có</span>
                        @endif
                    </td>
                    <td>{{ $item->don_vi_tinh ?? '-' }}</td>
                    <td >
                        @if($item->so_luong == 0)
                        <span class="badge bg-danger">Không còn</span>
                        @else
                        <span class="badge bg-info">{{ $item->so_luong }}</span>
                        @endif
                    </td>

                    <td>{{ $item->ghi_chu ?? '-' }}</td>
                    <td>
                        <a href="{{ route('kho.edit', $item->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                        <form action="{{ route('kho.destroy', $item->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Xóa tài sản này khỏi kho?')" class="btn btn-sm btn-danger">Xóa</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">Chưa có tài sản nào trong kho</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-3">
            {{ $kho->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>

</div>
@endsection