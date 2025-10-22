@extends('admin.layouts.admin')

@section('content')
@php use Illuminate\Support\Str; @endphp

<div class="x_panel">
    <div class="x_title d-flex justify-content-between align-items-center">
        <h2>Danh sách sự cố</h2>
        <a href="{{ route('suco.create') }}" class="btn btn-primary btn-modern">
            <i class="fa fa-plus"></i> Thêm sự cố
        </a>
    </div>

    <div class="x_content">

        @if(session('success'))
            <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
        @endif

        <table class="table table-striped table-bordered text-center align-middle shadow-sm">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Ảnh</th>
                    <th>Sinh viên</th>
                    <th>Phòng</th>
                    <th>Mô tả</th>
                    <th>Ngày gửi</th>
                    <th>Trạng thái</th>
                    <th width="150px">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suco as $item)
                <tr>
                    <td>{{ $item->id }}</td>

                    {{-- ✅ Hiển thị ảnh --}}
                    <td>
                        @if(!empty($item->anh))
                            <img src="{{ asset($item->anh) }}" 
                                 alt="Ảnh sự cố" 
                                 width="60" height="60" 
                                 class="rounded shadow-sm"
                                 style="object-fit: cover;">
                        @else
                            <img src="{{ asset('images/no-image.png') }}" 
                                 alt="Không có ảnh" 
                                 width="60" height="60" 
                                 class="rounded shadow-sm opacity-50"
                                 style="object-fit: cover;">
                        @endif
                    </td>

                    {{-- ✅ Thông tin sinh viên --}}
                    <td>
                        @if($item->sinhVien)
                            <strong>{{ $item->sinhVien->ho_ten }}</strong><br>
                            <small class="text-muted">MSSV: {{ $item->sinhVien->ma_sinh_vien ?? '---' }}</small>
                        @else
                            ---
                        @endif
                    </td>

                    <td>{{ $item->phong->ten_phong ?? '---' }}</td>
                    <td>{{ Str::limit($item->mo_ta, 60) }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->ngay_gui)->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge 
                            @if($item->trang_thai == 'Tiếp nhận') bg-secondary
                            @elseif($item->trang_thai == 'Đang xử lý') bg-info
                            @elseif($item->trang_thai == 'Hoàn thành') bg-success
                            @else bg-warning
                            @endif">
                            {{ $item->trang_thai }}
                        </span>
                    </td>

                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="{{ route('suco.show', $item->id) }}" 
                               class="btn btn-modern btn-info" 
                               title="Xem chi tiết">
                                <i class="fa fa-eye"></i>
                            </a>
                            <a href="{{ route('suco.edit', $item->id) }}" 
                               class="btn btn-modern btn-warning" 
                               title="Cập nhật">
                                <i class="fa fa-edit"></i>
                            </a>
                            <form action="{{ route('suco.destroy', $item->id) }}" 
                                  method="POST" 
                                  style="display:inline-block;">
                                @csrf @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-modern btn-danger" 
                                        onclick="return confirm('Xóa sự cố này?')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        <i class="fa fa-exclamation-circle"></i> Chưa có sự cố nào được ghi nhận.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="text-center mt-3">
            {{ $suco->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>

{{-- ✅ CSS hiện đại, vuông đẹp --}}
<style>
.table td, .table th {
    vertical-align: middle !important;
}
.badge {
    padding: 6px 10px;
    border-radius: 8px;
    color: #fff;
    font-size: 12px;
}
.bg-secondary { background-color: #6c757d; }
.bg-info { background-color: #17a2b8; }
.bg-success { background-color: #28a745; }
.bg-warning { background-color: #ffc107; color: #000; }

/* 🔹 Nút vuông đẹp, bo nhẹ, đổ bóng */
.btn-modern {
    border-radius: 6px !important;
    padding: 6px 10px !important;
    font-weight: 500;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.btn-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}
.btn-modern i {
    margin-right: 2px;
}

/* Nhóm nút gọn gàng */
.btn-group .btn {
    margin-right: 4px;
}
</style>
@endsection
