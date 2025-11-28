@extends('admin.layouts.admin')

@section('title', 'Thông báo Hóa đơn Slot')

@section('content')
<div class="container mt-4">

    {{-- Tiêu đề --}}
    <div class="mb-4">
        <h3 class="room-page__title mb-2">📢 THÔNG BÁO HÓA ĐƠN SLOT</h3>
        <p class="text-muted mb-0">Theo dõi trạng thái thanh toán của sinh viên</p>
    </div>

    {{-- Nút mở modal bộ lọc + xuất --}}
    {{-- Form tìm kiếm --}}
<form method="GET" class="mb-3 search-bar">
    <div class="input-group">
        {{-- Ô tìm kiếm --}}
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Tìm kiếm...">


        {{-- Nút tìm kiếm --}}
        <button type="submit" class="btn btn-dergin btn-dergin--info">
            <i class="fa fa-search"></i> Tìm kiếm
        </button>

        {{-- Nút bộ lọc --}}
        <button type="button" class="btn btn-dergin btn-dergin--info" id="openFilterModalBtn">
            <i class="fa fa-filter"></i> <span>Bộ lọc</span>
        </button>
</div>
        {{-- Nút xuất tất cả --}}
        <a href="{{ route('hoadonslot.export.all') }}" class="btn btn-dergin btn-dergin--primary">
            📥 Xuất tất cả
        </a>
         <a href="{{ route('hoadonslot.export.paid') }}" class="btn btn-success btn-sm">📗 Xuất đã thanh toán</a>
            <a href="{{ route('hoadonslot.export.unpaid') }}" class="btn btn-danger btn-sm">📕 Xuất chưa thanh toán</a>
    
