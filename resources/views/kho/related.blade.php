@extends('admin.layouts.admin')
@section('title', 'Tài sản cùng loại')

@section('content')
<div class="container">
    <h4>🔁 Tài sản loại: {{ $loai->ten_loai }}</h4>

    <a href="{{ route('kho.create', $loai->id) }}" class="btn btn-primary mb-3">➕ Thêm tài sản mới</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Hình ảnh</th>
                <th>Mã tài sản</th>
                <th>Tên tài sản</th>
                <th>Tình trạng</th>
                <th>Số lượng</th>
                <th>Đơn vị</th>
                <th>Ghi chú</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($taiSan as $item)
                <tr>
                    <td class="text-center">
                        @if($item->hinh_anh)
                            <div class="thumbnail-container">
                                <img src="{{ asset('storage/' . $item->hinh_anh) }}" alt="{{ $item->ten_tai_san }}" class="thumbnail-img">
                            </div>
                        @else
                            <span>Chưa có hình</span>
                        @endif
                    </td>
                    <td>{{ $item->ma_tai_san }}</td>
                    <td>{{ $item->ten_tai_san }}</td>
                    <td>{{ $item->tinh_trang ?? '-' }}</td>
                    <td>{{ $item->so_luong }}</td>
                    <td>{{ $item->don_vi_tinh ?? '-' }}</td>
                    <td>{{ $item->ghi_chu ?? '-' }}</td>
                    <td>
                        <a href="{{ route('kho.edit', $item->id) }}" class="btn btn-warning btn-sm mb-1">✏️ Sửa</a>
                        <form action="{{ route('kho.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa?');" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">🗑️ Xóa</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Chưa có tài sản nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $taiSan->links() }}
    </div>
</div>

<style>
/* Thumbnail hình ảnh nhỏ, hover phóng to */
.thumbnail-container {
    width: 150px;
    height: 120px;
    overflow: hidden;
    border-radius: 5px;
    margin: auto;
}

.thumbnail-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
    cursor: pointer;
}

.thumbnail-img:hover {
    transform: scale(2);
    z-index: 10;
    position: relative;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
}
</style>
@endsection
