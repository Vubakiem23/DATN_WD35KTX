@extends('admin.layouts.admin')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

@section('title', 'Kho tài sản')

@section('content')
<div class="container mt-4">

    <h3 class="page-title">🏢 Danh sách tài sản trong kho</h3>

    {{-- 🔍 Thanh tìm kiếm --}}
    <form method="GET" action="{{ route('kho.index') }}" class="mb-3 search-bar">
        <div class="input-group">
            <input type="text" name="search" value="{{ request('search') }}"
                class="form-control" placeholder="Tìm theo mã hoặc tên tài sản...">
            <button type="submit" class="btn btn-outline-secondary">Tìm kiếm</button>
            @if(request('search'))
            <a href="{{ route('kho.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
            @endif
        </div>
    </form>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Danh sách tài sản trong kho</h4>
        <a href="{{ route('kho.create') }}" class="btn btn-primary">+ Thêm tài sản</a>
    </div>

    {{-- 🔔 Thông báo --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- 🧱 Danh sách thẻ --}}
    <div class="row g-3">
        @forelse($kho as $item)
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">

                {{-- Header --}}
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>{{ $item->ten_tai_san }}</strong>
                    <span class="text-muted small">{{ $item->ma_tai_san }}</span>
                </div>

                {{-- Ảnh --}}
                @if($item->hinh_anh)
                <img src="{{ asset('uploads/kho/'.$item->hinh_anh) }}"
                    alt="{{ $item->ten_tai_san }}"
                    class="card-img-top"
                    style="height:180px;object-fit:cover">
                @else
                <div class="card-img-top d-flex align-items-center justify-content-center"
                    style="height:180px;background:#f8f9fa">
                    <svg width="80" height="60" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <rect width="24" height="24" rx="2" fill="#e9ecef" />
                        <path d="M3 15L8 9L13 15L21 6" stroke="#adb5bd" stroke-width="1.2"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                @endif

                {{-- Nội dung --}}
                <div class="card-body">
                    <p class="mb-1"><strong>Đơn vị tính:</strong> {{ $item->don_vi_tinh ?? '-' }}</p>
                    <p class="mb-1">
                        <strong>Số lượng:</strong>
                        @if($item->so_luong == 0)
                        <span class="badge bg-danger">Không còn</span>
                        @else
                        <span class="badge bg-success">{{ $item->so_luong }}</span>
                        @endif
                    </p>
                    <p class="mb-1"><strong>Ghi chú:</strong> {{ $item->ghi_chu ?? '-' }}</p>
                </div>

                {{-- Footer hành động --}}
                <div class="card-footer d-flex gap-2">
                    <a href="{{ route('kho.edit', $item->id) }}"
                        class="btn btn-sm btn-warning flex-fill">Sửa</a>
                    <button class="btn btn-sm btn-info flex-fill btn-xem-chi-tiet" data-id="{{ $item->id }}">
                        Xem chi tiết
                    </button>

                    <form action="{{ route('kho.destroy', $item->id) }}"
                        method="POST" class="mb-0 flex-fill"
                        style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger w-100"
                            onclick="return confirm('Xóa tài sản này khỏi kho?')">
                            Xóa
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center text-muted py-4">
            Chưa có tài sản nào trong kho
        </div>
        @endforelse
    </div>

    {{-- 📄 Phân trang --}}
    <div class="d-flex justify-content-center mt-3">
        {{ $kho->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
</div>
<div id="modalContainer"></div>

<script>
    $(document).ready(function() {
        $('.btn-xem-chi-tiet').click(function() {
            var id = $(this).data('id');

            $.ajax({
                url: '{{ url("admin/kho/show") }}/' + id, // thêm 'admin' vào đúng prefix
                type: 'GET',
                success: function(res) {
                    $('#modalContainer').html(res.data);
                    $('#modalKho').modal('show');
                },
                error: function(xhr) {
                    console.log(xhr.responseText); // xem lỗi chi tiết trong console
                    alert('Không thể tải dữ liệu.');
                }
            });

        });
    });
</script>
@endsection