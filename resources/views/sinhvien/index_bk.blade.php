@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">
    <h3 class="page-title">📋 Danh sách sinh viên</h3>

    <!-- Ô tìm kiếm -->
    <form method="GET" class="mb-3 search-bar">
        <div class="input-group">
            <input type="text"
                   name="search"
                   value="{{ $keyword ?? '' }}"
                   class="form-control"
                   placeholder="Tìm kiếm sinh viên (mã SV, họ tên, lớp, ngành)">
            <button type="submit" class="btn btn-outline-secondary">Tìm kiếm</button>
            @if(!empty($keyword))
                <a href="{{ route('sinhvien.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
            @endif
        </div>
    </form>

    <!-- Nút thêm sinh viên -->
    <a href="{{ route('sinhvien.create') }}" class="btn btn-primary mb-3 btn-add">+ Thêm sinh viên</a>

    <!-- Bảng danh sách -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle sv-table">
            <thead class="table-light text-center sticky-head">
                <tr>
                    <th>Mã SV</th>
                    <th>Họ và tên</th>
                    <th>Ngày sinh</th>
                    <th>Giới tính</th>
                    <th class="d-none d-lg-table-cell">Lớp</th>
                    <th class="d-none d-lg-table-cell">Ngành</th>
                    <th class="d-none d-xl-table-cell">Khóa học</th>
                    <th class="d-none d-lg-table-cell">Quê quán</th>
                    <th class="d-none d-xl-table-cell">Nơi ở hiện tại</th>
                    <th class="d-none d-xl-table-cell">Số điện thoại</th>
                    <th class="d-none d-lg-table-cell">Email</th>
                    <th>Phòng</th>
                    <th>Trạng thái hồ sơ</th>
                    <th width="160">Hành động</th>
                </tr>
            </thead>

            <tbody>
                @forelse($sinhviens as $sv)
                <tr>
                    <td class="text-nowrap">{{ $sv->ma_sinh_vien }}</td>
                    <td class="fw-500">{{ $sv->ho_ten }}</td>
                    <td class="text-nowrap">
                        {{ !empty($sv->ngay_sinh) ? \Carbon\Carbon::parse($sv->ngay_sinh)->format('d/m/Y') : '-' }}
                    </td>
                    <td class="text-center">{{ $sv->gioi_tinh ?? '-' }}</td>

                    <td class="d-none d-lg-table-cell text-center">{{ $sv->lop ?? '-' }}</td>
                    <td class="d-none d-lg-table-cell text-center">{{ $sv->nganh ?? '-' }}</td>
                    <td class="d-none d-xl-table-cell text-center">{{ $sv->khoa_hoc ?? '-' }}</td>

                    <td class="d-none d-lg-table-cell text-truncate max-200" title="{{ $sv->que_quan }}">
                        {{ $sv->que_quan }}
                    </td>
                    <td class="d-none d-xl-table-cell text-truncate max-220" title="{{ $sv->noi_o_hien_tai }}">
                        {{ $sv->noi_o_hien_tai }}
                    </td>
                    <td class="d-none d-xl-table-cell text-nowrap">{{ $sv->so_dien_thoai ?? '-' }}</td>
                    <td class="d-none d-lg-table-cell text-truncate max-220" title="{{ $sv->email }}">
                        {{ $sv->email }}
                    </td>

                    <td class="text-nowrap">{{ $sv->phong->ten_phong ?? 'Chưa phân' }}</td>

                    <td class="text-center">
                        @php
                            $status = $sv->trang_thai_ho_so ?? 'Khác';
                            $badge = match($status) {
                                'Đã duyệt' => 'bg-success',
                                'Chờ duyệt' => 'bg-warning',
                                default => 'bg-secondary'
                            };
                        @endphp
                        <span class="badge {{ $badge }}">{{ $status }}</span>
                    </td>

                    <td class="text-center">
                        <a href="{{ route('sinhvien.edit', $sv->id) }}" class="btn btn-sm btn-warning mb-1">Sửa</a>

                        <form action="{{ route('sinhvien.destroy', $sv->id) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn btn-sm btn-danger mb-1"
                                    onclick="return confirm('Xác nhận xóa sinh viên này?')">
                                Xóa
                            </button>
                        </form>

                        @if(($sv->trang_thai_ho_so ?? '') !== 'Đã duyệt')
                        <form action="{{ route('sinhvien.approve', $sv->id) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-success mb-1">Duyệt</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="14" class="text-center text-muted py-4">
                        Không có sinh viên nào trong hệ thống
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Phân trang -->
    <div class="d-flex justify-content-center mt-3">
        {{ $sinhviens->onEachSide(1)->links() }}
    </div>
</div>
@endsection
