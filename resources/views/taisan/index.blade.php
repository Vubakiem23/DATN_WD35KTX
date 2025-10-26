@extends('admin.layouts.admin')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

@section('title', 'Quản lý tài sản phòng')

@section('content')
<div class="container mt-4">

    <h3 class="page-title">🏢 Quản lý tài sản phòng</h3>

    {{-- 🔍 Ô tìm kiếm --}}
    <form method="GET" action="{{ route('taisan.index') }}" class="mb-3 search-bar">
        <div class="input-group">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                placeholder="Tìm kiếm tài sản (mã hoặc tên)">
            <select name="phong_id" class="form-select form-control" style="max-width: 220px;">
                <option value="">-- Tất cả phòng --</option>
                @foreach($phongs as $phong)
                <option value="{{ $phong->id }}" {{ request('phong_id') == $phong->id ? 'selected' : '' }}>
                    {{ $phong->ten_phong }}
                </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-outline-secondary">Tìm kiếm</button>
            @if(request('search') || request('phong_id'))
                <a href="{{ route('taisan.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
            @endif
        </div>
    </form>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Danh sách tài sản</h4>
        <a href="{{ route('taisan.create') }}" class="btn btn-primary">+ Thêm tài sản</a>
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
        @forelse($listTaiSan as $item)
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">

                {{-- Header --}}
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>{{ $item->khoTaiSan->ten_tai_san ?? '—' }}</strong>
                    <span class="text-muted small">{{ $item->khoTaiSan->ma_tai_san ?? '—' }}</span>
                </div>

                {{-- Ảnh --}}
                @if(!empty($item->khoTaiSan->hinh_anh))
                    <img src="{{ asset('uploads/kho/' . $item->khoTaiSan->hinh_anh) }}" 
                         class="card-img-top" 
                         style="height:180px;object-fit:cover" 
                         alt="Ảnh tài sản">
                @else
                    <div class="card-img-top d-flex align-items-center justify-content-center" 
                         style="height:180px;background:#f8f9fa">
                        <svg width="80" height="60" viewBox="0 0 24 24" fill="none" 
                             xmlns="http://www.w3.org/2000/svg">
                            <rect width="24" height="24" rx="2" fill="#e9ecef"/>
                            <path d="M3 15L8 9L13 15L21 6" stroke="#adb5bd" stroke-width="1.2"
                                  stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                @endif

                {{-- Nội dung --}}
                <div class="card-body">
                    <p class="mb-1"><strong>Phòng:</strong> {{ $item->phong->ten_phong ?? 'Chưa gán' }}</p>
                    <p class="mb-1"><strong>Số lượng:</strong> {{ $item->so_luong }}</p>
                    <p class="mb-1">
                        <strong>Tình trạng ban đầu:</strong>
                        <span class="badge 
                            @if($item->tinh_trang == 'mới') bg-success
                            @elseif($item->tinh_trang == 'cũ') bg-secondary
                            @elseif($item->tinh_trang == 'bảo trì') bg-warning text-dark
                            @elseif($item->tinh_trang == 'hỏng') bg-danger
                            @else bg-white @endif">
                            {{ ucfirst($item->tinh_trang) }}
                        </span>
                    </p>
                    <p class="mb-1">
                        <strong>Tình trạng hiện tại:</strong>
                        <span class="badge 
                            @if($item->tinh_trang_hien_tai == 'mới') bg-success
                            @elseif($item->tinh_trang_hien_tai == 'cũ') bg-secondary
                            @elseif($item->tinh_trang_hien_tai == 'bảo trì') bg-warning text-dark
                            @elseif($item->tinh_trang_hien_tai == 'hỏng') bg-danger
                            @else bg-success @endif">
                            {{ ucfirst($item->tinh_trang_hien_tai ?? 'Không rõ') }}
                        </span>
                    </p>
                    <p class="mb-1"><strong>Ghi chú:</strong> {{ $item->ghi_chu ?? '-' }}</p>
                </div>

                {{-- Footer hành động --}}
                <div class="card-footer d-flex gap-2">
                    <a href="{{ route('taisan.edit', $item->id) }}" 
                       class="btn btn-sm btn-warning flex-fill">Sửa</a>

                    <a href="{{ route('lichbaotri.create', ['taisan_id' => $item->id]) }}" 
                       class="btn btn-sm btn-primary flex-fill">
                       Lên lịch bảo trì
                    </a>
<button type="button" 
        class="btn btn-sm btn-info flex-fill text-white btn-xemchitiet" 
        data-id="{{ $item->id }}">
    Xem chi tiết
</button>

                    <form action="{{ route('taisan.destroy', $item->id) }}" method="POST" 
                          style="display:inline-block" class="mb-0 flex-fill">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger w-100"
                            onclick="return confirm('Xóa tài sản này khỏi phòng?')">Xóa</button>
                    </form>
                </div>
            </div>
        </div>
        @empty
            <div class="col-12 text-center text-muted py-4">Không có tài sản nào trong phòng</div>
        @endforelse
    </div>

    {{-- 📄 Phân trang --}}
    <div class="d-flex justify-content-center mt-3">
        {{ $listTaiSan->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>

</div>
<!-- Modal xem chi tiết -->
<div class="modal fade" id="modalTaiSan" tabindex="-1" aria-labelledby="modalTaiSanLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="modalTaiSanLabel">🔍 Chi tiết tài sản phòng</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body text-center">
        <div class="spinner-border text-info" role="status">
          <span class="visually-hidden">Đang tải...</span>
        </div>
        <p class="mt-2 text-muted">Đang tải dữ liệu...</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function() {
    $('.btn-xemchitiet').click(function() {
        let id = $(this).data('id');
        let modal = $('#modalTaiSan');

        modal.modal('show');
        modal.find('.modal-body').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-info" role="status"></div>
                <p class="mt-2 text-muted">Đang tải dữ liệu...</p>
            </div>
        `);

        $.ajax({
            url: '{{ route("taisan.showModal", "") }}/' + id, // 👈 dùng route helper
            type: 'GET',
            success: function(response) {
                modal.find('.modal-body').html(response.data);
            },
            error: function() {
                modal.find('.modal-body').html('<p class="text-danger text-center">Không thể tải dữ liệu tài sản.</p>');
            }
        });
    });
});
</script>

@endsection
