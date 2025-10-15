@extends('admin.layouts.admin')

@section('content')
<div class="x_panel">
    <div class="x_title">
        <h2>Chi tiết sự cố #{{ $suco->id }}</h2>
        <div class="clearfix"></div>
    </div>

    <div class="x_content">
        <table class="table table-bordered">
            <tr>
                <th>Sinh viên</th>
                <td>{{ $suco->sinhVien->ten ?? '---' }}</td>
            </tr>
            <tr>
                <th>Phòng</th>
                <td>{{ $suco->phong->ten_phong ?? '---' }}</td>
            </tr>
            <tr>
                <th>Mô tả</th>
                <td>{{ $suco->mo_ta }}</td>
            </tr>
            <tr>
                <th>Ngày gửi</th>
                <td>{{ \Carbon\Carbon::parse($suco->ngay_gui)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <th>Trạng thái</th>
                <td>
                    <span class="badge 
                        @if($suco->trang_thai == 'Tiếp nhận') bg-secondary
                        @elseif($suco->trang_thai == 'Đang xử lý') bg-info
                        @elseif($suco->trang_thai == 'Hoàn thành') bg-success
                        @else bg-danger
                        @endif">
                        {{ $suco->trang_thai }}
                    </span>
                </td>
            </tr>
        </table>

        <a href="{{ route('suco.index') }}" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Quay lại</a>
        <a href="{{ route('suco.edit', $suco->id) }}" class="btn btn-warning"><i class="fa fa-edit"></i> Cập nhật</a>
    </div>
</div>
@endsection
