@extends('admin.layouts.admin')
@section('title', 'Tài sản cùng loại')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="container mt-4">

    <h4 class="mb-3">🔁 Tài sản loại: {{ $loai->ten_loai }}</h4>

    <a href="{{ route('kho.index') }}" class="btn btn-outline-dark" title="Quay về kho đồ">
        <i class="fa fa-warehouse"></i>
    </a>
    <a href="{{ route('kho.create', $loai->id) }}" class="btn btn-primary me-2">
        <i class="fa fa-plus"></i> Thêm tài sản mới
    </a>
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Form lọc --}}
    <form action="{{ route('kho.related', $loai->id) }}" method="GET" class="mb-3">
        <div class="row g-2">
            <div class="col-md-3">
                <input type="text" name="ma_tai_san" class="form-control"
                    placeholder="Tìm theo mã tài sản"
                    value="{{ request('ma_tai_san') }}">
            </div>
            <div class="col-md-3">
                <select name="tinh_trang" class="form-select form-control">
                    <option value="">-- Chọn tình trạng --</option>
                    @foreach(['Mới', 'Hỏng', 'Cũ', 'Bảo trì', 'Bình thường'] as $status)
                    <option value="{{ $status }}" {{ request('tinh_trang') == $status ? 'selected' : '' }}>
                        {{ $status }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-filter"></i> Lọc
                </button>
                <a href="{{ route('kho.related', $loai->id) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i> Làm mới
                </a>
            </div>
        </div>
    </form>

    {{-- Bảng tài sản --}}
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-bordered table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Hình ảnh</th>
                        <th>Mã tài sản</th>
                        <th>Tên tài sản</th>
                        <th>Tình trạng</th>
                        <th>Số lượng</th>
                        <th>Đơn vị</th>
                        <th>Ghi chú</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($taiSan as $item)
                    <tr>
                        <td>{{ $taiSan->firstItem() + $loop->index }}</td>
                        <td class="text-center">
                            @if($item->hinh_anh)
                            <img src="{{ asset('storage/' . $item->hinh_anh) }}"
                                alt="{{ $item->ten_tai_san }}"
                                style="width:70px;height:70px;object-fit:cover;border-radius:8px;border:1px solid #ddd;">
                            @else
                            <div class="bg-light text-muted d-flex align-items-center justify-content-center border rounded"
                                style="width:70px;height:70px;">
                                <small>Chưa có hình</small>
                            </div>
                            @endif
                        </td>
                        <td>{{ $item->ma_tai_san }}</td>
                        <td>{{ $item->ten_tai_san }}</td>
                        <td>{{ $item->tinh_trang ?? '-' }}</td>
                        <td>{{ $item->so_luong }}</td>
                        <td>{{ $item->don_vi_tinh ?? '-' }}</td>
                        <td>{{ $item->ghi_chu ?? '-' }}</td>
                        <td class="text-end kho-actions">
                            <a href="{{ route('kho.edit', $item->id) }}"
                                class="btn btn-outline-primary btn-action" title="Sửa">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <form action="{{ route('kho.destroy', $item->id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Bạn có chắc muốn xóa tài sản này không?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-action" title="Xóa">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-3">Chưa có tài sản nào.</td>
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
    .kho-actions .btn-action {
        width: 40px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
    }

    .kho-actions .btn-action i {
        font-size: 14px;
    }
</style>
@endpush
@endsection