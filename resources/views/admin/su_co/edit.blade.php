@extends('admin.layouts.admin')

@section('content')
<div class="x_panel">
    <div class="x_title">
        <h2>Cập nhật trạng thái sự cố</h2>
        <div class="clearfix"></div>
    </div>

    <div class="x_content">
        <form action="{{ route('suco.update', $suco->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Sinh viên</label>
                <input type="text" class="form-control" value="{{ $suco->sinhVien->ten ?? '---' }}" disabled>
            </div>

            <div class="form-group">
                <label>Phòng</label>
                <input type="text" class="form-control" value="{{ $suco->phong->ten_phong ?? '---' }}" disabled>
            </div>

            <div class="form-group">
                <label>Mô tả</label>
                <textarea class="form-control" rows="4" disabled>{{ $suco->mo_ta }}</textarea>
            </div>

            <div class="form-group">
                <label>Trạng thái</label>
                <select name="trang_thai" class="form-control" required>
                    <option value="Tiếp nhận" {{ $suco->trang_thai == 'Tiếp nhận' ? 'selected' : '' }}>Tiếp nhận</option>
                    <option value="Đang xử lý" {{ $suco->trang_thai == 'Đang xử lý' ? 'selected' : '' }}>Đang xử lý</option>
                    <option value="Hoàn thành" {{ $suco->trang_thai == 'Hoàn thành' ? 'selected' : '' }}>Hoàn thành</option>
                    <option value="Hủy" {{ $suco->trang_thai == 'Hủy' ? 'selected' : '' }}>Hủy</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Cập nhật</button>
            <a href="{{ route('suco.index') }}" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>
</div>
@endsection
