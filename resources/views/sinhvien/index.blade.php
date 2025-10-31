@extends('admin.layouts.admin')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@section('content')
    <div class="container mt-4">
        <h3 class="page-title">📋 Danh sách sinh viên</h3>

        <!-- Ô tìm kiếm -->
        <form method="GET" class="mb-3 search-bar">
            <div class="input-group">
                <input type="text" name="search" value="{{ $keyword ?? '' }}" class="form-control"
                    placeholder="Tìm kiếm tên sinh viên">
                <button type="submit" class="btn btn-outline-secondary">Tìm kiếm</button>
                <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#filterModal">
                    <i class="fa fa-filter mr-1"></i> Bộ lọc
                </button>
                @if (!empty($keyword))
                    <a href="{{ route('sinhvien.index') }}" class="btn btn-outline-secondary">Xóa</a>
                @endif
            </div>

        </form>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Danh sách các sinh viên</h4>
            <!-- Nút thêm sinh viên -->
            <a href="{{ route('sinhvien.create') }}" class="btn btn-primary mb-3 btn-add">+ Thêm sinh viên</a>
        </div>
        {{-- Trang mới --}}
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                @php
                    $perPage = $sinhviens->perPage();
                    $currentPage = $sinhviens->currentPage();
                    $sttBase = ($currentPage - 1) * $perPage;
                @endphp

                <div class="table-responsive">
                    <table class="table table-hover mb-0 table-sv">
                        <thead>
                            <tr>
                                <th class="fit text-center">STT</th>
                                <th>Họ và tên</th>
                                <th class="fit">Mã SV</th>
                                <th class="fit">Hình ảnh</th>
                                <th class="fit">Phòng</th>
                                <th class="fit">Trạng thái</th>
                                <th class="text-right fit fit text-center" >Thao tác</th>
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
                                    <td class="text-right fit">
                                        <div class="btn-group">
                                            <button type="button" data-id="{{ $sv->id }}"
                                                class="btn btn-sm btn-outline-info openModalBtn" title="Xem chi tiết">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            <a href="{{ route('sinhvien.edit', $sv->id) }}"
                                                class="btn btn-sm btn-outline-primary" title="Sửa">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            @if (($sv->trang_thai_ho_so ?? '') !== 'Đã duyệt')
                                                <form action="{{ route('sinhvien.approve', $sv->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf @method('PATCH')
                                                    <button class="btn btn-sm btn-outline-success" title="Duyệt hồ sơ">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('sinhvien.destroy', $sv->id) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('Xác nhận xóa sinh viên này?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" title="Xóa">
                                                    <i class="fa fa-trash"></i>
                                                </button>
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
                        <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                            <span aria-hidden="true">&times;</span>
                        </button>
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalBody">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.openModalBtn').on('click', function() {
                let id = $(this).data('id');
                get_sinh_vien(id);
                $('#exampleModal').modal('show');
            });
        });


        async function get_sinh_vien(id) {
            let url = `{{ route('sinhvien.show.modal', ['id' => ':id']) }}`;
            url = url.replace(':id', id);

            $.ajax({
                url: url,
                type: 'GET',
                async: false,
                success: function(res, textStatus) {
                    console.log(res);
                    const response = res.data ?? '';
                    renderSinhvien(response);
                },
                error: function(request, status, error) {
                    let data = JSON.parse(request.responseText);
                    alert(data.message);
                }
            });
        }

        function renderSinhvien(html) {
            $('#modalBody').html(html);
        }
    </script>
@endsection
