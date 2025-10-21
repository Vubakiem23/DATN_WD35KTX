@extends('admin.layouts.admin')

@section('content')
<div class="x_panel">
    <div class="x_title">
        <h2>Chi tiết sự cố #{{ $suco->id }}</h2>
        <div class="clearfix"></div>
    </div>

    <div class="x_content">

        {{-- Hiển thị ảnh minh chứng --}}
        <div class="text-center mb-4">
            @if(!empty($suco->anh))
                <img src="{{ asset('uploads/suco/' . $suco->anh) }}" 
                     alt="Ảnh sự cố" 
                     class="img-thumbnail" 
                     width="300" 
                     style="border-radius: 10px; object-fit: cover;">
            @else
                <img src="{{ asset('images/no-image.png') }}" 
                     alt="Không có ảnh" 
                     class="img-thumbnail" 
                     width="300" 
                     style="opacity: 0.6;">
                <p class="text-muted mt-2">Chưa có ảnh minh chứng</p>
            @endif
        </div>

        <table class="table table-bordered">
            <tr>
                <th width="25%">Sinh viên</th>
                <td>
                    @if($suco->sinhVien)
                        <strong>{{ $suco->sinhVien->ho_ten  }}</strong><br>
                        <small>MSSV: {{ $suco->sinhVien->ma_sv ?? '---' }}</small><br>
                        <small>Email: {{ $suco->sinhVien->email ?? '---' }}</small>
                    @else
                        ---
                    @endif
                </td>
            </tr>
            <tr>
                <th>Phòng</th>
                <td>{{ $suco->phong->ten_phong ?? '---' }}</td>
            </tr>
            <tr>
                <th>Mô tả sự cố</th>
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

        <div class="mt-3">
            <a href="{{ route('suco.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Quay lại
            </a>
            <a href="{{ route('suco.edit', $suco->id) }}" class="btn btn-warning">
                <i class="fa fa-edit"></i> Cập nhật
            </a>
        </div>
    </div>
</div>

{{-- CSS tùy chỉnh --}}
<style>
.table th {
    background-color: #f8f9fa;
    width: 25%;
}
.badge {
    padding: 6px 10px;
    border-radius: 12px;
    color: #fff;
    font-size: 12px;
}
.bg-secondary { background-color: #6c757d; }
.bg-info { background-color: #17a2b8; }
.bg-success { background-color: #28a745; }
.bg-danger { background-color: #dc3545; }
</style>
@endsection