</form>


    {{-- Bảng Đã thanh toán --}}
    <div class="room-table-wrapper mb-4">
        <h5 class="mb-3 text-success">Sinh viên Đã thanh toán</h5>
        <div class="table-responsive">
            <table class="table table-hover room-table mb-0">
                <thead>
                    <tr>
                        <th class="fit text-center">STT</th>
                        <th>Phòng</th>
                        <th>Mã sinh viên</th>
                        <th>Tên sinh viên</th>
                        <th>Slot</th>
                        <th>Ngày thanh toán</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($daThanhToan as $item)
                        <tr>
                            <td class="fit text-center">{{ $loop->iteration }}</td>
                            <td>{{ $item->hoaDon->phong->ten_phong ?? $item->hoaDon->phong->ten ?? '' }}</td>
                            <td>{{ $item->sinhVien->ma_sinh_vien ?? '' }}</td>
                            <td>{{ $item->sinh_vien_ten }}</td>
                            <td>{{ $item->slot_label }}</td>
                            <td>{{ optional($item->ngay_thanh_toan)->format('d/m/Y H:i') ?? '' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">Chưa có sinh viên nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Bảng Chưa thanh toán --}}
    <div class="room-table-wrapper mb-4">
        <h5 class="mb-3 text-danger">Sinh viên Chưa thanh toán</h5>
        <div class="table-responsive">
            <table class="table table-hover room-table mb-0">
                <thead>
                    <tr>
                        <th class="fit text-center">STT</th>
                        <th>Phòng</th>
                        <th>Mã sinh viên</th>
                        <th>Tên sinh viên</th>
                        <th>Slot</th>
                        <th>Ngày tạo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($chuaThanhToan as $item)
                        <tr>
                            <td class="fit text-center">{{ $loop->iteration }}</td>
                            <td>{{ $item->hoaDon->phong->ten_phong ?? $item->hoaDon->phong->ten ?? '' }}</td>
                            <td>{{ $item->sinhVien->ma_sinh_vien ?? '-' }}</td>
                            <td>{{ $item->sinhVien->ho_ten ?? $item->sinh_vien_ten }}</td>
                            <td>{{ $item->slot_label }}</td>
                            <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">Chưa có sinh viên nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal bộ lọc --}}
<div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bộ lọc hóa đơn slot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="GET" action="{{ route('hoadonslot.index') }}">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="phong_id" class="form-label">Phòng</label>
                            <select name="phong_id" id="phong_id" class="form-select">
                                <option value="">-- Tất cả --</option>
                                @foreach($phongs as $phong)
                                    <option value="{{ $phong->id }}" {{ request('phong_id') == $phong->id ? 'selected' : '' }}>
                                        {{ $phong->ten_phong ?? $phong->ten }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="ma_sinh_vien" class="form-label">Mã sinh viên</label>
                            <input type="text" name="ma_sinh_vien" id="ma_sinh_vien" class="form-control" value="{{ request('ma_sinh_vien') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select name="status" id="status" class="form-select">
                                <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>-- Tất cả --</option>
                                <option value="da_thanh_toan" {{ request('status') === 'da_thanh_toan' ? 'selected' : '' }}>Đã thanh toán</option>
                                <option value="chua_thanh_toan" {{ request('status') === 'chua_thanh_toan' ? 'selected' : '' }}>Chưa thanh toán</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="date_start" class="form-label">Từ ngày</label>
                            <input type="date" name="date_start" id="date_start" class="form-control" value="{{ request('date_start') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="date_end" class="form-label">Đến ngày</label>
                            <input type="date" name="date_end" id="date_end" class="form-control" value="{{ request('date_end') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('hoadonslot.index') }}" class="btn btn-secondary">🔄 Reset</a>
                    <button type="submit" class="btn btn-primary">Áp dụng</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<!-- Script này phải load sau bootstrap.bundle.min.js -->
<script>
document.getElementById('openFilterModalBtn').addEventListener('click', function() {
    var modalEl = document.getElementById('filterModal');
    var modal = new bootstrap.Modal(modalEl);
    modal.show();
});
</script>
@endpush

 @push('styles')
            <style>
                html {
                    scroll-behavior: auto !important
                }


                .room-page__title {
                    font-size: 1.75rem;
                    font-weight: 700;
                    color: #1f2937;
                }

                .room-table-wrapper {
                    background: #fff;
                    border-radius: 14px;
                    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
                    padding: 1.25rem;
                }

                .room-table {
                    margin-bottom: 0;
                    border-collapse: separate;
                    border-spacing: 0 12px;
                }

                .room-table thead th {
                    font-size: .78rem;
                    text-transform: uppercase;
                    letter-spacing: .05em;
                    color: #6c757d;
                    border: none;
                    padding-bottom: .75rem;
                }

                .room-table tbody tr {
                    background: #f9fafc;
                    border-radius: 16px;
                    transition: transform .2s ease, box-shadow .2s ease;
                }

                .room-table tbody tr:hover {
                    /* transform: translateY(-2px); */
                    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
                }

                .room-table tbody td {
                    border: none;
                    vertical-align: middle;
                    padding: 1rem .95rem;
                }

                .room-table tbody tr td:first-child {
                    border-top-left-radius: 16px;
                    border-bottom-left-radius: 16px;
                }

                .room-table tbody tr td:last-child {
                    border-top-right-radius: 16px;
                    border-bottom-right-radius: 16px;
                }

                .room-table .fit {
                    white-space: nowrap;
                    width: 1%;
                }

                .room-table th.text-center,
                .room-table td.text-center {
                    text-align: center;
                }

                .room-actions {
                    display: flex;
                    justify-content: center;
                }

                .room-actions.dropdown {
                    position: relative;
                }

                /* Nút răng cưa gọn, nằm giữa cột */
                .room-actions .action-gear {
                    min-width: 40px;
                    padding: .45rem .7rem;
                    border-radius: 999px;
                }

                /* MENU: bay ngang sang trái, canh giữa ô, không tràn xuống dòng dưới */
                .room-actions .dropdown-menu {
                    position: absolute;
                    top: 50% !important;
                    /* lấy mốc giữa ô Thao tác */
                    right: 110%;
                    /* bật ngang sang trái của nút răng cưa */
                    left: auto;
                    transform: translateY(-50%);
                    /* canh giữa theo chiều dọc */
                    z-index: 1050;

                    min-width: 190px;
                    border-radius: 16px;
                    padding: .4rem 0;
                    margin: 0;
                    border: 1px solid #e5e7eb;
                    box-shadow: 0 16px 40px rgba(15, 23, 42, .18);
                    font-size: .82rem;
                    background: #fff;
                }

                /* Item trong dropdown: icon + chữ đẹp, hover nhẹ */
                .room-actions .dropdown-item {
                    display: flex;
                    align-items: center;
                    gap: .55rem;
                    padding: .42rem .9rem;
                    color: #4b5563;
                }

                .room-actions .dropdown-item i {
                    width: 16px;
                    text-align: center;
                }

                .room-actions .dropdown-item:hover {
                    background: #eef2ff;
                    color: #111827;
                }

                /* Riêng nút Xóa giữ màu đỏ */
                .room-actions .dropdown-item.text-danger,
                .room-actions .dropdown-item.text-danger:hover {
                    color: #dc2626;
                    font-weight: 500;
                }


                .btn-dergin {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    gap: .35rem;
                    padding: .4rem .9rem;
                    border-radius: 999px;
                    font-weight: 600;
                    font-size: .72rem;
                    border: none;
                    color: #fff;
                    background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
                    box-shadow: 0 6px 16px rgba(78, 84, 200, .22);
                    transition: transform .2s ease, box-shadow .2s ease;
                }

                .btn-dergin:hover {
                    transform: translateY(-1px);
                    box-shadow: 0 10px 22px rgba(78, 84, 200, .32);
                    color: #fff;
                }

                .btn-dergin i {
                    font-size: .8rem;
                }

                .btn-dergin--muted {
                    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
                }

                .btn-dergin--info {
                    background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
                }

                .btn-dergin--danger {
                    background: linear-gradient(135deg, #f43f5e 0%, #ef4444 100%);
                }

                .avatar-56 {
                    width: 56px;
                    height: 56px;
                    border-radius: 12px;
                    /* bo góc, không tròn nữa */
                    object-fit: cover;
                    border: 2px solid #e5e7eb;
                    /* viền nhạt */
                    background: #fff;
                }



                @media (max-width: 992px) {
                    .room-table thead {
                        display: none;
                    }

                    .room-table tbody {
                        display: block;
                    }

                    .room-table tbody tr {
                        display: flex;
                        flex-direction: column;
                        padding: 1rem;
                    }

                    .room-table tbody td {
                        display: flex;
                        justify-content: space-between;
                        padding: .35rem 0;
                    }
                }
            </style>
        @endpush
@endsection
