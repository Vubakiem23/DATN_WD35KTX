@extends('admin.layouts.admin')

@section('content')
<style>
    /* === Pagination Custom Style === */
.pagination {
    justify-content: center;
    margin-top: 20px;
}

.page-item.active .page-link {
    background-color: #1e88e5;
    border-color: #1e88e5;
    color: #fff;
    box-shadow: 0 0 5px rgba(30,136,229,0.5);
}

.page-link {
    color: #333;
    border-radius: 6px;
    margin: 0 3px;
    transition: all 0.2s ease-in-out;
}

.page-link:hover {
    background-color: #f0f0f0;
    text-decoration: none;
}
/* === End Pagination Custom Style === */

</style>
<div class="container mt-4">
    <h3>📋 Danh sách sinh viên</h3>

    <!-- Ô tìm kiếm -->
    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" value="{{ $keyword }}" class="form-control" placeholder="Tìm kiếm sinh vien">
            <button type="submit" class="btn btn-outline-secondary">Tìm kiếm</button>
        </div>
    </form>

    <!-- Nút thêm sinh viên -->
    <a href="{{ route('sinhvien.create') }}" class="btn btn-primary mb-3">+ Thêm sinh viên</a>

    <!-- Bảng danh sách -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light text-center">
                <tr>
                    <th>Mã SV</th>
                    <th>Họ và tên</th>
                    <th>Ngày sinh</th>
                    <th>Giới tính</th>
                    <th>Lớp</th>
                    <th>Ngành</th>
                    <th>Khóa học</th>
                    <th>Quê quán</th>
                    <th>Nơi ở hiện tại</th>
                    <th>Số điện thoại</th>
                    <th>Email</th>
                    <th>Phòng</th>
                    <th>Trạng thái hồ sơ</th>
                    <th width="150">Hành động</th>
                </tr>
            </thead>

            <tbody>
                @forelse($sinhviens as $sv)
                <tr>
                    <td>{{ $sv->ma_sinh_vien }}</td>
                    <td>{{ $sv->ho_ten }}</td>
                    <td>{{ \Carbon\Carbon::parse($sv->ngay_sinh)->format('d/m/Y') }}</td>
                    <td>{{ $sv->gioi_tinh }}</td>
                    <td>{{ $sv->lop }}</td>
                    <td>{{ $sv->nganh }}</td>
                    <td>{{ $sv->khoa_hoc }}</td>
                    <td>{{ $sv->que_quan }}</td>
                    <td>{{ $sv->noi_o_hien_tai }}</td>
                    <td>{{ $sv->so_dien_thoai }}</td>
                    <td>{{ $sv->email }}</td>
                    <td>{{ $sv->phong->ten_phong ?? 'Chưa phân' }}</td>
                    <td>
                        <span class="badge 
                            @if($sv->trang_thai_ho_so == 'Đã duyệt') bg-success 
                            @elseif($sv->trang_thai_ho_so == 'Chờ duyệt') bg-warning 
                            @else bg-secondary @endif">
                            {{ $sv->trang_thai_ho_so }}
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('sinhvien.edit', $sv->id) }}" class="btn btn-sm btn-warning mb-1">Sửa</a>
                        <form action="{{ route('sinhvien.destroy', $sv->id) }}" method="POST" style="display:inline-block">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger mb-1"
                                onclick="return confirm('Xác nhận xóa sinh viên này?')">Xóa</button>
                        </form>

                        @if($sv->trang_thai_ho_so != 'Đã duyệt')
                        <form action="{{ route('sinhvien.approve', $sv->id) }}" method="POST" style="display:inline-block">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-success">Duyệt</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="14" class="text-center text-muted">Không có sinh viên nào trong hệ thống</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Phân trang -->
    <div class="d-flex justify-content-center mt-3">
        {{ $sinhviens->links() }}
    </div>
</div>
@endsection
