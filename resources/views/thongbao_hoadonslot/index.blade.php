@extends('admin.layouts.admin')

@section('title', 'Thông báo Hóa đơn Slot')

@section('content')

{{-- Tiêu đề --}}
    <div class="mb-4">
        <h3 class="room-page__title mb-2">📢 THÔNG BÁO HÓA ĐƠN SLOT</h3> 
    </div>
    <!-- Dropdown chọn phòng -->
    <form action="{{ route('hoadonslot.index') }}" method="GET" class="mb-3 search-bar">
        <div class="input-group">
            <label for="phong_id" class="mb-0">Chọn phòng:</label>
            <select name="phong_id" id="phong_id" class="form-control" onchange="this.form.submit()">
                <option value="">-- Tất cả phòng --</option>
                @foreach($phongs as $phong)
                    <option value="{{ $phong->id }}" @if($phongId==$phong->id) selected @endif>
                        {{ $phong->ten ?? $phong->ten_phong }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>
    <!-- Sinh viên đã thanh toán -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">Sinh viên Đã thanh toán</h4>
        </div>
        <div class="card-body table-responsive room-table-wrapper">
            <table class="table table-hover room-table mb-0">
                <thead class="table">
                    <tr>
                        <th class="fit text-center">STT</th>
                        <th>Phòng</th>
                        <th>Sinh viên</th>
                        <th>Slot</th>
                        <th>Ngày thanh toán</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($daThanhToan as $item)
                    <tr>
                        <td class="fit text-center">{{ $loop->iteration }}</td>
                        <td>{{ $item->hoaDon->phong->ten_phong ?? $item->hoaDon->phong->ten ?? '' }}</td>
                        <td>{{ $item->sinh_vien_ten }}</td>
                        <td>{{ $item->slot_label }}</td>
                        <td>{{ optional($item->ngay_thanh_toan)->format('d/m/Y H:i') ?? '' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-3">
                            Chưa có sinh viên nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sinh viên chưa thanh toán -->
    <div class="card mb-4">
        <div class="card-header bg-danger text-white">
            <h4 class="mb-0">Sinh viên Chưa thanh toán</h4>
        </div>
        <div class="card-body table-responsive room-table-wrapper">
            <table class="table table-hover room-table mb-0">
                <thead class="table">
                    <tr>
                        <th class="fit text-center">STT</th>
                        <th>Phòng</th>
                        <th>Sinh viên</th>
                        <th>Slot</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($chuaThanhToan as $item)
                    <tr>
                        <td class="fit text-center">{{ $loop->iteration }}</td>
                        <td>{{ $item->hoaDon->phong->ten_phong ?? $item->hoaDon->phong->ten ?? '' }}</td>
                        <td>{{ $item->sinh_vien_ten }}</td>
                        <td>{{ $item->slot_label }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3">
                            Chưa có sinh viên nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

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
