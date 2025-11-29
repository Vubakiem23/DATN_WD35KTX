@extends('admin.layouts.admin')

@section('title', 'Tổng quan hóa đơn điện nước')

@section('content')
<div class="container mt-4">
    {{-- Tiêu đề --}}
    <div class="mb-4">
        <h3 class="room-page__title mb-2">📢 TỔNG QUAN HÓA ĐƠN ĐIỆN - NƯỚC</h3> 
    </div>

    {{-- Dropdown lọc phòng --}}
     <form action="{{ route('hoadon_dien_nuoc.index') }}" method="GET" class="d-flex align-items-center gap-2 mb-2">
            <label for="phong_id" class="mb-0">Lọc theo phòng:</label>
            <select name="phong_id" id="phong_id" class="form-control" onchange="this.form.submit()">
                <option value="">-- Tất cả phòng --</option>
                @foreach($phongs as $phong)
                    <option value="{{ $phong->id }}" @if(request('phong_id') == $phong->id) selected @endif>
                        {{ $phong->ten_phong ?? $phong->ten }}
                    </option>
                @endforeach
            </select>
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
                        <td class="fit text-center">{{ $key + 1 }}</td>
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
