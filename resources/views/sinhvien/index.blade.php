@extends('admin.layouts.admin')
@section('content')
    <div class="container mt-4">

            <div>
                <h3 class="room-page__title mb-1">Danh sách sinh viên</h3>
                <p class="text-muted mb-0">Theo dõi hồ sơ sinh viên và trạng thái duyệt</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('sinhvien.create') }}" class="btn btn-dergin btn-dergin--info"><i class="fa fa-plus"></i><span>Thêm sinh viên</span></a>
            </div>


        <!-- Ô tìm kiếm -->
        <form method="GET" class="mb-3 search-bar">
            <div class="input-group">
                <input type="text" name="search" value="{{ $keyword ?? '' }}" class="form-control"
                    placeholder="Tìm kiếm tên sinh viên">
                <button type="submit" class="btn btn-outline-secondary">Tìm kiếm</button>
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                    <i class="fa fa-filter mr-1"></i> Bộ lọc
                </button>
                @if (!empty($keyword))
                    <a href="{{ route('sinhvien.index') }}" class="btn btn-outline-secondary">Xóa</a>
                @endif
            </div>

        </form>
        @push('styles')
        <style>
            html{scroll-behavior:auto !important}
            .room-page__title{font-size:1.75rem;font-weight:700;color:#1f2937}
            .room-table-wrapper{background:#fff;border-radius:14px;box-shadow:0 10px 30px rgba(15,23,42,0.06);padding:1.25rem}
            .room-table{margin-bottom:0;border-collapse:separate;border-spacing:0 12px}
            .room-table thead th{font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;color:#6c757d;border:none;padding-bottom:.75rem}
            .room-table tbody tr{background:#f9fafc;border-radius:16px;transition:transform .2s ease,box-shadow .2s ease}
            .room-table tbody tr:hover{transform:translateY(-2px);box-shadow:0 12px 30px rgba(15,23,42,0.08)}
            .room-table tbody td{border:none;vertical-align:middle;padding:1rem .95rem}
            .room-table tbody tr td:first-child{border-top-left-radius:16px;border-bottom-left-radius:16px}
            .room-table tbody tr td:last-child{border-top-right-radius:16px;border-bottom-right-radius:16px}
            .room-actions{display:flex;flex-wrap:nowrap;justify-content:center;gap:.4rem;white-space:nowrap}
            .room-actions .btn-dergin{min-width:92px}
            .room-actions .btn-dergin span{line-height:1;white-space:nowrap}
            .btn-dergin{display:inline-flex;align-items:center;justify-content:center;gap:.35rem;padding:.4rem .9rem;border-radius:999px;font-weight:600;font-size:.72rem;border:none;color:#fff;background:linear-gradient(135deg,#4e54c8 0%,#8f94fb 100%);box-shadow:0 6px 16px rgba(78,84,200,.22);transition:transform .2s ease,box-shadow .2s ease}
            .btn-dergin:hover{transform:translateY(-1px);box-shadow:0 10px 22px rgba(78,84,200,.32);color:#fff}
            .btn-dergin i{font-size:.8rem}
            .btn-dergin--muted{background:linear-gradient(135deg,#4f46e5 0%,#6366f1 100%)}
            .btn-dergin--info{background:linear-gradient(135deg,#0ea5e9 0%,#2563eb 100%)}
            .btn-dergin--danger{background:linear-gradient(135deg,#f43f5e 0%,#ef4444 100%)}
            .avatar-56{width:56px;height:56px;border-radius:50%;object-fit:cover}
            @media (max-width:992px){
                .room-table thead{display:none}
                .room-table tbody{display:block}
                .room-table tbody tr{display:flex;flex-direction:column;padding:1rem}
                .room-table tbody td{display:flex;justify-content:space-between;padding:.35rem 0}
            }
        </style>
        @endpush
        {{-- Trang mới --}}
        <div class="room-table-wrapper">
            <div class="table-responsive">
                @php
                    $perPage = $sinhviens->perPage();
                    $currentPage = $sinhviens->currentPage();
                    $sttBase = ($currentPage - 1) * $perPage;
                @endphp

                <div class="table-responsive">
                    <table class="table table-hover mb-0 room-table">
                        <thead>
                            <tr>
                                <th class="fit text-center">STT</th>
                                <th>Họ và tên</th>
                                <th class="fit">Mã SV</th>
                                <th class="fit">Hình ảnh</th>
                                <th class="fit">Phòng</th>
                                <th class="fit">Trạng thái</th>
                                <th class="text-end fit fit text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sinhviens as $sv)
                                @php
                                    $status = $sv->trang_thai_ho_so ?? 'Khác';
                                    $badgeClass =
                                        $status === 'Đã duyệt'
                                            ? 'badge-soft-success'
                                            : ($status === 'Chờ duyệt'
                                                ? 'badge-soft-warning'
                                                : 'badge-soft-secondary');
                                    $imgUrl = $sv->anh_sinh_vien
                                        ? asset('storage/' . $sv->anh_sinh_vien)
                                        : asset('images/default-avatar.png');
                                @endphp
                                <tr>
                                    <td class="fit text-center">{{ $sttBase + $loop->iteration }}</td>
                                    <td class="font-weight-600">{{ $sv->ho_ten }}</td>
                                    <td class="fit">{{ $sv->ma_sinh_vien }}</td>
                                    <td class="fit">
                                        <img src="{{ $imgUrl }}" alt="Ảnh {{ $sv->ho_ten }}" class="avatar-56">
                                    </td>
                                    <td class="fit">{{ $sv->phong->ten_phong ?? '-' }}</td>
                                    <td class="fit"><span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                    </td>
                                    <td class="text-end fit">
                                        <div class="room-actions">
                                            <button type="button" data-id="{{ $sv->id }}" class="btn btn-dergin btn-dergin--muted openModalBtn" title="Xem chi tiết"><i class="fa fa-eye"></i><span>Chi tiết</span></button>
                                            <a href="{{ route('sinhvien.edit', $sv->id) }}" class="btn btn-dergin" title="Sửa"><i class="fa fa-pencil"></i><span>Sửa</span></a>
                                            @if (($sv->trang_thai_ho_so ?? '') !== 'Đã duyệt')
                                            <form action="{{ route('sinhvien.approve', $sv->id) }}" method="POST" class="d-inline">
                                                @csrf @method('PATCH')
                                                <button class="btn btn-dergin btn-dergin--info" title="Duyệt hồ sơ"><i class="fa fa-check"></i><span>Duyệt</span></button>
                                            </form>
                                            @endif
                                            <form action="{{ route('sinhvien.destroy', $sv->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xác nhận xóa sinh viên này?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-dergin btn-dergin--danger" title="Xóa"><i class="fa fa-trash"></i><span>Xóa</span></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <img src="https://dummyimage.com/120x80/eff3f9/9aa8b8&text=No+data" class="mb-2"
                                            alt="">
                                        <div>Chưa có sinh viên phù hợp</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>





        {{-- MODAL BỘ LỌC --}}
        <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="filterModalLabel">Bộ lọc sinh viên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>

                    <form method="GET" id="filterForm">
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="row">
                                    {{-- Hàng 1: Tìm nhanh – Giới tính – Tình trạng – Phòng – Khu --}}
                                    <div class="col-md-4 mb-3">
                                        <label class="small text-muted">Tìm nhanh</label>
                                        <input type="text" name="q" value="{{ request('q', $keyword ?? '') }}"
                                            class="form-control" placeholder="Mã SV, Họ tên, SĐT, Email">
                                    </div>

                                    <div class="col-md-2 mb-3">
                                        <label class="small text-muted">Giới tính</label>
                                        <select name="gender" class="form-control">
                                            <option value="">-- Tất cả --</option>
                                            <option value="Nam" @selected(request('gender') == 'Nam')>Nam</option>
                                            <option value="Nữ" @selected(request('gender') == 'Nữ')>Nữ</option>
                                            <option value="Khác" @selected(request('gender') == 'Khác')>Khác</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="small text-muted">Tình trạng hồ sơ</label>
                                        <select name="status" class="form-control">
                                            <option value="">-- Tất cả --</option>
                                            <option value="Đã duyệt" @selected(request('status') == 'Đã duyệt')>Đã duyệt</option>
                                            <option value="Chờ duyệt" @selected(request('status') == 'Chờ duyệt')>Chờ duyệt</option>
                                        </select>
                                    </div>

                                    <div class="col-md-2 mb-3">
                                        <label class="small text-muted">Phòng</label>
                                        <select name="room_id" class="form-control">
                                            <option value="">-- Tất cả --</option>
                                            @isset($phongs)
                                                @foreach ($phongs as $p)
                                                    <option value="{{ $p->id }}" @selected(request('room_id') == $p->id)>
                                                        {{ $p->ten_phong ?? 'P' . $p->id }}
                                                    </option>
                                                @endforeach
                                            @endisset
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="small text-muted">Khu</label>
                                        <select name="khu" class="form-control">
                                            <option value="">-- Tất cả --</option>
                                            @isset($dsKhu)
                                                @foreach ($dsKhu as $k)
                                                    <option value="{{ $k }}" @selected(request('khu') == $k)>
                                                        {{ $k }}</option>
                                                @endforeach
                                            @endisset
                                        </select>
                                    </div>

                                    {{-- Hàng 2: Lớp – Ngành – Niên khóa --}}
                                    <div class="col-md-4 mb-3">
                                        <label class="small text-muted">Lớp</label>
                                        <input type="text" name="class_id" class="form-control"
                                            value="{{ request('class_id') }}" placeholder="VD: CNTT01">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="small text-muted">Ngành</label>
                                        <input type="text" name="major_id" class="form-control"
                                            value="{{ request('major_id') }}" placeholder="VD: CNTT">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="small text-muted">Niên khóa</label>
                                        <input type="text" name="intake_year" class="form-control"
                                            value="{{ request('intake_year') }}" placeholder="VD: 2022/K17">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <a href="{{ route('sinhvien.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
                            <button type="submit" class="btn btn-primary">Áp dụng</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>


        <!-- Phân trang -->
        <div class="d-flex justify-content-center mt-3">
            {{ $sinhviens->onEachSide(1)->links() }}
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Thông tin sinh viên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalBody">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
    // Mở modal chi tiết sinh viên (chạy sau khi jQuery/Bootstrap đã được nạp ở layout)
    (function () {
        $(function() {
            $('.openModalBtn').on('click', function() {
                var id = $(this).data('id');
                get_sinh_vien(id);
                try {
                    var modalEl = document.getElementById('exampleModal');
                    var modal = window.bootstrap ? new bootstrap.Modal(modalEl) : null;
                    if (modal) { modal.show(); } else { $('#exampleModal').modal('show'); }
                } catch(e) {
                    $('#exampleModal').modal('show'); // Fallback cho BS4 nếu có
                }
            });
        });

        window.get_sinh_vien = function(id) {
            var url = `{{ route('sinhvien.show.modal', ['id' => ':id']) }}`.replace(':id', id);
            $.ajax({
                url: url,
                type: 'GET',
                success: function(res) {
                    var response = res.data ?? '';
                    renderSinhvien(response);
                },
                error: function(request) {
                    try {
                        var data = JSON.parse(request.responseText);
                        alert(data.message || 'Có lỗi xảy ra');
                    } catch(e) {
                        alert('Có lỗi xảy ra');
                    }
                }
            });
        };

        window.renderSinhvien = function(html) {
            $('#modalBody').html(html);
        };
    })();
</script>
<script>
    // Ngăn trình duyệt tự khôi phục vị trí cuộn và đưa trang về đầu khi vào trang
    (function () {
        if ('scrollRestoration' in history) {
            try { history.scrollRestoration = 'manual'; } catch (e) {}
        }
        window.addEventListener('load', function () {
            if (!location.hash) {
                window.scrollTo(0, 0);
            }
        });
        var cancelProgrammaticScroll = function(){
            try {
                if (window.jQuery) { jQuery('html, body').stop(true, false); }
            } catch(e) {}
            window.removeEventListener('wheel', cancelProgrammaticScroll, { passive: true });
            window.removeEventListener('touchmove', cancelProgrammaticScroll, { passive: true });
        };
        window.addEventListener('wheel', cancelProgrammaticScroll, { passive: true });
        window.addEventListener('touchmove', cancelProgrammaticScroll, { passive: true });
    })();
</script>
@endpush
@endsection
