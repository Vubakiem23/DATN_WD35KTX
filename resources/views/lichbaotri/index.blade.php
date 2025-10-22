@extends('admin.layouts.admin')
@section('title', 'Quản lý lịch bảo trì')

@section('content')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<div class="container mt-4">
    <h3 class="page-title">🛠️ Quản lý lịch bảo trì</h3>

    {{-- Thanh tìm kiếm & lọc --}}
    <form method="GET" class="mb-3 search-bar">
        <div class="row g-2 align-items-center">
            <div class="col-md-3">
                <input type="text" name="ten_tai_san" value="{{ request('ten_tai_san') }}"
                       class="form-control" placeholder="🔍 Tìm theo tên tài sản">
            </div>

            <div class="col-md-3">
                <select name="trang_thai" class="form-select form-control">
                    <option value="" class="text-center">-- Trạng thái --</option>
                    <option value="Chờ bảo trì" {{ request('trang_thai') == 'Chờ bảo trì' ? 'selected' : '' }}>Chờ bảo trì</option>
                    <option value="Đang bảo trì" {{ request('trang_thai') == 'Đang bảo trì' ? 'selected' : '' }}>Đang bảo trì</option>
                    <option value="Hoàn thành" {{ request('trang_thai') == 'Hoàn thành' ? 'selected' : '' }}>Hoàn thành</option>
                </select>
            </div>

            <div class="col-md-3">
                <input type="date" name="ngay_bao_tri" value="{{ request('ngay_bao_tri') }}"
                       class="form-control" placeholder="Ngày bảo trì">
            </div>

            <div class="col-md-3 d-flex justify-content-end">
                <button type="submit" class="btn btn-outline-secondary me-2">Lọc</button>
                <a href="{{ route('lichbaotri.create') }}" class="btn btn-primary">+ Lên lịch mới</a>
            </div>
        </div>
    </form>

    {{-- Thông báo --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Danh sách lịch bảo trì --}}
    <div class="tab-content">
        <div class="row g-3">
            @forelse($lich as $l)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm {{ $l->trang_thai == 'Hoàn thành' ? 'border-success' : '' }}">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <strong>{{ $l->taiSan->ten_tai_san ?? 'Không xác định' }}</strong>
                            <span class="badge 
                                @if($l->trang_thai == 'Hoàn thành') bg-success
                                @elseif($l->trang_thai == 'Đang bảo trì') bg-warning text-dark
                                @else bg-secondary @endif">
                                {{ $l->trang_thai }}
                            </span>
                        </div>

                        @if($l->hinh_anh)
                            <img src="{{ asset('uploads/lichbaotri/'.$l->hinh_anh) }}" 
                                 class="card-img-top" style="height:160px;object-fit:cover;" alt="Ảnh bảo trì">
                        @else
                            <div class="card-img-top d-flex align-items-center justify-content-center"
                                 style="height:160px;background:#f8f9fa;">
                                <svg width="80" height="60" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <rect width="24" height="24" rx="2" fill="#e9ecef"/>
                                    <path d="M3 15L8 9L13 15L21 6" stroke="#adb5bd" stroke-width="1.2"
                                          stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        @endif

                        <div class="card-body">
                            <p class="mb-1"><strong>Ngày bảo trì:</strong> {{ $l->ngay_bao_tri }}</p>
                            <p class="mb-1"><strong>Ngày hoàn thành:</strong> {{ $l->ngay_hoan_thanh ?? '-' }}</p>
                            <p class="mb-1"><strong>Mô tả:</strong> {{ $l->mo_ta ?? '-' }}</p>
                        </div>

                        <div class="card-footer d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-secondary flex-fill openModalBtn"
                                    data-id="{{ $l->id }}"> Chi tiết</button>

                            <a href="{{ route('lichbaotri.edit', $l->id) }}" class="btn btn-sm btn-warning flex-fill"> Sửa</a>

                            <form action="{{ route('lichbaotri.destroy', $l->id) }}" method="POST" class="mb-0 flex-fill">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger w-100" onclick="return confirm('Xóa lịch này?')">
                                     Xóa
                                </button>
                            </form>

                            @if($l->trang_thai != 'Hoàn thành')
                                <form action="{{ route('lichbaotri.hoanthanh', $l->id) }}" method="POST" class="mb-0 flex-fill">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-sm btn-success w-100" onclick="return confirm('Đánh dấu hoàn thành?')">
                                         Hoàn thành
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted">Không có lịch bảo trì nào</div>
            @endforelse
        </div>
    </div>

    {{-- Phân trang --}}
    <div class="d-flex justify-content-center mt-3">
        {{ $lich->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
</div>

{{-- Modal hiển thị chi tiết --}}
<div class="modal fade" id="lichModal" tabindex="-1" aria-labelledby="lichModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lichModalLabel">Chi tiết lịch bảo trì</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <p class="text-center text-muted">Đang tải dữ liệu...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

{{-- Script Ajax lấy dữ liệu chi tiết --}}
<script>
$(document).ready(function () {
    $('.openModalBtn').on('click', function () {
        let id = $(this).data('id');
        getChiTietLich(id);
        $('#lichModal').modal('show');
    });
});

function getChiTietLich(id) {
    let url = `{{ route('lichbaotri.show.modal', ['id'=>':id']) }}`;
    url = url.replace(':id', id);

    $.ajax({
        url: url,
        type: 'GET',
        success: function (res) {
            $('#modalBody').html(res.data ?? '<p class="text-muted">Không có dữ liệu</p>');
        },
        error: function (err) {
            $('#modalBody').html('<p class="text-danger">Không thể tải dữ liệu</p>');
        }
    });
}
</script>
@endsection
