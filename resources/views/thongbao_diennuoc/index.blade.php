@extends('admin.layouts.admin')

@section('title', 'Tổng quan hóa đơn điện nước')

@section('content')
<div class="container mt-4">
    {{-- Tiêu đề --}}
    <div class="mb-4">
        <h3 class="room-page__title mb-2">📢 TỔNG QUAN HÓA ĐƠN ĐIỆN - NƯỚC</h3> 
    </div>

    {{-- Dropdown lọc phòng --}}
    <form method="GET" action="{{ route('hoadon_dien_nuoc.index') }}" class="filter-card mb-3">
    <div class="row g-3 align-items-end">
        {{-- Lọc theo khu --}}
        <div class="col-md-3">
            <label for="khu_id" class="form-label">Khu</label>
            <select name="khu_id" id="khu_id" class="form-select" onchange="this.form.submit()">
                <option value="">-- Tất cả khu --</option>
                @foreach ($khus as $khu)
                    <option value="{{ $khu->id }}" {{ request('khu_id') == $khu->id ? 'selected' : '' }}>
                        {{ $khu->ten_khu ?? $khu->ten }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Lọc theo phòng --}}
        <div class="col-md-3">
            <label for="phong_id" class="form-label">Phòng</label>
            <select name="phong_id" id="phong_id" class="form-select" onchange="this.form.submit()">
                <option value="">-- Tất cả phòng --</option>
                @foreach($phongs as $phong)
                    <option value="{{ $phong->id }}" {{ request('phong_id') == $phong->id ? 'selected' : '' }}>
                        {{ $phong->ten_phong ?? $phong->ten }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Nhóm nút --}}
        <div class="col-md-6 d-flex gap-2 justify-content-end">
            <button type="submit" class="btn btn-outline-primary d-inline-flex align-items-center px-4">
                <i class="fa fa-filter me-1"></i> Lọc
            </button>

            @if (request('khu_id') || request('phong_id'))
                <a href="{{ route('hoadon_dien_nuoc.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center px-4">
                    <i class="fa fa-times me-1"></i> Xóa lọc
                </a>
            @endif
        </div>
    </div>
</form>


    {{-- Bảng tổng quan --}}
    <div class="room-table-wrapper table-responsive">
        <table class="table table-hover room-table mb-0">
            <thead>
                <tr>
                    <th class="fit text-center">#</th>
                    <th>Phòng</th>
                    <th>Tổng tiền</th>
                    <th class="text-success">Đã thanh toán</th>
                    <th class="text-danger">Chưa thanh toán</th>
                    <th class="fit text-center">Chi tiết</th>
                </tr>
            </thead>
            <tbody>
    @forelse($data as $key => $row)
        <tr>
            {{-- Số thứ tự đúng theo trang: firstItem() + key --}}
            <td class="fit text-center">{{ $data->firstItem() + $key }}</td>

            <td>{{ $row->phong->ten_phong ?? $row->phong->ten }}</td>
            <td>{{ number_format($row->tong_tien) }}đ</td>
            <td class="text-success">{{ number_format($row->da_thanh_toan) }}</td>
            <td class="text-danger">{{ number_format($row->chua_thanh_toan) }}</td>
            <td class="fit text-center">
                <a href="{{ route('hoadon_dien_nuoc.detail', $row->phong->id) }}"
                   class="btn btn-dergin btn-dergin--info btn-sm">
                   Xem
                </a>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center text-muted py-3">Chưa có dữ liệu</td>
        </tr>
    @endforelse
</tbody>

        </table>
    </div>
     @if($data->hasPages())
        <div class="mt-3 d-flex justify-content-end">
            {{-- appends() để giữ lại query phong_id khi chuyển trang --}}
            {{ $data->appends(request()->query())->links() }}
        </div>
    @endif
</div>

@push('styles')
    <style>
        html { scroll-behavior: auto !important; }

        .room-page__title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1f2937;
        }

        .room-table-wrapper {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(15,23,42,0.06);
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
            box-shadow: 0 12px 30px rgba(15,23,42,0.08);
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
            box-shadow: 0 10px 22px rgba(78,84,200,.32);
        }

        .btn-dergin--info { background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%); }
        .btn-dergin--danger { background: linear-gradient(135deg, #f43f5e 0%, #ef4444 100%); }

        @media (max-width: 992px) {
            .room-table thead { display: none; }
            .room-table tbody { display: block; }
            .room-table tbody tr { display: flex; flex-direction: column; padding: 1rem; }
            .room-table tbody td { display: flex; justify-content: space-between; padding: .35rem 0; }
        }
    </style>
@endpush
@endsection
