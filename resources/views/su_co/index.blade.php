@extends('admin.layouts.admin')

@section('content')

<div class="x_panel">
    <div class="x_title d-flex justify-content-between align-items-center flex-wrap">
        <h2><i class="fa fa-exclamation-circle text-primary"></i> Danh sách sự cố</h2>
        <a href="{{ route('suco.create') }}" class="btn btn-sm btn-primary mt-2 mt-sm-0">
            <i class="fa fa-plus"></i> Thêm sự cố
        </a>
    </div>


    <div class="x_content">
        {{-- 🔍 Ô tìm kiếm --}}
        <form method="GET" action="{{ route('suco.index') }}" class="mb-3 d-flex align-items-center flex-wrap gap-2">
            <input type="text" name="search" value="{{ request('search') ?? '' }}"
                   class="form-control form-control-sm w-auto"
                   placeholder="Tìm theo MSSV hoặc Họ tên">
            <button type="submit" class="btn btn-sm btn-primary">Tìm</button>
            @if (request('search'))
                <a href="{{ route('suco.index') }}" class="btn btn-sm btn-light">Xóa lọc</a>
            @endif
        </form>

        {{-- 🟢 Thông báo --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fa fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        {{-- 📋 Danh sách --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle text-center small mb-0">
                <thead class="bg-light">
                    <tr>
                        <th style="width:40px;">ID</th>
                        <th style="width:120px;">Sinh viên</th>
                        <th style="width:80px;">Phòng</th>
                        <th style="width:80px;">Ngày gửi</th>
                        <th style="width:100px;">Hoàn thành</th>
                        <th style="width:60px;">Ảnh</th>
                        <th style="max-width:200px;">Mô tả</th>
                        <th style="width:90px;">Trạng thái</th>
                        <th style="width:80px;">Giá tiền</th>
                        <th style="width:100px;">Thanh toán</th>
                        <th style="width:110px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($su_cos as $sc)
                        <tr>
                            <td>{{ $sc->id }}</td>
                            <td class="text-start">
                                <div class="text-truncate" style="max-width:120px;">
                                    {{ $sc->sinhVien->ho_ten ?? '---' }}
                                    <br>
                                    <small class="text-muted">MSSV: {{ $sc->sinhVien->ma_sinh_vien ?? '---' }}</small>
                                </div>
                            </td>
                            <td>{{ $sc->phong->ten_phong ?? '---' }}</td>
                            <td>{{ $sc->ngay_gui ? \Carbon\Carbon::parse($sc->ngay_gui)->format('d/m/Y') : '-' }}</td>
                            <td>
                                @if($sc->ngay_hoan_thanh)
                                    {{ \Carbon\Carbon::parse($sc->ngay_hoan_thanh)->format('d/m/Y') }}
                                @else
                                    <span class="text-muted">---</span>
                                @endif
                            </td>
                            <td>
                                @if ($sc->anh && file_exists(public_path($sc->anh)))
                                    <img src="{{ asset($sc->anh) }}" class="img-thumbnail shadow-sm"
                                         style="width:35px; height:35px; object-fit:cover;">
                                @else
                                    <span class="text-muted">---</span>
                                @endif
                            </td>

                            {{-- ✏️ Mô tả --}}
                            <td class="text-start">
                                <div class="desc-truncate" title="{{ $sc->mo_ta }}">
                                    {{ $sc->mo_ta }}
                                </div>
                            </td>

                            <td>
                                @php
                                    $badge = match($sc->trang_thai) {
                                        'Tiếp nhận' => 'bg-secondary',
                                        'Đang xử lý' => 'bg-info',
                                        'Hoàn thành' => 'bg-success',
                                        default => 'bg-light text-dark'
                                    };
                                @endphp
                                <span class="badge {{ $badge }}">{{ $sc->trang_thai }}</span>
                            </td>

                            <td>{{ $sc->payment_amount > 0 ? number_format($sc->payment_amount, 0, ',', '.') . ' ₫' : '0 ₫' }}</td>

                            <td>
                                @if($sc->payment_amount == 0)
                                    <span class="badge bg-secondary">Không TT</span>
                                @elseif($sc->is_paid)
                                    <span class="badge bg-success">Đã TT</span>
                                @else
                                    <span class="badge bg-warning text-dark">Chưa TT</span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex justify-content-center gap-1 flex-wrap">
                                    <a href="{{ route('suco.show', $sc->id) }}" class="btn btn-secondary btn-xs" title="Xem">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="{{ route('suco.edit', $sc->id) }}" class="btn btn-warning btn-xs" title="Sửa">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <form action="{{ route('suco.destroy', $sc->id) }}" method="POST" onsubmit="return confirm('Xác nhận xóa?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-xs" title="Xóa">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-3">Chưa có sự cố nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $su_cos->onEachSide(1)->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>

<style>
.table th, .table td {
    vertical-align: middle !important;
    padding: 0.45rem !important;
    font-size: 13px;
    white-space: nowrap;
}
.badge {
    padding: 4px 8px;
    border-radius: 10px;
    font-size: 11px;
}
.btn-xs {
    padding: 4px 6px;
    font-size: 0.75rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.table-responsive {
    overflow-x: auto;
}
.desc-truncate {
    max-width: 220px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: normal;
    word-break: break-word;
    line-height: 1.3;
    font-size: 13px;
    color: #333;
}
</style>
@endsection
